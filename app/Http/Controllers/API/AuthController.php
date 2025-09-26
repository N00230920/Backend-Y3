<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facade\Auth;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;

use Validator;

class AuthController extends BaseController
{
    /**
     * Register api
     * 
     * @return \Illuminate\Http\Response
    */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'c_password' => 'required',
        ]);
    }

    /**
     * Login api
     * 
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
    }
}

