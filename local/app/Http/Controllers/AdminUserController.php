<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\Nzoho;
use App\Models\AdminUser;
use App\Models\Accounts;
use App\Models\User;
use Hash;

class AdminUserController extends Controller
{

    public function __construct()
    {

    }

    public function showLoginForm(){
        $masterloging = Session::get('getmasteradmin');
        session::forget('getmasteradmin');
        if (isset($masterloging) && !empty($masterloging)) {
           return redirect('master-admin-dashboard');
        }
        else {
           return view('master.adminUser.login');
        }
    }

    public function showContactList( $account_module_id ){
        $admin_token = isset($_REQUEST['AuthTokenAdminUser']) ? $_REQUEST['AuthTokenAdminUser'] : "" ;
        $masterloging = DB::table('admin_user_token')->where('token', $admin_token)->where('expires_at', '>', date('Y-m-d H:i:s'))->first();

        if (isset($masterloging) && !empty($masterloging)) {
            $contacts = AdminUser::getContactsByAccount($account_module_id);

            return response([
                'contacts' => $contacts,
            ], 200);
        }
        else{
            return response([
                'message' => "Something Went Wrong!",
            ], 400);
        }

    }

    public function logout(){
        session::forget('getmasteradmin');
        if(!Session::has('getmasteradmin')){
            return redirect('master-admin-login');
        }
    }

    public function loginAdmin(Request $request)
    {
        $email    = trim($request->email);
        $password = $request->password;

        $result = AdminUser::getUserAuth($email, $password);

        if (!empty($result)) {

            $token = DB::table("admin_user_token")->where("admin_id",$result->id)->first();
            if ($token =="") {
                $data['token'] = Hash::make( time() );
                $data['admin_id'] = $result->id;
                $data['expires_at'] = date('Y-m-d H:i:s', strtotime(date("Y-m-d"). ' + 1 days'));
                DB::table("admin_user_token")->insert($data);
                $cookie_name = "msatoken";
                $cookie_value = $data['token'];
                setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
            }else{

                $data['token'] = Hash::make( time() );
                $data['admin_id'] = $result->id;
                $data['expires_at'] = date('Y-m-d H:i:s', strtotime(date("Y-m-d"). ' + 1 days'));
                DB::table("admin_user_token")->where("id" , $token->id )->update($data);
                $cookie_name = "msatoken";
                $cookie_value = $data['token'];
                setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
            }

            return response([
                'message' => "Successfully Login",
                'token' => $data['token'],
                'user' => $result,
                'expires_at' => $result->expires_at,
                'server_time_at_now' => date('Y-m-d H:i:s'),
            ], 200);
            
        } else {
            return response([
                'message' => "Email or Password is invalid",
                'status' => 'TokenExpired',
                'server_time_at_now' => date('Y-m-d H:i:s'),
            ], 400);
        }

    }

    public function dashboard(){
        $admin_token = isset($_REQUEST['AuthTokenAdminUser']) ? $_REQUEST['AuthTokenAdminUser'] : "" ;
        $masterloging = DB::table('admin_user_token')->where('token', $admin_token)->where('expires_at', '>', date('Y-m-d H:i:s'))->first();

        if (isset($masterloging) && !empty($masterloging)) {
            $asscoAccounts = AdminUser::where('id' , $masterloging->admin_id )->first();

            if( !is_null($asscoAccounts->assignedAccounts) ){
                $accounts = Accounts::whereIn('module_id',json_decode($asscoAccounts->assignedAccounts))
                ->get();
            }

            return response([
                'accounts' => $accounts,
            ], 200);
        }
        else{
            return response([
                'message' => "Something Went Wrong!",
            ], 400);
        }

    }

    function loggedin_data(Request $request)
    {

        try {
            $admin_token = isset($_REQUEST['AuthTokenAdminUser']) ? $_REQUEST['AuthTokenAdminUser'] : "" ;
            $masterloging = DB::table('admin_user_token')->where('token', $admin_token)->where('expires_at', '>', date('Y-m-d H:i:s'))->first();

            $setting = DB::table('setting')->first();

            if (isset($masterloging->id)) {

                $user = DB::table('admin_users')->where('id', $masterloging->admin_id)->first();
                return response([
                    'message' => "Successfully Login",
                    'token' => $masterloging->token,
                    'user' => $user,
                    'setting' => $setting,
                    'expires_at' => $masterloging->expires_at,
                    'server_time_at_now' => date('Y-m-d H:i:s'),
                ], 200);
            } else {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired',
                    'server_time_at_now' => date('Y-m-d H:i:s'),

                ], 400);
            }
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage(),
                'ball' => 'chater problem'
            ], 400);
        }
    }

    function attempt_login_client(Request $request)
    {
        try {
            $admin_token = isset($_REQUEST['AuthTokenAdminUser']) ? $_REQUEST['AuthTokenAdminUser'] : "" ;
            $masterloging = DB::table('admin_user_token')->where('token', $admin_token)->where('expires_at', '>', date('Y-m-d H:i:s'))->first();

            if (empty($masterloging)) {
                return response([
                    'message' => "Token Expired"
                ], 400);
            }
            
            $module_id = $request->module_id;
            $contact = DB::table('zc_contacts')->where('Portal_Status', 'Active')->where("module_id", $module_id)->first();
            if (isset($contact->id)) {
                Auth::logout();

                $user = User::where('contact_id', $module_id)->first();
                Auth::login($user);

                $token = $user->createToken('app')->accessToken;
                return response([
                    'message' => "Successfully Login",
                    'token' => $token,
                ], 200);
            } else {
                return response([
                    'message' => "Login failed",
                ], 400);
            }
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}