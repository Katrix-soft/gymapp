<?php

namespace App\Livewire\Gym\Member;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Membership;
use App\Models\User;
use Livewire\Component;
use Carbon\Carbon;

class CheckoutSimulation extends Component
{
    public $paymentId;
    public $planId;

    public $payment;
    public $plan;

    public $simulationStatus = null; // 'success', 'pending', 'failure'

    public function mount()
    {
        $this->paymentId = request()->query('payment_id');
        $this->planId = request()->query('plan_id');

        $this->payment = Payment::findOrFail($this->paymentId);
        $this->plan = Plan::findOrFail($this->planId);
    }

    public function processSimulation($status)
    {
        $user = auth()->user();

        if ($status === 'success') {
            // Update payment record
            $this->payment->update([
                'status' => 'paid',
                'external_reference' => 'Simulated-MP-' . strtoupper(uniqid()),
            ]);

            // Deactivate existing active memberships to prevent double entries
            Membership::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);

            // Calculate dates
            $start = Carbon::today();
            $end = $start->copy()->addMonths($this->plan->duration_months);

            // Create membership
            $membership = Membership::create([
                'user_id' => $user->id,
                'plan_id' => $this->plan->id,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'status' => 'active',
            ]);

            // Attach membership to payment
            $this->payment->update(['membership_id' => $membership->id]);

            // Ensure member status is active
            $user->update(['status' => 'active']);

            $this->simulationStatus = 'success';
            session()->flash('message', 'Membresía activada exitosamente.');
        } elseif ($status === 'pending') {
            $this->payment->update(['status' => 'pending']);
            $this->simulationStatus = 'pending';
        } else {
            $this->payment->update(['status' => 'rejected']);
            $this->simulationStatus = 'failure';
        }
    }

    public function goBack()
    {
        $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
        return redirect($prefix . '/member/dashboard');
    }

    public function render()
    {
        return view('livewire.gym.member.checkout-simulation')
            ->layout('layouts.tenant-app');
    }
}
