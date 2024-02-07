<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
<style>
    body {
        font-family: "Comic Sans MS", cursive, sans-serif; /* Aquí estableces la fuente Comic Sans MS como la primera opción */
    }
    .item-recorrido {
        margin-top: 5px;
    }

    .item-recorrido_propiedad {
        margin-top: 5px;
    }

    .propiedad-estado {
        margin-top: 5px;
        max-width:100px;
        border-radius: 20px;
        padding: 5px 10px 5px 10px;
    }

    .propiedad-estado_text {
        margin:auto;text-align:center;
    }
</style>

<div class="bg-white" style="padding: 16px;">
    <div class="item-recorrido" style="max-width: 85%;">
        <div style="font-weight: bold;"> Origen </div> 
       
        <div class="item-recorrido_propiedad"> {{ $recorrido->origen_formateado }} </div> 
    </div>
    <div class="item-recorrido" style="max-width: 85%;">
        <div style="font-weight: bold;"> Destino </div>
        <div class="item-recorrido_propiedad"> {{ $recorrido->destino_formateado }} </div> 
    </div>
    <div class="item-recorrido">
        <div style="font-weight: bold;"> Paradas </div>
        <div class="item-recorrido_propiedad"> {{ count($recorrido->paradas) }} </div>
    </div>
    <div class="item-recorrido">
        <div style="font-weight: bold;"> Inicio </div>
        <div class="item-recorrido_propiedad" style="margin-left: 8px;"> {{ $recorrido->inicio }} </div>
    </div>
    <div class="item-recorrido">
        <div style="font-weight: bold;"> Estado </div>
        <div class="propiedad-estado" style="background-color: {{ $recorrido->recorridoEstado->color }}; ">
            <div class="propiedad-estado_text"> {{ $recorrido->recorridoEstado->nombre }} </div>
        </div>
    </div>

    @if (count($recorrido->paradas) > 0)
        <div style="margin-top: 16px; font-size: 20px; font-weight: bold;"> Paradas </div>
        @foreach ($recorrido->paradas as $parada)
            <div style="margin-top: 8px; border: 1px solid #ccc; padding: 8px;page-break-inside: avoid;">
                <div style="max-width: 85%;">
                    <div style="font-weight: bold;"> Direccion </div> 
                    <div> {{ $parada->direccion_formateada }} </div> 
                </div>
                <div>
                    <div style="font-weight: bold;"> Estado </div>
                   
                    <div class="propiedad-estado" style="background-color: {{ $parada->paradaEstado->color }}">
                        <div class="propiedad-estado_text">{{ $parada->paradaEstado->nombre }} </div>
                    </div>
                </div>
                @if (count($parada->items) > 0 && $parada->items->contains('comprobantes'))
                    <div style="margin-top: 8px;">
                        @foreach ($parada->items as $item)
                            @foreach ($item->comprobantes as $comprobante)
                            <img src="{{ $urlBucket }}/{{ $comprobante->path }}" style="height: 400px; max-width: 400px;">
                            @endforeach
                        @endforeach
                    </div>
                @endif

                @if (count($parada->comprobantes) > 0)
                    <div style="margin-top: 8px;">
                        @foreach ($parada->comprobantes as $comprobante)
                        <img src="{{ $urlBucket }}/{{ $comprobante->path }}" style="height: 400px; max-width: 400px;">
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
</body>
</html>