<?php

namespace App\Http\Requests\Parada;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Services\Parada\ParadaService;
use App\Http\Services\Recorrido\RecorridoService;
use App\Traits\RequestValidationHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SaveParadaRequest extends FormRequest
{

    use RequestValidationHandler;

    protected $recorridoService;
    protected $paradaService;

    public function __construct(
        RecorridoService $recorridoService,
        ParadaService $paradaService
        )
    {
        parent::__construct();
        $this->recorridoService = $recorridoService;
        $this->paradaService =  $paradaService;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if($this->isMethod("PATCH")){
            return [
                "parada_estado_id"  => "required|integer|exists:paradas_estados,id",
                "rider_id"          => "required|integer|exists:usuarios,id"
            ];
        }

        $rules = [
            "lat" => [
                "required",
                "numeric"
            ],
            "lng" => [
                "required",
                "numeric"
            ],
            "direccion_formateada" => [
                "required",
                "string"
            ],
            "codigo_postal" => [
                "nullable",
                "string"
            ],
            "localidad" => [
                "nullable",
                "string"
            ],
            "provincia" => [
                "nullable",
                "string"
            ],
            "rider_id" => [
                "required",
                "integer",
                function ($attribute, $value, $fail) {
                    if ($value !== $this->user()->id) {
                        $this->setCustomValidation(true);
                        $this->setCustomCode(AppErrors::USUARIO_NO_TE_PERTENECE_CODE);
                        $this->setCustomMessage(AppErrors::USUARIO_NO_TE_PERTENECE_MESSAGE);
                        $fail(true);
                    }
                }
            ],
            "tipo_domicilio" => ["nullable", "string"]
        ];

        $this->agregarValidacionRecorrido($rules);
        
        return $rules;
    }

    protected function agregarValidacionRecorrido(array &$rules): void
    {
        $riderId = $this->input('rider_id');
        $recorridoId = $this->input('recorrido_id');

        if ($riderId && $recorridoId) {
            $rules["recorrido_id"] = ["required", "integer"];

            $rules["recorrido_id"][] = function ($attribute, $value, $fail) use ($riderId, $recorridoId) {
                if (!$this->recorridoService->existeRecorrido($value)) {
                    $this->setCustomValidation(true);
                    $this->setCustomCode(AppErrors::RECORRIDO_NO_EXISTE_CODE);
                    $this->setCustomMessage(AppErrors::RECORRIDO_NO_EXISTE_MESSAGE);
                    $fail(true);
                }

                if (!$this->recorridoService->perteneceUsuario($riderId, $recorridoId)) {
                    $this->setCustomValidation(true);
                    $this->setCustomCode(AppErrors::PARADA_NO_PERTENCE_RECORRIDO_USUARIO_CODE);
                    $this->setCustomMessage(AppErrors::PARADA_NO_PERTENCE_RECORRIDO_USUARIO_MESSAGE);
                    $fail(true);
                }
            };
        }
    }

}
