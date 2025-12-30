<html>
<head>
    <meta charset="UTF-8">
    <title>FACTURA DE VENTA {{ $factura->numero }}</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        
        /* HEADER */
        .header-container { width: 100%; margin-bottom: 30px; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .company-logo { font-size: 24px; font-weight: bold; color: #3b82f6; text-transform: uppercase; margin-bottom: 5px; }
        .company-details { font-size: 11px; color: #666; line-height: 1.6; }
        
        /* CLIENTE / PROVEEDOR BOX */
        .recipient-box { margin-top: 20px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9; width: 60%; float: left; }
        .recipient-label { font-size: 10px; color: #666; text-transform: uppercase; margin-bottom: 3px; }
        .recipient-name { font-size: 14px; font-weight: bold; margin-bottom: 5px; }
        
        .doc-info-box { width: 35%; float: right; margin-top: 20px; text-align: right; }
        .doc-type { font-size: 16px; font-weight: bold; color: #000; text-transform: uppercase; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 10px; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 4px 0; font-size: 11px; }
        .meta-label { font-weight: bold; color: #555; text-align: left; }

        /* TABLA ITEMS */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 40px; clear: both; }
        .items-table th { border-bottom: 2px solid #000; border-top: 2px solid #000; padding: 8px; text-align: left; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .items-table td { padding: 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* TOTALES */
        .totals-container { float: right; width: 40%; margin-top: 20px; }
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td { padding: 6px 0; font-size: 12px; }
        .total-final { border-top: 2px solid #000; margin-top: 5px; font-size: 14px; font-weight: bold; }
        
        /* MONTO EN LETRAS */
        .amount-words { 
            clear: both; 
            margin-top: 30px; 
            padding: 10px; 
            background-color: #f0f0f0; 
            border-left: 4px solid #3b82f6; 
            font-size: 11px; 
            font-style: italic;
        }

        /* FIRMAS */
        .signature-area { width: 45%; float: left; text-align: center; margin-top: 50px; }
        .signature-line { border-top: 1px solid #000; margin-top: 60px; padding-top: 5px; }
        
        /* FOOTER */
        .footer { 
            position: fixed; 
            bottom: 15px; 
            left: 20px; 
            right: 20px; 
            text-align: center; 
            font-size: 9px; 
            color: #999; 
            border-top: 1px solid #ddd; 
            padding-top: 5px;
        }
        
        /* ELECTRONIC INVOICE */
        .electronic-box { 
            margin-top: 20px; 
            padding: 10px; 
            border: 1px solid #ddd; 
            background-color: #f9f9f9; 
            font-size: 9px;
            clear: both;
        }
        .qr-code { float: left; width: 80px; height: 80px; margin-right: 15px; }
        .electronic-details { line-height: 1.6; }
        
        /* ESTADO */
        .status-badge { 
            display: inline-block; 
            padding: 4px 10px; 
            border-radius: 3px; 
            font-size: 10px; 
            font-weight: bold; 
            text-transform: uppercase;
        }
        .status-pendiente { background-color: #fef3c7; color: #92400e; }
        .status-pagada { background-color: #d1fae5; color: #065f46; }
        .status-anulada { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="company-logo">{{ $factura->empresa->nombre_comercial ?? $factura->empresa->razon_social ?? 'ERP SISTEMA' }}</div>
        <div class="company-details">
            RUC: {{ $factura->empresa->ruc ?? 'N/A' }} | 
            Dirección: {{ $factura->empresa->direccion ?? 'N/A' }} | 
            Teléfono: {{ $factura->empresa->telefono ?? 'N/A' }}
            @if($factura->empresa->email) | Email: {{ $factura->empresa->email }}@endif
            <br>
            <strong>FACTURA DE VENTA</strong> 
            <span class="status-badge status-{{ $factura->estado }}">{{ strtoupper($factura->estado) }}</span>
        </div>
    </div>

    <div class="recipient-box">
        <div class="recipient-label">Cliente:</div>
        <div class="recipient-name">{{ $factura->cliente->nombre_comercial }}</div>
        <div class="company-details">
            RUC: {{ $factura->cliente->identificacion }}<br>
            Dirección: {{ $factura->clienteSucursal->direccion ?? $factura->cliente->direccion ?? 'N/A' }}
            @if($factura->clienteSucursal)
            <br>Sucursal: {{ $factura->clienteSucursal->nombre }}
            @endif
            <br>Teléfono: {{ $factura->cliente->telefono ?? 'N/A' }}
        </div>
    </div>

    <div class="doc-info-box">
        <div class="doc-type">FACTURA DE VENTA</div>
        <table class="meta-table">
            <tr><td class="meta-label">N° DOCUMENTO</td><td class="meta-value">{{ $factura->numero }}</td></tr>
            <tr><td class="meta-label">FECHA EMISIÓN</td><td class="meta-value">{{ $factura->fecha->format('d/m/Y') }}</td></tr>
            <tr><td class="meta-label">FECHA VENC.</td><td class="meta-value">{{ $factura->fecha_vencimiento->format('d/m/Y') }}</td></tr>
            <tr><td class="meta-label">CONDICIÓN</td><td class="meta-value">{{ $factura->tipo_pago == 'credito' ? 'CRÉDITO' : 'CONTADO' }}</td></tr>
            <tr><td class="meta-label">VENDEDOR</td><td class="meta-value">{{ $factura->vendedor->name ?? 'N/A' }}</td></tr>
        </table>
    </div>

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
            @foreach($factura->detalles as $detalle)
            <tr>
                <td class="text-center">{{ number_format($detalle->cantidad, 2) }}</td>
                <td>
                    <strong>{{ $detalle->producto->nombre }}</strong>
                    @if($detalle->producto->codigo)
                    <br><small style="color: #666;">Código: {{ $detalle->producto->codigo }}</small>
                    @endif
                </td>
                <td class="text-right">B/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-right">B/. {{ number_format($detalle->subtotal, 2) }}</td>
                <td class="text-right">B/. {{ number_format($detalle->itbms_monto, 2) }}</td>
                <td class="text-right"><strong>B/. {{ number_format($detalle->total, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="totals-container">
        <table class="totals-table">
            <tr>
                <td style="font-weight: bold;">Subtotal:</td>
                <td class="text-right">B/. {{ number_format($factura->subtotal, 2) }}</td>
            </tr>
            @if($factura->descuento > 0)
            <tr>
                <td style="font-weight: bold;">Descuento:</td>
                <td class="text-right">B/. {{ number_format($factura->descuento, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td style="font-weight: bold;">ITBMS (7%):</td>
                <td class="text-right">B/. {{ number_format($factura->itbms, 2) }}</td>
            </tr>
        </table>
        <table class="totals-table total-final">
            <tr>
                <td>TOTAL A PAGAR:</td>
                <td class="text-right">B/. {{ number_format($factura->total, 2) }}</td>
            </tr>
        </table>
    </div>
    
    @php
    function numeroALetras($n) {
        $n = floor($n); if ($n == 0) return 'cero';
        $u = ['', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'];
        $e = [10=>'diez',11=>'once',12=>'doce',13=>'trece',14=>'catorce',15=>'quince',16=>'dieciséis',17=>'diecisiete',18=>'dieciocho',19=>'diecinueve',20=>'veinte',21=>'veintiuno',22=>'veintidós',23=>'veintitrés',24=>'veinticuatro',25=>'veinticinco',26=>'veintiséis',27=>'veintisiete',28=>'veintiocho',29=>'veintinueve'];
        $d = ['','diez','veinte','treinta','cuarenta','cincuenta','sesenta','setenta','ochenta','noventa'];
        $c = ['','ciento','doscientos','trescientos','cuatrocientos','quinientos','seiscientos','setecientos','ochocientos','novecientos'];
        if ($n < 10) return $u[$n]; if ($n < 30) return $e[$n] ?? $d[floor($n/10)] . ' y ' . $u[$n%10];
        if ($n < 100) { $r = $n % 10; return $d[floor($n/10)] . ($r > 0 ? ' y ' . $u[$r] : ''); }
        if ($n == 100) return 'cien';
        if ($n < 1000) { $ce = floor($n/100); $r = $n%100; return $c[$ce] . ($r > 0 ? ' ' . numeroALetras($r) : ''); }
        if ($n < 1000000) { $m = floor($n/1000); $r = $n%1000; return ($m == 1 ? 'mil' : numeroALetras($m) . ' mil') . ($r > 0 ? ' ' . numeroALetras($r) : ''); }
        return 'número grande';
    }
    $centavos = str_pad(round(($factura->total - floor($factura->total)) * 100), 2, '0', STR_PAD_LEFT);
    @endphp
    
    <div class="amount-words">
        <strong>SON:</strong> {{ strtoupper(numeroALetras($factura->total)) }} BALBOAS CON {{ $centavos }}/100
    </div>
    
    @if($factura->observaciones)
    <div style="margin-top: 15px; padding: 10px; border: 1px solid #ddd; background-color: #fffbeb; font-size: 10px;">
        <strong>Observaciones:</strong> {{ $factura->observaciones }}
    </div>
    @endif
    
    <!-- FACTURA ELECTRÓNICA -->
    @if($factura->cufe)
    <div class="electronic-box">
        @if($factura->qr_code && file_exists(public_path('storage/' . $factura->qr_code)))
        <img src="{{ public_path('storage/' . $factura->qr_code) }}" class="qr-code">
        @else
        <div class="qr-code" style="border: 1px solid #ddd; background: #f5f5f5; text-align: center; line-height: 80px; font-size: 9px;">QR CODE</div>
        @endif
        <div class="electronic-details">
            <strong>FACTURA ELECTRÓNICA - REPÚBLICA DE PANAMÁ</strong><br>
            <strong>CUFE:</strong> {{ $factura->cufe }}<br>
            <strong>Fecha Generación:</strong> {{ $factura->fecha->format('d/m/Y H:i:s') }}<br>
            <strong>Estado DGI:</strong> {{ strtoupper($factura->estado_dgi ?? 'PENDIENTE ENVÍO') }}<br>
            @if($factura->xml_path)
            <strong>Archivo XML:</strong> Disponible
            @endif
        </div>
        <div style="clear: both;"></div>
    </div>
    @endif
    
    <div style="clear: both; margin-top: 50px;">
        <div class="signature-area" style="float: left;">
            <div class="signature-line">
                <strong>{{ strtoupper($factura->vendedor->name ?? 'SISTEMA') }}</strong><br>
                <span style="font-size: 10px; color: #555;">ELABORADO POR</span>
            </div>
        </div>
        <div class="signature-area" style="float: right;">
            <div class="signature-line">
                <strong>{{ strtoupper($factura->cliente->nombre_comercial) }}</strong><br>
                <span style="font-size: 10px; color: #555;">RECIBIDO CONFORME</span>
            </div>
        </div>
    </div>
    
    <div class="footer">
        Documento generado electrónicamente el {{ now()->format('d/m/Y H:i:s') }} | Sistema ERP | 
        @if($factura->saldo_pendiente > 0)
            <strong style="color: #dc2626;">Saldo Pendiente: B/. {{ number_format($factura->saldo_pendiente, 2) }}</strong>
        @else
            <strong style="color: #059669;">PAGADA</strong>
        @endif
    </div>
    
    @if($factura->estado == 'anulada')
    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 100px; font-weight: bold; color: rgba(220, 38, 38, 0.1); z-index: -1;">ANULADA</div>
    @endif
</body>
</html>