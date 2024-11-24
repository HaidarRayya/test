<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Mail\RegisterUser;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->middleware('auth:api', ['except' => ['register']]);
        $this->authService = $authService;
    }

    /**
     * create a new user account in the system 
     *
     * @param RegisterRequest $request 
     *
     * @return JsonResponse|mixed  of the status of operation : message the user data and the token
     */
    public function register(RegisterRequest $request)
    {
        $registerData = $request->validatedWithCasts();
        $userData = $this->authService->register($registerData);

        Mail::to($userData['user']->email)->send(new RegisterUser($userData['user']));
        return response()->json([
            'status' => 'success',
            'message' => 'تم التسجيل بنجاح',
            'data' => [
                'user' =>  $userData['user']
            ],
            'authorisation' => [
                'token' => $userData['token'],
                'type' => 'bearer',
            ]
        ], 201);
    }
}
