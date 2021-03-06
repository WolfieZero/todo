<?php

namespace App\Http\Controllers\Api;

use Config;
use JWTAuth;
use App\User;
use Validator;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Requests\SignupRequest;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthApiController extends Controller
{
    use Helpers;

    public function check()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($user->id > 0) {
            return $this->response->array('true');
        }

        return $this->response->error('error', 500);
    }

    /**
     * Login to app and return a JWT.
     *
     * @param   Request  $request  User login credentials
     * @return  response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $token = User::first();

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'invalid_credentials'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'cannot_create_token'
            ], 500);
        }

        return response()->json(compact('token'));
    }

    /**
     * Signup to use app.
     *
     * @param   SignupRequest  $request  User signup details
     * @return  response
     */
    public function signup(SignupRequest $request)
    {
        // Create the user
        $userData = $request->only('name', 'email', 'password');
        $userData['password'] = bcrypt($userData['password']);

        // Check we have successfully created a user
        if (! User::create($userData)) {
            return $this->response->error('could_not_create_user', 500);
        }

        // Checks if we want to return back the token
        if (env('API_SIGNUP_TOKEN_RELEASE', true)) {
            return $this->login($request);
        }

        return response()->json([ 'success' => true ]);
    }

}
