<?php

namespace App\Services;


use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * login a user
     * @param array $credentials 
     * @return array  token and UserResource user
     * 
     */
    public function login(array $credentials)
    {
        try {
            $user = '';
            $token = JWTAuth::attempt($credentials);
            if ($token) {
                $user = UserResource::make(auth()->user());
            }
            return [
                'token' => $token,
                'user' => $user
            ];
        } catch (Exception $e) {
            Log::error("error in get login" . $e->getMessage());
            throw new Exception("there is something wrong in server");
        }
    }
    /**
     * register a user
     * @param  $registerData 
     * @return array  token and UserResource user
     * 
     */
    public function register($registerData)
    {
        try {

            $user = new  User();
            $user->name = $registerData->name;
            $user->email = $registerData->email;
            $user->password = $registerData->password;
            $user->save();
            $token = Auth::login($user);
            $user = UserResource::make($user);

            return [
                'token' => $token,
                'user' => $user
            ];
        } catch (Exception $e) {
            Log::error("error in  register" . $e->getMessage());
            throw new Exception("there is something wrong in server");
        }
    }
    /**
     * get role user in project
     * @param  Project $project 
     * @return string  active_user_role
     * 
     */
    public function getRoleUserInProject($project)
    {
        try {

            $user = User::find(Auth::user()->id);

            $project = $project->load(['users' => function ($q) use ($user) {
                $q->where('user_id', '=', $user->id);
            }]);

            $active_user_role = $project->users[0]->pivot->role;
            return $active_user_role;
        } catch (Exception $e) {
            Log::error("error in  get role user in project" . $e->getMessage());
            throw new Exception("there is something wrong in server");
        }
    }
}