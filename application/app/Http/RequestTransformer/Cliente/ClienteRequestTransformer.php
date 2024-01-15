<?php 

namespace App\Http\RequestTransformer\Cliente;

class ClienteRequestTransformer {

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function transform(): array
    {
       
        return array_filter([
            'tipo_documento_id' => $this->data['tipo_documento_id'],
            'nombre'            => isset($this->data['nombre']) ? trim($this->data['nombre']) : null,
            'numero_documento'  => getNumbers($this->data['numero_documento']),
            'codigo_area_id'    => $this->data['codigo_area_id'] ?? null,
            'numero_celular'    => isset($this->data['numero_celular']) ? getNumbers($this->data['numero_celular']) : null,
            'numero_fijo'       => isset($this->data['numero_fijo']) ? getNumbers($this->data['numero_fijo']) : null,
            'empresa_id'        => $this->data['empresa_id'],
        ]);
    }

}