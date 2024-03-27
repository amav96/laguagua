<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
      
        if ($request->expectsJson()){
           
            if ($e instanceof ModelNotFoundException){
                return response([
                    'errors'=> 'No encontrado'
                ], 404);
            }

            if ($e instanceof NotFoundHttpException){
                return response([
                    'errors'=> 'Route Not Found'
                ], 404);
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $code = $e->getStatusCode();
                return response([
                    'errors'=> $e->getMessage()
                ], $code);
              }           
        }
        
        if ($e instanceof RouteNotFoundException){
            return response([
                'errors'=> 'Route Not Found Accept/application/json'
            ], 404);
        }

        if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            $code = $e->getStatusCode();
            return response([
                'errors'=> $e->getMessage()
            ], $code);
          
        }

        if ($e instanceof ModelNotFoundException){
            return response([
                'errors'=> 'Not Found'
            ], 404);
        }

      
        return parent::render($request, $e);
  
    } 
}
