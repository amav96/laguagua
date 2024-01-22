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
            'numero_documento'  => $this->data['numero_documento'] ? getNumbers($this->data['numero_documento']) : null,
            'empresa_id'        => $this->data['empresa_id'],
            "clientes_numeros"  => $this->data["clientes_numeros"]
        ]);
    }

}