<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    /**
     * Register new user
     *
     * @return Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirmation_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Email or password invalid.', $validator->errors());
        }

        $data = $request->all();
        $user = User::where('email', $data['email'])->first();
        if ($user) {
            return $this->sendError('Email already exist.', ['error' => 'Email already exist']);
        }

        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        $response['token'] = $user->createToken('AuthToken')->accessToken;
        $response['name'] = $user->name;

        return $this->sendResponse($response, 'User registered successfully.');
    }

    /**
     * Login
     *
     * @return Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user  = Auth::user();
            $response['token'] = $user->createToken('AuthToken')->accessToken;
            $response['name'] = $user->name;
            return $this->sendResponse($response, 'User logged in.');
        } else {
            return $this->sendError('Email or password wrong', ['error' => 'Unauthorized']);
        }
    }
}
