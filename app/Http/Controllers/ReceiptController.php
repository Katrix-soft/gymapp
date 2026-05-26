<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    /**
     * Generate and download PDF receipt for a payment.
     */
    public function download($paymentId)
    {
        $payment = Payment::with(['user', 'membership.plan'])->findOrFail($paymentId);
        $currentUser = auth()->user();

        // Security check: must be owner of the payment, or a gym admin
        if ($currentUser->id !== $payment->user_id && !$currentUser->hasRole('gym_admin')) {
            abort(403, 'No tienes permiso para ver este recibo.');
        }

        $gymName = \App\Models\TenantConfig::get('gym_name', 'SaaS Gym');
        $logoUrl = \App\Models\TenantConfig::get('logo_url', '');
        $brandColor = \App\Models\TenantConfig::get('brand_color', '#f97316');

        $data = [
            'payment' => $payment,
            'gymName' => $gymName,
            'logoUrl' => $logoUrl,
            'brandColor' => $brandColor,
            'dateFormatted' => $payment->created_at->format('d/m/Y H:i'),
        ];

        // Load the view and render PDF
        $pdf = Pdf::loadView('receipts.pdf', $data);

        return $pdf->download("recibo-{$payment->id}.pdf");
    }
}
