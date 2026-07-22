<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #1f2937;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1f2937;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 12px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #d1d5db;
        }
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .total-row td {
            background-color: #1f2937;
            color: #ffffff;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 30px;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Restaurante "El Encuentro"</h1>
        <p>RUC: 20123456789 | Dirección: Jacobo Hunter, Arequipa, Perú</p>
    </div>

    <div class="info-section">
        <h2 style="margin:0 0 10px 0; font-size: 18px; color: #111827;">{{ $titulo }}</h2>
        <p><strong>Fecha de Emisión:</strong> {{ $fecha }}</p>
        <p><strong>Generado por:</strong> Sistema Administrativo</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">#</th>
                <th width="45%">Descripción</th>
                <th width="20%">Categoría</th>
                <th width="15%" class="text-center">Cantidad</th>
                <th width="15%" class="text-right">Total (S/)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->descripcion }}</td>
                    <td>{{ $row->categoria }}</td>
                    <td class="text-center">{{ $row->cantidad }}</td>
                    <td class="text-right">{{ number_format($row->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px;">No hay datos registrados para este reporte.</td>
                </tr>
            @endforelse

            @if(count($data) > 0)
                <tr class="total-row">
                    <td colspan="4" class="text-right" style="padding: 12px;">TOTAL GENERAL:</td>
                    <td class="text-right" style="padding: 12px;">S/ {{ number_format($totalGeneral, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Este documento es un reporte interno del sistema generado automáticamente.
    </div>

</body>
</html>
