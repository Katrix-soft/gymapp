<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Handle success payment callback
     */
    public function success(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $payment = Payment::findOrFail($paymentId);
        $plan = Plan::findOrFail($payment->membership_id ?? Plan::first()->id); // Fallback plan just in case

        if ($payment->status !== 'paid') {
            $user = User::findOrFail($payment->user_id);
            $plan = Plan::where('price', $payment->amount)->first() ?: Plan::first();

            // Deactivate active memberships
            Membership::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);

            // Calculate dates
            $start = Carbon::today();
            $end = $start->copy()->addMonths($plan->duration_months);

            // Create membership
            $membership = Membership::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'status' => 'active',
            ]);

            // Update payment record
            $payment->update([
                'status' => 'paid',
                'membership_id' => $membership->id,
                'external_reference' => $request->query('payment_id') . '-' . $request->query('preference_id'),
            ]);

            // Activate user status
            $user->update(['status' => 'active']);
        }

        session()->flash('message', '¡Membresía activada exitosamente!');
        $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
        return redirect($prefix . '/member/dashboard');
    }

    /**
     * Handle pending payment callback
     */
    public function pending(Request $request)
    {
        $paymentId = $request->query('payment_id');
        if ($paymentId) {
            $payment = Payment::find($paymentId);
            if ($payment && $payment->status !== 'paid') {
                $payment->update(['status' => 'pending']);
            }
        }

        session()->flash('message', 'Tu pago está pendiente de aprobación.');
        $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
        return redirect($prefix . '/member/dashboard');
    }

    /**
     * Handle failure payment callback
     */
    public function failure(Request $request)
    {
        $paymentId = $request->query('payment_id');
        if ($paymentId) {
            $payment = Payment::find($paymentId);
            if ($payment && $payment->status !== 'paid') {
                $payment->update(['status' => 'rejected']);
            }
        }

        session()->flash('error', 'El pago fue rechazado o cancelado. Intenta de nuevo.');
        $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
        return redirect($prefix . '/member/dashboard');
    }

    /**
     * MercadoPago Webhook (IPN / Notifications)
     */
    public function webhook(Request $request)
    {
        Log::info('MercadoPago Webhook recibido', $request->all());

        $topic = $request->query('topic') ?: $request->input('type');
        $id = $request->query('id') ?: $request->input('data.id');

        if ($topic === 'payment' && $id) {
            // Initialize SDK
            if (!empty(config('services.mercadopago.access_token'))) {
                \MercadoPago\SDK::setAccessToken(config('services.mercadopago.access_token'));
            } else {
                \MercadoPago\SDK::setClientId(config('services.mercadopago.client_id'));
                \MercadoPago\SDK::setClientSecret(config('services.mercadopago.client_secret'));
            }

            try {
                // Fetch payment status from MercadoPago
                $mpPayment = \MercadoPago\Payment::find_by_id($id);

                if ($mpPayment && $mpPayment->status === 'approved') {
                    $paymentId = $mpPayment->external_reference;
                    $payment = Payment::find($paymentId);

                    if ($payment && $payment->status !== 'paid') {
                        $user = User::findOrFail($payment->user_id);
                        $plan = Plan::where('price', $payment->amount)->first() ?: Plan::first();

                        Membership::where('user_id', $user->id)
                            ->where('status', 'active')
                            ->update(['status' => 'expired']);

                        $start = Carbon::today();
                        $end = $start->copy()->addMonths($plan->duration_months);

                        $membership = Membership::create([
                            'user_id' => $user->id,
                            'plan_id' => $plan->id,
                            'start_date' => $start->toDateString(),
                            'end_date' => $end->toDateString(),
                            'status' => 'active',
                        ]);

                        $payment->update([
                            'status' => 'paid',
                            'membership_id' => $membership->id,
                            'external_reference' => 'webhook-' . $id,
                        ]);

                        $user->update(['status' => 'active']);

                        Log::info("Pago {$paymentId} aprobado y procesado vía Webhook.");
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error procesando Webhook de MercadoPago: ' . $e->getMessage());
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
