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

        // 2. Try generating MercadoPago Checkout URL (simulated / real sandbox integration)
        $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
        $successUrl = route('gym.member.payment.success', ['payment_id' => $payment->id]);
        $pendingUrl = route('gym.member.payment.pending', ['payment_id' => $payment->id]);
        $failureUrl = route('gym.member.payment.failure', ['payment_id' => $payment->id]);

        // We provide a fully interactive mock payment page to simulate checkout success/failure
        $this->checkoutUrl = $prefix . "/member/checkout-simulation?payment_id={$payment->id}&plan_id={$plan->id}";

        // Redirect directly to checkout
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
