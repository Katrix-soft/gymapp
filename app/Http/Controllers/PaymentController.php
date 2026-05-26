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

        // Security: verify the payment belongs to the authenticated user
        if (auth()->id() !== $payment->user_id) {
            abort(403, 'No autorizado: este pago no te pertenece.');
        }

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

            // Security: verify the payment belongs to the authenticated user
            if ($payment && auth()->id() !== $payment->user_id) {
                abort(403, 'No autorizado.');
            }

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

            // Security: verify the payment belongs to the authenticated user
            if ($payment && auth()->id() !== $payment->user_id) {
                abort(403, 'No autorizado.');
            }

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
     * Validates HMAC signature when MERCADOPAGO_WEBHOOK_SECRET is configured.
     */
    public function webhook(Request $request)
    {
        Log::info('MercadoPago Webhook recibido', $request->all());

        // Validate webhook signature if secret is configured
        $webhookSecret = config('services.mercadopago.webhook_secret');
        if (!empty($webhookSecret)) {
            $signature = $request->header('x-signature');
            $requestId = $request->header('x-request-id');

            if ($signature && $requestId) {
                // Parse signature parts
                $parts = [];
                foreach (explode(',', $signature) as $part) {
                    $kv = explode('=', trim($part), 2);
                    if (count($kv) === 2) {
                        $parts[$kv[0]] = $kv[1];
                    }
                }

                $ts = $parts['ts'] ?? '';
                $v1 = $parts['v1'] ?? '';

                // Build the manifest string
                $dataId = $request->query('data.id', $request->query('id', ''));
                $manifest = "id:{$dataId};request-id:{$requestId};ts:{$ts};";
                $computed = hash_hmac('sha256', $manifest, $webhookSecret);

                if (!hash_equals($computed, $v1)) {
                    Log::warning('MercadoPago Webhook: firma inválida', [
                        'expected' => $computed,
                        'received' => $v1,
                    ]);
                    return response()->json(['error' => 'Invalid signature'], 403);
                }
            }
        }

        $topic = $request->query('topic') ?: $request->input('type');
        $id = $request->query('id') ?: $request->input('data.id') ?: $request->input('resource');

        // Create log record
        $webhook = \App\Models\MercadoPagoWebhook::create([
            'webhook_id' => $id,
            'topic' => $topic,
            'resource' => $request->input('resource'),
            'payload' => json_encode($request->all()),
            'status' => 'received',
        ]);

        if ($topic === 'payment' && $id) {
            // Initialize SDK
            $accessToken = config('services.mercadopago.access_token');
            if (!empty($accessToken)) {
                try {
                    \MercadoPago\MercadoPagoConfig::setAccessToken($accessToken);
                    $client = new \MercadoPago\Client\Payment\PaymentClient();

                    // Fetch payment status from MercadoPago
                    $mpPayment = $client->get($id);

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
                
                $webhook->update(['status' => 'processed']);

            } catch (\Throwable $e) {
                Log::error('Error procesando Webhook de MercadoPago: ' . $e->getMessage());
                $webhook->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }
        } else {
            // Other topics processed directly as processed received
            $webhook->update(['status' => 'processed']);
        }

        return response()->json(['status' => 'ok']);
    }
}
