<?php

namespace App\Livewire\Gym\Member;

use App\Models\User;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Plan;
use Livewire\Component;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $activeMembership = null;
    public $daysRemaining = 0;
    public $checkoutUrl = '';

    // Selected plan for renewal
    public $renewalPlanId = '';

    public function mount()
    {
        $this->loadMemberData();
    }

    public function loadMemberData()
    {
        $user = auth()->user();
        $this->activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('plan')
            ->first();

        if ($this->activeMembership) {
            $end = Carbon::parse($this->activeMembership->end_date);
            $today = Carbon::today();
            $this->daysRemaining = $end->isPast() ? 0 : $today->diffInDays($end, false);
        } else {
            $this->daysRemaining = 0;
        }

        // Set default renewal plan if plans exist
        $firstPlan = Plan::first();
        if ($firstPlan) {
            $this->renewalPlanId = $firstPlan->id;
        }
    }

    public function generateRenewalCheckout()
    {
        $this->validate([
            'renewalPlanId' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($this->renewalPlanId);
        $user = auth()->user();

        // 1. Create a pending payment log in the database
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $plan->price,
            'status' => 'pending',
            'payment_method' => 'MercadoPago',
            'external_reference' => 'MP-' . strtoupper(uniqid()),
        ]);

        $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';

        // 2. Generate real MercadoPago Preference if configured
        $accessToken = config('services.mercadopago.access_token');
        $clientId = config('services.mercadopago.client_id');
        $clientSecret = config('services.mercadopago.client_secret');

        if (!empty($accessToken) || (!empty($clientId) && !empty($clientSecret))) {
            try {
                if (!empty($accessToken)) {
                    \MercadoPago\SDK::setAccessToken($accessToken);
                } else {
                    \MercadoPago\SDK::setClientId($clientId);
                    \MercadoPago\SDK::setClientSecret($clientSecret);
                }

                $preference = new \MercadoPago\Preference();

                // Item
                $item = new \MercadoPago\Item();
                $item->title = "Membresía " . $plan->name . " - " . (tenant('name') ?: 'GymApp');
                $item->quantity = 1;
                $item->unit_price = (float)$plan->price;
                $item->currency_id = "ARS";
                $preference->items = [$item];

                // Payer
                $payer = new \MercadoPago\Payer();
                $payer->email = $user->email;
                $payer->name = $user->name;
                $preference->payer = $payer;

                // Return URLs
                $successUrl = url($prefix . '/member/payment/success') . "?payment_id=" . $payment->id;
                $failureUrl = url($prefix . '/member/payment/failure') . "?payment_id=" . $payment->id;
                $pendingUrl = url($prefix . '/member/payment/pending') . "?payment_id=" . $payment->id;

                $preference->back_urls = [
                    "success" => $successUrl,
                    "failure" => $failureUrl,
                    "pending" => $pendingUrl,
                ];

                $preference->auto_return = "approved";
                $preference->external_reference = (string)$payment->id;

                // Webhook Notification URL
                $preference->notification_url = url($prefix . '/webhook/mercadopago');

                $preference->save();

                $checkoutUrl = (config('app.env') === 'production') ? $preference->init_point : $preference->sandbox_init_point;

                if (!empty($checkoutUrl)) {
                    $payment->update(['external_reference' => 'PREF-' . $preference->id]);
                    return redirect($checkoutUrl);
                }
            } catch (\Exception $e) {
                logger()->error('Error al generar preferencia de MercadoPago: ' . $e->getMessage());
            }
        }

        // Fallback to simulated payment screen if not configured or failed
        $this->checkoutUrl = $prefix . "/member/checkout-simulation?payment_id={$payment->id}&plan_id={$plan->id}";
        return redirect($this->checkoutUrl);
    }

    public function render()
    {
        $user = auth()->user();

        // Fetch upcoming bookings
        $upcomingBookings = $user->classBookings()
            ->where('date', '>=', now()->toDateString())
            ->with('gymClass')
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        // Fetch active routine
        $activeRoutine = $user->workoutLogs()
            ->orderBy('completed_at', 'desc')
            ->first();

        // Fetch physical measurements
        $measurements = $user->bodyMeasurements()
            ->orderBy('logged_at', 'asc')
            ->get();

        $plans = Plan::all();

        return view('livewire.gym.member.dashboard', [
            'upcomingBookings' => $upcomingBookings,
            'activeRoutine' => $activeRoutine,
            'measurements' => $measurements,
            'plans' => $plans,
        ])->layout('layouts.tenant-app');
    }
}
