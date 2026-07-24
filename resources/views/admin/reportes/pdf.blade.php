<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas - Café Aurora</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4d3b33;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #4d3b33;
            margin: 0 0 5px 0;
        }
        .header p {
            margin: 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            color: #4d3b33;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary {
            float: right;
            width: 300px;
        }
        .summary table {
            border: none;
        }
        .summary table td {
            border: none;
            padding: 4px 8px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #999;
            clear: both;
        }
        @media print {
            body { margin: 0; padding: 15px; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>Café Aurora - Reporte de Ventas</h1>
        <p>
            Filtro: {{ ucfirst(str_replace('_', ' ', $filtro)) }} 
            @if($fechaInicio)
                | Desde: {{ $fechaInicio->format('d/m/Y') }} Hasta: {{ $fechaFin->format('d/m/Y') }}
            @endif
        </p>
        <p>Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <button onclick="window.print()" style="margin-bottom: 20px; padding: 8px 15px; cursor: pointer; background-color: #4d3b33; color: #fff; border: none; border-radius: 4px;">Imprimir PDF</button>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Productos</th>
                <th class="text-center">Cant.</th>
                <th>Estado</th>
                <th>Método</th>
                <th class="text-right">Subtotal</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $granTotal = 0; $granSubtotal = 0; @endphp
            @forelse($pedidos as $pedido)
                @php 
                    $subtotal = $pedido->total / 1.16;
                    $granSubtotal += $subtotal;
                    $granTotal += $pedido->total;
                @endphp
                <tr>
                    <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $pedido->user->nombre_completo ?? 'Usuario Eliminado' }}</td>
                    <td>
                        @foreach($pedido->detalles as $detalle)
                            {{ $detalle->producto->nombre }} (x{{ $detalle->cantidad }})<br>
                        @endforeach
                    </td>
                    <td class="text-center">{{ $pedido->detalles->sum('cantidad') }}</td>
                    <td>{{ ucfirst($pedido->estado) }}</td>
                    <td>{{ ucfirst($pedido->metodo_pago) }}</td>
                    <td class="text-right">${{ number_format($subtotal, 2) }}</td>
                    <td class="text-right fw-bold">${{ number_format($pedido->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No hay pedidos en este rango de fechas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td><strong>Subtotal Global:</strong></td>
                <td class="text-right">${{ number_format($granSubtotal, 2) }}</td>
            </tr>
            <tr>
                <td><strong>IVA Estimado (16%):</strong></td>
                <td class="text-right">${{ number_format($granTotal - $granSubtotal, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Global:</strong></td>
                <td class="text-right" style="font-size: 1.2em; font-weight: bold;">${{ number_format($granTotal, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Este documento es un reporte interno del Sistema Café Aurora.
    </div>
</body>
</html>
