<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\Contacts;
use App\Models\Common;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Crypt;
use App\Models\Nzoho;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try{
            if(Auth::attempt($request->only('email','password'))){
                $user = Auth::user();
                $logged_contact = Contacts::where('module_id', $user->contact_id)->first();
                $token = $user->createToken('app')->accessToken;
                return response([
                    'message' => "Successfully Login",
                    'token' => $token,
                    'user' => $user,
                    'logged_contact' => $logged_contact,
                ], 200);
            }
        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }

        return response([
            'message' => "Invalid Email or Password."
        ], 401);
    }

    public function forgetPasswordMail(Request $request)
    {
        try{

            $user = user::where('email' , $request->email )->first();

            if (!empty($user)) {

                DB::table('password_resets')->where('email', $request->email)->delete();

                //create a new token to be sent to the user. 
                DB::table('password_resets')->insert([
                    'email' => $request->email,
                    'token' => str_random(60) //change 60 to any length you want
                ]);

                $tokenData = DB::table('password_resets')->where('email', $request->email)->first();
                $Common = new Common;
                $mail_data = array("Email" => $user->email,"Token" => $tokenData->token,"First_Name" => $user->first_name);

                if (strpos($request->publicPath, 'http://localhost:3000') !== false) {
                    //for local test purpose
                    $details['templete'] =  $Common->send_mail_to_reset_link_mailtrap($mail_data , $request->publicPath );
                
                    \Mail::to('softanis.rakeshroy@gmail.com')->send(new \App\Mail\MyTestMail($details));

                }else{
                    $details =  $Common->send_mail_to_reset_link($mail_data , $request->publicPath ); 
                }
                
                return response([
                    'message' => "Check Your Email To Reset Password"
                ], 200);
                
            }else{
                return response([
                    'message' => "There is no email"
                ], 200);
            }

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function resetPassword(Request $request)
    {

        try {
            //some validation
            $validatedData = $this->validate($request, [
                'password' => 'required|string|min:6|confirmed',
            ]);

            $token = $request->token;
            $password = $request->password;
            $tokenData = DB::table('password_resets')->where('token', $token)->first();

            $user = User::where('email', $tokenData->email)->first();
            if ( !$user ) {
                return response([
                    'message' => "No Email Exist"
                ], 200);
            }

            $user->password = Hash::make($password);
            $user->update(); //or $user->save();
            Auth::login($user);

            $crmid = $user->contact_id;

            DB::table('password_resets')->where('email', $user->email)->delete();
            $id = $user->contact_id;
            $zoho = New Nzoho;

            $zohoData[0] = array('Password' => $password);
            $updateZoho = $zoho->updateRecords($id, $zohoData, 'Contacts');
            $Common = new Common;
            $mail_data = array("Email" => $user->email,"Password" => $password,"First_Name" => $user->first_name);

            if (strpos($request->publicPath, 'http://localhost:3000') !== false) {
                //for local test purpose
                $details['templete'] =  $Common->send_mail_to_portal_credential_localhost($mail_data , $request->publicPath  );
                \Mail::to($user->email)->send(new \App\Mail\MyTestMail($details));

            }else{
                $Common->send_mail_to_portal_credential($mail_data , $request->publicPath );
            }


            return response([
                'message' => "Password Updated!"
            ], 200);

            
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }  
    }

    public function changePassword(Request $request)
    {

        try {
            //some validation
            $validatedData = $this->validate($request, [
                'password' => 'required|string|min:6|confirmed',
            ]);
            $user = User::where('email', $request->Email)->first();

            if ($user) {
                if ($user->password !="") {
                    if ($user->password != $request->old_password) {
                        return response([
                            'message' => "Old password is wrong"
                        ], 400);
                    }
                }
            }
            
            if ( !$user ) {
                return response([
                    'message' => "No Email Exist"
                ], 400);
            }
            $password = $request->password;

            $user->password = Hash::make($password);
            $user->update(); //or $user->save();
            Auth::login($user);

            $crmid = $user->contact_id;

            $id = $user->contact_id;
            $zoho = New Nzoho;

            $zohoData[0] = array('Password' => $password);
            $updateZoho = $zoho->updateRecords($id, $zohoData, 'Contacts');
            $Common = new Common;
            $mail_data = array("Email" => $user->email,"Password" => $password,"First_Name" => $user->first_name);

            // if (strpos($request->publicPath, 'http://localhost:3000') !== false) {
            //     //for local test purpose
            //     $details['templete'] =  $Common->send_mail_to_portal_credential_localhost($mail_data , $request->publicPath  );
            //     \Mail::to($user->email)->send(new \App\Mail\MyTestMail($details));

            // }else{
            //     $Common->send_mail_to_portal_credential($mail_data , $request->publicPath );
            // }


            return response([
                'message' => "Password Updated!"
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }  
    }

    public function register(RegisterRequest $request)
    {
        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $token = $user->createToken('app')->accessToken;
            return response([
                'message' => "Successfully Registered",
                'token' => $token,
                'user' => $user,
            ], 200);
        }
        catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 401);
        }

    }
}
