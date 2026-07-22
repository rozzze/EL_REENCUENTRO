<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Productos</title>
    <style>
        @page { margin: 15px; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .w-full { width: 100%; border-collapse: collapse; }
        .mt-2 { margin-top: 10px; }
        .mb-2 { margin-bottom: 10px; }
        .linea-punteada { border-bottom: 1px dashed #000; margin: 10px 0; }

        table th, table td { padding: 3px 0; }
    </style>
</head>
<body>

    <div class="text-center mb-2">
        <h2 style="margin: 0;">EL ENCUENTRO</h2>
        <p style="margin: 3px 0;">RUC: 20123456789</p>
        <p style="margin: 3px 0;">Av. Principal 123, Arequipa</p>
        <div class="linea-punteada"></div>
        <h3 style="margin: 5px 0;">REPORTE DE VENTAS</h3>
        <p style="margin: 3px 0;">TICKET INTERNO</p>
    </div>

    <p style="margin: 3px 0;"><strong>FECHA:</strong> {{ $fecha }}</p>
    <p style="margin: 3px 0;"><strong>TIPO:</strong> POR PRODUCTO</p>

    <div class="linea-punteada"></div>

    <table class="w-full">
        <thead>
            <tr style="border-bottom: 1px solid #000;">
                <th class="text-left">CANT</th>
                <th class="text-left">DESCRIPCIÓN</th>
                <th class="text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productos as $item)
                <tr>
                    <td class="text-left">{{ $item->cantidad }}</td>
                    <td class="text-left">{{ Str::limit($item->name, 15) }}</td>
                    <td class="text-right">S/ {{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No hay ventas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="linea-punteada"></div>

    <table class="w-full mt-2 font-bold">
        <tr>
            <td class="text-left">OP. GRAVADAS:</td>
            <td class="text-right">S/ {{ number_format($totalGeneral / 1.18, 2) }}</td>
        </tr>
        <tr>
            <td class="text-left">IGV (18%):</td>
            <td class="text-right">S/ {{ number_format($totalGeneral - ($totalGeneral / 1.18), 2) }}</td>
        </tr>
        <tr>
            <td class="text-left" style="font-size: 14px; padding-top: 5px;">TOTAL GENERAL:</td>
            <td class="text-right" style="font-size: 14px; padding-top: 5px;">S/ {{ number_format($totalGeneral, 2) }}</td>
        </tr>
    </table>

    <div class="linea-punteada" style="margin-top: 15px;"></div>

    <div class="text-center">
        <p>*** FIN DEL REPORTE ***</p>
    </div>

</body>
</html>
