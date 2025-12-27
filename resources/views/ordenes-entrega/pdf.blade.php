<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Entrega {{ $ordenEntrega->numero }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
        }
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #2563eb;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .total-label {
            font-weight: bold;
        }
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $ordenEntrega->empresa->razon_social }}</div>
        <div>RUC: {{ $ordenEntrega->empresa->ruc }}</div>
        <div>{{ $ordenEntrega->empresa->direccion }}</div>
        <div>Tel: {{ $ordenEntrega->empresa->telefono }}</div>
        <div class="document-title">ORDEN DE ENTREGA</div>
        <div>{{ $ordenEntrega->numero }}</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Fecha:</span>
            <span>{{ $ordenEntrega->fecha->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Cliente:</span>
            <span>{{ $ordenEntrega->cliente->nombre_comercial }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">RUC/Cédula:</span>
            <span>{{ $ordenEntrega->cliente->identificacion }}</span>
        </div>
        @if($ordenEntrega->clienteSucursal)
        <div class="info-row">
            <span class="info-label">Sucursal:</span>
            <span>{{ $ordenEntrega->clienteSucursal->nombre }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dirección Entrega:</span>
            <span>{{ $ordenEntrega->clienteSucursal->direccion }}</span>
        </div>
        @else
        <div class="info-row">
            <span class="info-label">Dirección:</span>
            <span>{{ $ordenEntrega->cliente->direccion }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Vendedor:</span>
            <span>{{ $ordenEntrega->vendedor->name }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Cantidad</th>
                <th style="width: 50%;">Descripción</th>
                <th style="width: 15%;" class="text-right">Precio Unit.</th>
                <th style="width: 10%;" class="text-right">ITBMS</th>
                <th style="width: 15%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordenEntrega->detalles as $detalle)
            <tr>
                <td>{{ $detalle->cantidad }}</td>
                <td>{{ $detalle->producto->nombre }}</td>
                <td class="text-right">B/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-right">B/. {{ number_format($detalle->itbms_monto, 2) }}</td>
                <td class="text-right">B/. {{ number_format($detalle->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span>B/. {{ number_format($ordenEntrega->subtotal, 2) }}</span>
        </div>
        <div class="total-row">
            <span class="total-label">ITBMS (7%):</span>
            <span>B/. {{ number_format($ordenEntrega->itbms, 2) }}</span>
        </div>
        <div class="total-row grand-total">
            <span class="total-label">TOTAL:</span>
            <span>B/. {{ number_format($ordenEntrega->total, 2) }}</span>
        </div>
    </div>

    <div style="clear: both; margin-top: 100px;">
        @if($ordenEntrega->observaciones)
        <div style="margin-top: 20px;">
            <strong>Observaciones:</strong>
            <p>{{ $ordenEntrega->observaciones }}</p>
        </div>
        @endif

        <div style="margin-top: 50px;">
            <div style="display: inline-block; width: 45%; text-align: center; border-top: 1px solid #333;">
                Firma Vendedor
            </div>
            <div style="display: inline-block; width: 45%; margin-left: 9%; text-align: center; border-top: 1px solid #333;">
                Firma Cliente
            </div>
        </div>
    </div>

    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
