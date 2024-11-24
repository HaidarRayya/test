<?php

namespace App\Services;


use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use App\Models\User;

class AuthService
{

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
}
