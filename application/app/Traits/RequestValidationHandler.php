<?php 

namespace App\Traits;

use Illuminate\Http\Request;
use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


trait RequestValidationHandler {

    protected $customHttpCode = 400;

    protected $customValidation = false;

    protected $customCode = "";

    protected $customMessage = "";

    public function setCustomHttpCode($code): void
    {
        $this->customHttpCode = $code;
    }

    public function setCustomValidation(bool $value): void
    {
        $this->customValidation = $value;
    }

    public function setCustomCode(string $value): void
    {
        $this->customCode = $value;
    }


    public function setCustomMessage(string $value): void
    {
        $this->customMessage = $value;
    }


    protected function failedValidation(Validator $validator)
    {
      
        if($this->customValidation && $this->customMessage && $this->customCode){
            $this->throwExcepcionValidation();   
        }

        $messages = [
            "code" => AppErrors::WRONG_INPUT_DATA_CODE,
            "messages" => $validator->errors()->get('*')
        ];
        throw new HttpResponseException(response($messages, $this->customHttpCode));
    }

    protected function throwExcepcionValidation(){
        $bussinessExepction = new BussinessException($this->customMessage, $this->customCode);
        throw new HttpResponseException(response($bussinessExepction->getAppResponse(), $this->customHttpCode));
    }

}