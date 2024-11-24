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
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->authService = $authService;
    }
    /**
     * authenticates a user with their email and password 
     *
     * @param LoginRequest $request 
     *
     * @return response  of the status of operation : message the user data and the token
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $loginData = $this->authService->login($credentials);
        if (!$loginData['token']) {
            return response()->json([
                'status' => 'error',
                'message' => 'البيانات المدخلة خاطئة',
            ], 401);
        } else {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' =>  $loginData['user']
                ],
                'message' => 'تم تسجيل الدخول بنجاح',
                'authorisation' => [
                    'token' => $loginData['token'],
                    'type' => 'bearer',
                ]
            ], 200);
        }
    }

    /**
     * invalidates the user Auth token
     *
     * @param Request $request 
     *
     * @return response  of the status of operation : message 
     */
    public function logout(Request $request)
    {
        auth()->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الخروج بنجاح'
        ], 200);
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
