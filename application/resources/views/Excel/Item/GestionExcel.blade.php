<table>
    <tr>
        <td></td>
        <td>@lang('Nombre'):</td>
        <td style="font-weight: bold">{{ $usuario->nombre ?? $usuario->email  }}</td>
    </tr>
    <tr>
        <td></td>
        <td>@lang('Fecha desde'):</td>
        <td style="font-weight: bold">
        {{ $parametros["fecha_inicio"] }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td>@lang('Fecha hasta'):</td>
        <td style="font-weight: bold">
        {{ $parametros["fecha_fin"] }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td>@lang('Paquetes')</td>
        <td><strong>{{ count($items) }}</strong></td>
    </tr>
    <tr>
        <td></td>
        <td>@lang('Entregados')</td>
        <td><strong>{{ $metricas["entregados"] }} </strong> ({{ $metricas["entregados_porcentaje"] }})%</td>
    </tr>
    <tr>
        <td></td>
        <td>@lang('Preparados')</td>
        <td><strong>{{ $metricas["preparados"] }} </strong> ({{ $metricas["preparados_porcentaje"] }})%</td>
    </tr>
    <tr>
        <td></td>
        <td>@lang('No entregados')</td>
        <td><strong>{{ $metricas["no_entregados"] }} </strong> ({{ $metricas["no_entregados_porcentaje"] }})%</td>
    </tr>
    <tr>
        <td></td>
        <td>@lang('Retirados')</td>
        <td><strong>{{ $metricas["retirados"] }} </strong> ({{ $metricas["retirados_porcentaje"] }})%</td>
    </tr>
  
</table>

<table>
    <thead>
        <tr>
            <th > <strong>Estado</strong> </th>
            <th > <strong>Direccion</strong> </th>
            <th > <strong>localidad</strong> </th>
            <th > <strong>Cp</strong> </th>
            <th > <strong>Gestionado</strong> </th>
            <th > <strong>Creado</strong> </th>
            <th > <strong>Nro</strong> </th>
            <th > <strong>Tipo</strong>  </th>
            <th > <strong>Proveedor</strong>  </th>
            <th > <strong>Destinatario</strong>  </th>
            <th > <strong>Documento</strong>  </th>
            <th > <strong>Observaciones</strong>  </th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
      
            <tr>
                <td> {{ $item->itemEstado->nombre }} </td>
                <td> {{ !$item->parada ? '' : $item->parada->direccion_formateada }} </td>
                <td> {{ !$item->parada ? '' : $item->parada->localidad }} </td>
                <td> {{ !$item->parada ? '' : $item->parada->codigo_postal }} </td>
                <td> {{ $item->gestionado_transformado }} </td>
                <td> {{ $item->created_at_transformado }} </td>
                <td> {{ $item->track_id }} </td>
                <td> {{ $item->itemTipo->nombre }} </td>
                <td> {{ $item->itemProveedor->nombre }} </td>
                <td> {{ !$item->cliente ? '' : $item->cliente->nombre }} </td>
                <td> {{ !$item->cliente ? '' : $item->cliente->numero_documento }} </td>
                <td> {{ !$item->cliente ? '' : $item->cliente->observaciones }} </td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>
<br>