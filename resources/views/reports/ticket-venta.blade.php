<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 15px; }
        body { font-family: 'Courier New', Courier, monospace; font-size: 11px; color: #000; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .w-full { width: 100%; border-collapse: collapse; }
        .linea-punteada { border-bottom: 1px dashed #000; margin: 8px 0; }
        table th, table td { padding: 3px 0; }
    </style>
</head>
<body>
    <div class="text-center">
        <h2 style="margin: 0;">EL ENCUENTRO</h2>
        <p style="margin: 2px 0;">RUC: 20123456789</p>
        <p style="margin: 2px 0;">Av. Principal 123, Arequipa</p>
        <div class="linea-punteada"></div>
        <h3 style="margin: 4px 0;">TICKET DE VENTA #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h3>
        <p style="margin: 2px 0;">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y h:i A') }}</p>
    </div>

    <div class="linea-punteada"></div>

    <p style="margin: 2px 0;"><strong>CLIENTE:</strong> {{ $client ? $client->name : 'Público General' }}</p>
    @if($client && $client->document_number)
        <p style="margin: 2px 0;"><strong>DNI/RUC:</strong> {{ $client->document_number }}</p>
    @endif

    <div class="linea-punteada"></div>

    <table class="w-full">
        <thead>
            <tr style="border-bottom: 1px solid #000;">
                <th class="text-left">CANT</th>
                <th class="text-left">PLATO</th>
                <th class="text-right">SUBT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $item)
                <tr>
                    <td class="text-left">{{ $item->quantity }}</td>
                    <td class="text-left">{{ Str::limit($item->name, 12) }}</td>
                    <td class="text-right">{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="linea-punteada"></div>

    <h3 class="text-right" style="margin: 5px 0;">TOTAL: S/ {{ number_format($order->total, 2) }}</h3>

    <div class="text-center" style="margin-top: 15px;">
        <p>¡Gracias por su preferencia!</p>
    </div>
</body>
</html>
