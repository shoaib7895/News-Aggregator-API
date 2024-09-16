<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class UserController extends Controller
{
    //Register function create new users
    public function register(Request $request)
    {
        //validation to filter required data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', //unique email check into database table 
            'password' => 'required|string|min:8|confirmed',
        ]);

        // return error with code 202 if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // create new recoed into database table users
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
       
        // Token create for user to authenticate
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return Success Message with user data and access token
        return response()->json([
            'user' => $user->only(['id', 'name', 'email']),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    //Login function to authenticate user
    public function login(Request $request)
    {
        //attemp to login with email and password
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        //get User data from table for generating token and send reponse in json
        $user = User::select('id','name','email')->where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        //Success response in json format
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    //logout function 
    public function logout(Request $request)
    {
        //delete token 
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

     //Send Reset Link
     public function sendResetLink(Request $request)
     {
        //validate email exist or not in db
         $request->validate(['email' => 'required|email']);
 

         $status = Password::sendResetLink(
             $request->only('email')
         );
 
         return $status === Password::RESET_LINK_SENT
                     ? response()->json(['message' => __($status)])
                     : response()->json(['email' => __($status)], 400);
     }


     //Reset Password Function
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? response()->json(['message' => __($status)])
                    : response()->json(['email' => __($status)], 400);
    }




}
