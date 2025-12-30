<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estado de Cuenta - {{ $cliente->nombre_comercial }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header-row {
            display: table;
            width: 100%;
        }
        
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 9px;
            color: #666;
            line-height: 1.6;
        }
        
        .document-title {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .document-number {
            font-size: 12px;
            color: #666;
        }
        
        /* Cliente Info */
        .client-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 12px;
            margin-bottom: 15px;
        }
        
        .client-title {
            font-size: 10px;
            font-weight: bold;
            color: #2563eb;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        
        .client-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }
        
        .client-col {
            display: table-cell;
            width: 50%;
            padding: 2px 0;
        }
        
        .label {
            font-weight: bold;
            color: #475569;
        }
        
        /* Período Info */
        .period-section {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .period-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e40af;
        }
        
        /* Saldo Anterior */
        .saldo-anterior {
            background: #f1f5f9;
            border-left: 4px solid #64748b;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .saldo-anterior-row {
            display: table;
            width: 100%;
        }
        
        .saldo-anterior-label {
            display: table-cell;
            font-weight: bold;
            color: #475569;
        }
        
        .saldo-anterior-valor {
            display: table-cell;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            color: #64748b;
        }
        
        /* Tabla de Movimientos */
        .movements-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .movements-table thead {
            background: #2563eb;
            color: white;
        }
        
        .movements-table th {
            padding: 8px 6px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .movements-table th.text-right {
            text-align: right;
        }
        
        .movements-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }
        
        .movements-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .movements-table td {
            padding: 6px;
            font-size: 10px;
        }
        
        .movements-table td.text-right {
            text-align: right;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-factura {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .badge-cobro {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-nc {
            background: #dbeafe;
            color: #1e40af;
        }
        
        /* Totales */
        .totals-table {
            width: 100%;
            margin-top: 10px;
        }
        
        .totals-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .totals-label {
            display: table-cell;
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
            color: #475569;
        }
        
        .totals-value {
            display: table-cell;
            text-align: right;
            width: 120px;
            font-weight: bold;
        }
        
        .totals-row.cargo .totals-value {
            color: #dc2626;
        }
        
        .totals-row.abono .totals-value {
            color: #059669;
        }
        
        .totals-row.final {
            border-top: 2px solid #2563eb;
            padding-top: 8px;
            margin-top: 8px;
        }
        
        .totals-row.final .totals-label {
            font-size: 12px;
            color: #1e40af;
        }
        
        .totals-row.final .totals-value {
            font-size: 16px;
            color: #2563eb;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #64748b;
            text-align: center;
        }
        
        /* Page break */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-row">
                <div class="header-left">
                    <div class="company-name">{{ $cliente->empresa->nombre ?? 'MI EMPRESA' }}</div>
                    <div class="company-info">
                        @if($cliente->empresa)
                        RUC: {{ $cliente->empresa->ruc }}<br>
                        {{ $cliente->empresa->direccion }}<br>
                        Tel: {{ $cliente->empresa->telefono }} | Email: {{ $cliente->empresa->email }}
                        @endif
                    </div>
                </div>
                <div class="header-right">
                    <div class="document-title">ESTADO DE CUENTA</div>
                    <div class="document-number">{{ $nombrePeriodo }}</div>
                </div>
            </div>
        </div>

        <!-- Cliente Info -->
        <div class="client-section">
            <div class="client-title">Información del Cliente</div>
            <div class="client-row">
                <div class="client-col">
                    <span class="label">Cliente:</span> {{ $cliente->nombre_comercial }}
                </div>
                <div class="client-col">
                    <span class="label">RUC:</span> {{ $cliente->identificacion }}
                </div>
            </div>
            <div class="client-row">
                <div class="client-col">
                    <span class="label">Dirección:</span> {{ $cliente->direccion ?? 'N/A' }}
                </div>
                <div class="client-col">
                    <span class="label">Teléfono:</span> {{ $cliente->telefono ?? 'N/A' }}
                </div>
            </div>
            <div class="client-row">
                <div class="client-col">
                    <span class="label">Límite de Crédito:</span> B/. {{ number_format($cliente->limite_credito, 2) }}
                </div>
                <div class="client-col">
                    <span class="label">Días de Crédito:</span> {{ $cliente->dias_credito }} días
                </div>
            </div>
        </div>

        <!-- Período -->
        <div class="period-section">
            <div class="period-title">{{ $nombrePeriodo }}</div>
        </div>

        <!-- Saldo Anterior -->
        <div class="saldo-anterior">
            <div class="saldo-anterior-row">
                <div class="saldo-anterior-label">Saldo Anterior ({{ $mesAnteriorNombre }} {{ $añoAnterior }}):</div>
                <div class="saldo-anterior-valor">B/. {{ number_format($saldoAnterior, 2) }}</div>
            </div>
        </div>

        <!-- Tabla de Movimientos -->
        <table class="movements-table">
            <thead>
                <tr>
                    <th style="width: 10%">Fecha</th>
                    <th style="width: 10%">Tipo</th>
                    <th style="width: 15%">Documento</th>
                    <th style="width: 30%">Descripción</th>
                    <th class="text-right" style="width: 12%">Cargos</th>
                    <th class="text-right" style="width: 12%">Abonos</th>
                    <th class="text-right" style="width: 11%">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @php $saldoAcumulado = $saldoAnterior; @endphp
                
                @forelse($movimientos as $mov)
                @php
                    $saldoAcumulado += $mov->cargo - $mov->abono;
                @endphp
                <tr>
                    <td>{{ $mov->fecha->format('d/m/Y') }}</td>
                    <td>
                        @if($mov->tipo === 'factura')
                        <span class="badge badge-factura">Factura</span>
                        @elseif($mov->tipo === 'cobro')
                        <span class="badge badge-cobro">Cobro</span>
                        @else
                        <span class="badge badge-nc">N/C</span>
                        @endif
                    </td>
                    <td>{{ $mov->numero }}</td>
                    <td>{{ $mov->descripcion }}</td>
                    <td class="text-right">{{ $mov->cargo > 0 ? 'B/. ' . number_format($mov->cargo, 2) : '-' }}</td>
                    <td class="text-right">{{ $mov->abono > 0 ? 'B/. ' . number_format($mov->abono, 2) : '-' }}</td>
                    <td class="text-right"><strong>B/. {{ number_format($saldoAcumulado, 2) }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #94a3b8;">
                        No hay movimientos en este período
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Totales -->
        <div class="totals-table">
            <div class="totals-row cargo">
                <div class="totals-label">TOTAL CARGOS:</div>
                <div class="totals-value">B/. {{ number_format($totalCargos, 2) }}</div>
            </div>
            <div class="totals-row abono">
                <div class="totals-label">TOTAL ABONOS:</div>
                <div class="totals-value">B/. {{ number_format($totalAbonos, 2) }}</div>
            </div>
            <div class="totals-row final">
                <div class="totals-label">SALDO FINAL:</div>
                <div class="totals-value">B/. {{ number_format($saldoFinal, 2) }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Documento generado el {{ now()->format('d/m/Y H:i:s') }}<br>
            Este es un documento informativo y no constituye un comprobante fiscal
        </div>
    </div>
</body>
</html>