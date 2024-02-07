<?php

namespace App\Http\Controllers;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use App\Http\Requests\Recorrido\DetectarPropiedadesRequest;
use App\Http\Requests\Recorrido\GenerarInformeRecorridoRequest;
use App\Http\Requests\Recorrido\GetRecorridoRequest;
use App\Http\Requests\Recorrido\InformeRecorridoRequest;
use App\Http\Requests\Recorrido\OptimizarRecorridoRequest;
use App\Http\Requests\Recorrido\SaveDestinoRequest;
use App\Http\Requests\Recorrido\SaveOrigenRequest;
use App\Http\Requests\Recorrido\SaveRecorridoRequest;
use App\Http\Requests\Recorrido\UpdateEstadoRecorridoRequest;
use App\Http\Requests\Recorrido\UpdateOrigenActualRequest;
use App\Http\Services\ConsumoService;
use App\Http\Services\EmailService;
use App\Http\Services\Empresa\EmpresaService;
use App\Http\Services\Recorrido\RecorridoService;
use App\Models\CodigoArea;
use App\Models\ItemEstado;
use App\Models\ItemProveedor;
use App\Models\ItemTipo;
use App\Models\Recorrido;
use App\Models\TipoDocumento;
use App\Models\User;
use Illuminate\Http\Request;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Barryvdh\DomPDF\Facade\Pdf;

class RecorridoController extends Controller
{

    public function __construct(
        public RecorridoService $recorridoService,
        public EmpresaService $empresaService,
       
    ){}

