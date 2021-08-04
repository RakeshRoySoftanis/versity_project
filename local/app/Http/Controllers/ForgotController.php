<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ForgotRequest;
use App\Http\Requests\ResetRequest;
use App\Mail\ForgotMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Facades\Hash;

class ForgotController extends Controller
{
    public function forgotpassword(ForgotRequest $request)
    {
        $email = $request->email;
        if(User::where('email', $email)->doesntExist()){
            return response([
                'message' => "Email not found."
            ], 401);
        }

        //delete old tokens if have
        DB::table('password_resets')->where('email', $request->email)->delete();

        //generate token for reset password
        $token = str_random(60);

        try{
            //create a new token to be sent to the user. 
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token //change 60 to any length you want
            ]);

            Mail::to($email)->send(new ForgotMail($token));
            return response([
                'message' => "Reset password mail sent on your email !",
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }

    }

    public function resetpassword(ResetRequest $request){
        $token = $request->token;
        $password = Hash::make($request->password);

        $tokenData = DB::table('password_resets')->where('token', $token)->first();
        if ( !$tokenData ){
            return response([
                'message' => "Token expired.",
            ], 401);
        }

        $user = User::where('email', $tokenData->email)->first();
        if ( !$user ){
            return response([
                'message' => "Email not found.",
            ], 401);
        }
        $email = $tokenData->email;

        DB::table('users')->where('email', $email)->update(['password' => $password]);

        DB::table('password_resets')->where('email', $user->email)->delete();
        return response([
            'message' => "Password changed successfully.",
        ], 200);
    }
}
