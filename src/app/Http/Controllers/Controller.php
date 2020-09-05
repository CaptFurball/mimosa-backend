<?php

namespace App\Http\Controllers;

use App\Responses\GenericResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Validates incoming request based on rules.
     * Failure will result in complete halt in code execution.
     */
    protected function validate(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules); 
        $response  = new GenericResponse;
        
        if ($validator->fails()) {
            $response->createMalformedRequestResponse($validator->errors()->messages())
                ->send();

            exit();
        }
    }
}
