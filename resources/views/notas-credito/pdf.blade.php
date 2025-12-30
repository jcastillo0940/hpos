<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>NOTA DE CRÉDITO {{ $notaCredito->numero }}</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header-container { width: 100%; margin-bottom: 30px; border-bottom: 2px solid #dc2626; padding-bottom: 10px; }
        .company-logo { font-size: 24px; font-weight: bold; color: #dc2626; text-transform: uppercase; margin-bottom: 5px; }
        .company-details { font-size: 11px; color: #666; line-height: 1.6; }
        .recipient-box { margin-top: 20px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9; width: 60%; float: left; }
        .recipient-label { font-size: 10px; color: #666; text-transform: uppercase; margin-bottom: 3px; }
        .recipient-name { font-size: 14px; font-weight: bold; margin-bottom: 5px; }
        .doc-info-box { width: 35%; float: right; margin-top: 20px; text-align: right; }
        .doc-type { font-size: 16px; font-weight: bold; color: #dc2626; text-transform: uppercase; border-bottom: 2px solid #dc2626; padding-bottom: 5px; margin-bottom: 10px; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 4px 0; font-size: 11px; }
        .meta-label { font-weight: bold; color: #555; text-align: left; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 40px; clear: both; }
        .items-table th { border-bottom: 2px solid #000; border-top: 2px solid #000; padding: 8px; text-align: left; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .items-table td { padding: 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals-container { float: right; width: 40%; margin-top: 20px; }
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td { padding: 6px 0; font-size: 12px; }
        .total-final { border-top: 2px solid #000; margin-top: 5px; font-size: 14px; font-weight: bold; }
        .footer { position: fixed; bottom: 15px; left: 20px; right: 20px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="company-logo">{{ $notaCredito->empresa->nombre_comercial ?? $notaCredito->empresa->razon_social ?? 'EMPRESA' }}</div>
        <div class="company-details">
            RUC: {{ $notaCredito->empresa->ruc ?? 'N/A' }} | 
            Dirección: {{ $notaCredito->empresa->direccion ?? 'N/A' }} | 
            Teléfono: {{ $notaCredito->empresa->telefono ?? 'N/A' }}
            @if($notaCredito->empresa->email) | Email: {{ $notaCredito->empresa->email }}@endif
            <br>
            <strong>NOTA DE CRÉDITO</strong> 
        </div>
    </div>

    <div class="recipient-box">
        <div class="recipient-label">Cliente:</div>
        <div class="recipient-name">{{ $notaCredito->cliente->nombre_comercial }}</div>
        <div class="company-details">
            RUC: {{ $notaCredito->cliente->identificacion }}<br>
            Dirección: {{ $notaCredito->cliente->direccion ?? 'N/A' }}<br>
            Teléfono: {{ $notaCredito->cliente->telefono ?? 'N/A' }}
        </div>
    </div>

    <div class="doc-info-box">
        <div class="doc-type">NOTA DE CRÉDITO</div>
        <table class="meta-table">
            <tr><td class="meta-label">N° DOCUMENTO</td><td class="meta-value">{{ $notaCredito->numero }}</td></tr>
            <tr><td class="meta-label">FECHA</td><td class="meta-value">{{ $notaCredito->fecha->format('d/m/Y') }}</td></tr>
            @if($notaCredito->factura)
            <tr><td class="meta-label">FACTURA</td><td class="meta-value">{{ $notaCredito->factura->numero }}</td></tr>
            @endif
            <tr><td class="meta-label">TIPO</td><td class="meta-value">{{ $notaCredito->tipo === 'devolucion' ? 'DEVOLUCIÓN' : 'DESCUENTO' }}</td></tr>
        </table>
    </div>

    @if($notaCredito->detalles->count() > 0)
    <table class="items-table">
        <thead>
            <tr>
                <th width="8%">Cant.</th>
                <th width="50%">Ítem / Descripción</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">ITBMS</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notaCredito->detalles as $detalle)
            <tr>
                <td class="text-center">{{ number_format($detalle->cantidad, 2) }}</td>
                <td><strong>{{ $detalle->producto->nombre }}</strong></td>
                <td class="text-right">B/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-right">B/. {{ number_format($detalle->subtotal, 2) }}</td>
                <td class="text-right">B/. {{ number_format($detalle->itbms_monto, 2) }}</td>
                <td class="text-right"><strong>B/. {{ number_format($detalle->total, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <div class="totals-container">
        <table class="totals-table">
            <tr>
                <td style="font-weight: bold;">Subtotal:</td>
                <td class="text-right">B/. {{ number_format($notaCredito->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">ITBMS (7%):</td>
                <td class="text-right">B/. {{ number_format($notaCredito->itbms, 2) }}</td>
            </tr>
        </table>
        <table class="totals-table total-final">
            <tr>
                <td>TOTAL CRÉDITO:</td>
                <td class="text-right">B/. {{ number_format($notaCredito->total, 2) }}</td>
            </tr>
        </table>
    </div>
    
    <div style="clear: both; margin-top: 30px; padding: 10px; background-color: #fffbeb; border: 1px solid #fbbf24;">
        <strong>MOTIVO:</strong> {{ $notaCredito->motivo }}
        @if($notaCredito->observaciones)
        <br><strong>OBSERVACIONES:</strong> {{ $notaCredito->observaciones }}
        @endif
    </div>
    
    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i:s') }} | Sistema ERP | 
        <strong>Estado: {{ strtoupper($notaCredito->estado) }}</strong>
    </div>
</body>
</html>