    public function findAll(GetRecorridoRequest $request, int $recorrido_id = null){

        try {

            $parametros = $request->all();
    
            $parametros["recorrido_id"] = $recorrido_id ?? $request->input("recorrido_id");
            $usuario = $request->user();

            $recorridos = $this->recorridoService->findAll(
                parametros: $parametros, 
                permisos: [],
                usuarioAutenticadoId: $usuario->id , 
            );
            
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), 404);
        }
 
        return response()->json($recorridos);
    }

    public function create(SaveRecorridoRequest $request){

        $usuarioAutenticado = $request->user();

        $usuarioId = $request->rider_id;

        try {

            if($usuarioAutenticado->id !== $usuarioId){
                throw new BussinessException(AppErrors::USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::USUARIO_NO_TE_PERTENECE_CODE);
            }
        
            // if(!$this->empresaService->usuarioPerteneceEmpresa($usuarioId, $request->input("empresa_id"))){
            //     throw new BussinessException(AppErrors::EMPRESA_USER_NOT_EXISTS_MESSAGE, AppErrors::EMPRESA_USER_NOT_EXISTS_CODE);
            // }

            $data = [
                "rider_id"      => $usuarioAutenticado->id,
                // "empresa_id"    => $request->input("empresa_id"),
                "inicio"        => $request->input("inicio"),
            ];

            $recorrido = $this->recorridoService->create($data, $usuarioAutenticado->id);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::EMPRESA_USER_NOT_EXISTS_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $recorrido]);
    }

    public function updateOrigen(Recorrido $recorrido, SaveOrigenRequest $request){

        $usuario = $request->user();

        try {
            
            if(!$this->recorridoService->perteneceUsuario($usuario->id, $recorrido->id)){
                throw new BussinessException(AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE);
            }

            $origen = $this->recorridoService->updateOrigen($request->all(), $recorrido->id);


        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $origen->only(["id", "origen_lat", "origen_lng", "origen_formateado","origen_auto"])]);

    }

    public function updateOrigenActual(Recorrido $recorrido, UpdateOrigenActualRequest $request){

        $usuario = $request->user();

        try {
            
            if(!$this->recorridoService->perteneceUsuario($usuario->id, $recorrido->id)){
                throw new BussinessException(AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE);
            }

            $origen = $this->recorridoService->updateOrigenActual($request->all(), $recorrido->id);


        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $origen->only(["id", "origen_lat", "origen_lng", "origen_formateado","origen_auto"])]);

    }

    public function removeOrigen(Recorrido $recorrido, Request $request){
      
        $usuario = $request->user();

        try {
            
            if(!$this->recorridoService->perteneceUsuario($usuario->id, $recorrido->id)){
                throw new BussinessException(AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE);
            }

            $origen = $this->recorridoService->removeOrigen($recorrido->id);


        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $origen->only(["id", "origen_lat", "origen_lng", "origen_formateado"])]);

    }

    public function removeDestino(Recorrido $recorrido, Request $request){

        $usuario = $request->user();

        try {
            
            if(!$this->recorridoService->perteneceUsuario($usuario->id, $recorrido->id)){
                throw new BussinessException(AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE);
            }

            $origen = $this->recorridoService->removeDestino($recorrido->id);


        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $origen->only(["id", "origen_lat", "origen_lng", "origen_formateado"])]);

    }

    public function updateDestino(Recorrido $recorrido, SaveDestinoRequest $request){

        $usuario = $request->user();

        try {
            
            if(!$this->recorridoService->perteneceUsuario($usuario->id, $recorrido->id)){
                throw new BussinessException(AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE);
            }

            $destino = $this->recorridoService->updateDestino($request->all(), $recorrido->id);


        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }

        return response()->json(["recorrido" => $destino->only(["id", "destino_lat", "destino_lng", "destino_formateado"])]);

    }

    public function updateEstado(Recorrido $recorrido, UpdateEstadoRecorridoRequest $request){

       try {
            $usuario = $request->user();

            if(!$this->recorridoService->perteneceUsuario($usuario->id, $recorrido->id)){
                throw new BussinessException(AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_MESSAGE, AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE);
            }

            $recorridoEstado = $this->recorridoService->updateEstado($recorrido, $request->all());
        
        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }

        return $recorridoEstado;
    }

    public function optimizar(OptimizarRecorridoRequest $request){
        
        [$recorrido, $distancia, $duracion, $polyline ] = $this->recorridoService->optimizar($request->all());

        return response()->json(compact('recorrido', 'distancia', 'duracion', 'polyline'), 200);
    }

    public function detectarPropiedades(DetectarPropiedadesRequest $request) {

        try {
            $imageAnnotatorClient = new ImageAnnotatorClient([
                'credentials' => env('GOOGLE_APPLICATION_CREDENTIALS'),
            ]);

            $usuario = $request->user();
        
            $imageContent = file_get_contents($request->file->getPathName());
            $response = $imageAnnotatorClient->textDetection($imageContent);
        
            // Verifica si hay alguna anotación de texto
            $anotations = $response->getTextAnnotations();
            if ($anotations && isset($anotations[0])) {
                // Obtiene la primera anotación de texto (puedes ajustar esto según tus necesidades)
                $textAnnotation = $anotations[0];
    
                // Accede al contenido del texto
                $textContent = $textAnnotation->getDescription();
               
                // Define patrones de expresiones regulares para cada propiedad que deseas extraer
                $patterns = [
                    'direccion' => '/Direccion:\s*(.*?)\n/',
                    'nombre' => '/Destinatario:\s*(.*?)\n/',
                    'numero_telefono' => '/Teléfono:\s*(.*?)\n/',
                    'numero_documento' => '/DNI:\s*(\d+)/',
                    'enviar_a' => '/Enviar a:\s*(.*?)\n/',
                    'track_id' => '/Envio:\s*(.*?)\n/',
                    'notas_del_cliente' => '/Notas del cliente:\s*(.*?)\n/',
                    'observaciones' => '/Referencia:\s*(.*?)\n/',
                    'envio_flex' => '/\bEnvío Flex\b/i',
                    'residencial' => '/\bRESIDENCIAL\b/i',
                    'comercial' => '/\COMERCIAL\b/i',
                ];

    
                // Inicializa el array de resultados
                $result = [
                    'direccion' => null,
                    'nombre' => null,
                    'numero_documento' => null,
                    'track_id' => null,
                    'observaciones' => null,
                    'tipo_documento_id' => null,
                    'item_proveedor_id' => null,
                    'tipo_domicilio' => null,
                    'item_tipo_id' => null,
                    'item_estado_id' => null,
                    'numero_telefono' => null,
                    'codigo_area_id'   => null
                ];
    
                // Busca cada propiedad en el texto utilizando las expresiones regulares
                foreach ($patterns as $label => $pattern) {
                   if (preg_match($pattern, $textContent, $matches)) {
                        $result[$label] = count($matches) > 1 ? $matches[1] : $matches[0];
                    }
                }

                if (isset($result['numero_documento'])) {
                    //Si ya encontramos el dni, busca la dirección debajo del dni
                    $dniPattern = '/DNI:\s*\d+\n(.*?)\n/';
                    if (preg_match($dniPattern, $textContent, $dniMatches) && count($dniMatches) > 1) {
                        $result['direccion'] = $dniMatches[1];
                    }
                    $result['tipo_documento_id'] = TipoDocumento::DNI;
                } 

                if(isset($result['enviar_a'])){
                    $result['nombre'] = $result['enviar_a'];
                    unset($result['enviar_a']);
                }

                if(isset($result['notas_del_cliente'])){
                    $result['observaciones'] = $result['notas_del_cliente'];
                    unset($result['notas_del_cliente']);
                }

                if(isset($result['envio_flex'])){
                    $result['item_proveedor_id'] = ItemProveedor::MERCADO_LIBRE;
                    unset($result['envio_flex']);
                } else {
                    $result['item_proveedor_id'] = ItemProveedor::INDEPENDIENTE;
                }

                if(isset($result['residencial'])){
                    $result['tipo_domicilio'] = $result['residencial'];
                    unset($result['residencial']);
                }

                if(isset($result['comercial'])){
                    $result['tipo_domicilio'] = $result['comercial'];
                    unset($result['comercial']);
                }

                $result['item_tipo_id'] = ItemTipo::ENTREGA;
                $result['item_estado_id'] = ItemEstado::PREPARADO;

                if(isset($result["numero_telefono"])){
                    $codigosArea = CodigoArea::get();
                    $numero_telefono = trim($result["numero_telefono"]);
                    foreach($codigosArea as $area){
                        $partir = explode($area->codigo, $numero_telefono);
                        if($partir && count($partir) > 1){
                            $result["codigo_area_id"] = $area->id;
                            $result["numero_telefono"] = $partir[1];
                            break;
                        }
                    }
                }

                $result = array_filter($result);
              
                $imageAnnotatorClient->close();
                $consumoService =  new ConsumoService();
                $consumoService->guardarConsumoDetectar($usuario->id);
    
                return response()->json(["propiedades" => $result]);
            } else {
                return response()->json(["propiedades" => []]);
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 400);
        }
    }

    public function informe(InformeRecorridoRequest $request){

        try {
            $usuario = User::findOrFail($request["rider_id"]);

            $recorrido = Recorrido::with(["paradas.comprobantes","paradas.paradaEstado"])->findOrFail($request["recorrido_id"]);
           
            $urlBucket = env('S3BUCKETLAGUAGUA');
            $pdf = PDF::loadView('pdf.Recorrido.informe', ["recorrido" => $recorrido , "urlBucket" => $urlBucket]);

            // Generar un nombre de archivo único para el PDF
            $pdfFileName = 'informe_' . time() . '.pdf';

            $path = storage_path('app/temp/' . $pdfFileName);

            // Guardar el PDF en una ubicación temporal del servidor
            $pdf->save( $path);

            $emailService = new EmailService();

            $configEmail = config('app.values');

            $emailService->sendEmail(
                $configEmail["MAIL_HOST"],
                $configEmail["MAIL_USERNAME"],
                $configEmail["MAIL_PASSWORD"],
                $configEmail["MAIL_USERNAME"],
                config('app.name'),
                $usuario->email,
                'Informe',
                'Este es el informe adjunto en formato PDF de tu recorrido',
                $path
            );

            // Eliminar el PDF temporal después de enviar el correo electrónico
            unlink( $path);

           
            $consumoService =  new ConsumoService();
            $consumoService->guardarConsumoInforme($usuario->id);

        } catch (BussinessException $e) {
            return response()->json($e->getAppResponse(), $e->getInternalCode() === AppErrors::RECORRIDO_USUARIO_NO_TE_PERTENECE_CODE ? 404 : 400);
        }
        
       
        return  response()->json();
    }

    
}