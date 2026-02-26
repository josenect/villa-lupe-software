<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            width: 58mm;
            padding: 3mm;
            margin: 0;
        }

        h1 {
            font-size: 15px;
            text-align: center;
            margin: 0;
        }

        p {
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .separador {
            text-align: center;
            font-size: 11px;
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            font-size: 12px;
            padding: 1px 0;
        }

        .mesa-nombre {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px dashed #000;
            padding-top: 3px;
            margin-top: 3px;
        }

        .mesa-total {
            text-align: right;
            font-weight: bold;
        }

        .producto-linea {
            font-size: 11px;
            padding-left: 4px;
        }

        .gran-total {
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 3px;
        }

        @media print {
            @page {
                size: 58mm auto;
                margin: 0;
            }
            body {
                width: 58mm;
                margin: 0;
                padding: 3mm;
            }
        }
    </style>
</head>
<body>
    @php
        $restNombre    = \App\Models\Setting::get('restaurante_nombre', 'Villa Lupe');
        $restPropiedad = \App\Models\Setting::get('restaurante_propiedad', '');
        $restDireccion = \App\Models\Setting::get('restaurante_direccion', '');
    @endphp

    <h1>{{ strtoupper($restNombre) }}</h1>
    @if($restPropiedad)<p>{{ $restPropiedad }}</p>@endif
    @if($restDireccion)<p>{{ $restDireccion }}</p>@endif
    <p>{{ date('d/m/Y H:i') }}</p>
    <p><strong>PENDIENTES POR COBRAR</strong></p>

    <div class="separador">--------------------------------</div>

    @foreach($mesas as $mesa)
    <div class="mesa-nombre">{{ $mesa->name }}</div>
    @foreach($mesa->elementTables as $item)
    <div class="producto-linea">
        {{ $item->amount }}x {{ $item->producto->name }}
        <span style="float:right;">$ {{ number_format(($item->price - $item->dicount) * $item->amount, 0, ',', '.') }}</span>
    </div>
    @endforeach
    <div class="mesa-total">
        Sub: $ {{ number_format($mesa->subtotal_pendiente, 0, ',', '.') }}
        @if($propinaHabilitada)
        | Prop: $ {{ number_format($mesa->propina_pendiente, 0, ',', '.') }}
        @endif
        | Total: $ {{ number_format($mesa->total_pendiente, 0, ',', '.') }}
    </div>
    @endforeach

    <div class="separador">--------------------------------</div>

    <table class="gran-total">
        <tr>
            <td>Subtotal general:</td>
            <td style="text-align:right;">$ {{ number_format($totalGlobal, 0, ',', '.') }}</td>
        </tr>
        @if($propinaHabilitada)
        <tr>
            <td>Propina ({{ $propinaPct }}%):</td>
            <td style="text-align:right;">$ {{ number_format($propinaGlobal, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td><strong>GRAN TOTAL:</strong></td>
            <td style="text-align:right;"><strong>$ {{ number_format($granTotal, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>Mesas activas:</td>
            <td style="text-align:right;">{{ $mesas->count() }}</td>
        </tr>
    </table>

    <script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
