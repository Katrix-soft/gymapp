<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago #{{ $payment->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            line-height: 1.4;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }
        .header {
            border-bottom: 2px solid {{ $brandColor }};
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
        }
        .header-right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }
        .logo {
            max-height: 50px;
            margin-bottom: 5px;
        }
        .gym-name {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 0;
        }
        .meta-label {
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .meta-value {
            color: #1f2937;
            font-weight: bold;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th {
            background-color: #f3f4f6;
            color: #374151;
            text-align: left;
            padding: 10px;
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        .details-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f3f4f6;
        }
        .total-row {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 15px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="header-left">
            @if($logoUrl)
                <img class="logo" src="{{ $logoUrl }}" alt="Logo" /><br>
            @endif
            <span class="gym-name">{{ $gymName }}</span>
        </div>
        <div class="header-right">
            <h1 class="title">RECIBO</h1>
            <span style="color: #6b7280; font-size: 12px;">Comprobante de Pago</span>
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label" style="width: 25%;">Recibo Nro:</td>
            <td class="meta-value" style="width: 25%;">#{{ $payment->id }}</td>
            <td class="meta-label" style="width: 25%; text-align: right;">Fecha:</td>
            <td class="meta-value" style="width: 25%; text-align: right;">{{ $dateFormatted }}</td>
        </tr>
        <tr>
            <td class="meta-label">Cliente:</td>
            <td class="meta-value" colspan="3">{{ $payment->user->name }} ({{ $payment->user->email }})</td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>Detalle / Concepto</th>
                <th>Método</th>
                <th>Estado</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Pago de Membresía</strong><br>
                    <span style="font-size: 12px; color: #6b7280;">
                        {{ $payment->membership->plan->name ?? 'Plan Personalizado' }} 
                        @if($payment->membership)
                            (Vence el {{ \Carbon\Carbon::parse($payment->membership->end_date)->format('d/m/Y') }})
                        @endif
                    </span>
                </td>
                <td style="font-size: 13px; color: #4b5563;">{{ $payment->payment_method ?? 'MercadoPago' }}</td>
                <td>
                    @if($payment->status === 'paid')
                        <span class="badge badge-success">Pagado</span>
                    @else
                        <span class="badge badge-pending">{{ ucfirst($payment->status) }}</span>
                    @endif
                </td>
                <td style="text-align: right; font-weight: bold;">
                    ${{ number_format($payment->amount, 2, ',', '.') }}
                </td>
            </tr>
            <tr class="total-row">
                <td colspan="3" style="text-align: right; border-bottom: none; padding-top: 20px;">Total General:</td>
                <td style="text-align: right; border-bottom: none; padding-top: 20px; color: {{ $brandColor }};">
                    ${{ number_format($payment->amount, 2, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Este documento es un comprobante válido de pago electrónico para la membresía en {{ $gymName }}.<br>
        ¡Gracias por entrenar con nosotros!
    </div>
</div>

</body>
</html>
