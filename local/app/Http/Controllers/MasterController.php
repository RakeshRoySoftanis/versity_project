<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contacts;
use App\Models\Master;
use App\Models\User;
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
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class MasterController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Client Functions
    |--------------------------------------------------------------------------
    */
    function portal_client()
    {
        // try{
        if (isset($_REQUEST['AuthToken'])) {
            $AuthToken = $_REQUEST['AuthToken'];
        } else {
            $AuthToken = '';
        }
        if (!Master::check_master_token($AuthToken)) {
            return response([
                'message' => "Token Expired",
                'status' => 'TokenExpired'
            ], 400);
        }

        $offset = ($_GET['page'] - 1) * $_GET['per_page'];
        $sort_field = (isset($_GET['sort_field'])) ? $_GET['sort_field'] : "id";
        $sort_order = (isset($_GET['sort_order'])) ? $_GET['sort_order'] : "desc";

        $data = array();
        $data_count = 0;

        if (Schema::hasTable('zc_contacts')) {
            $sql = DB::table('zc_contacts')->where('Portal_Status', 'Active');
            //search conditions
            if (isset($_GET['search']) && ($_GET['search'] != "")) {
                $q = $_GET['search'];
                $sql->where(function ($query) use ($q) {
                    $query->where('module_id', 'LIKE', '%' . $q . '%')
                        ->orWhere('Account_Name', 'LIKE', '%' . $q . '%')
                        ->orWhere('Full_Name', 'LIKE', '%' . $q . '%')
                        ->orWhere('Email', 'LIKE', '%' . $q . '%');
                });
            }
            $data = $sql->offset($offset)->limit($_GET['per_page'])->orderBy($sort_field, $sort_order)->get();

            $newdata = array();
            foreach ($data as $key => $value) {
                $single_row_data = array();

                $layout_role_name = DB::table('users')->where('contact_id', $value->module_id)->first();

                $single_row_data = array(
                    "module_id" => $value->module_id,
                    "Full_Name" => $value->Full_Name,
                    "Account_Name" => $value->Account_Name,
                    "Email" => $value->Email,
                    "Phone" => $value->Phone,
                    "portal_layout_role_name" => isset($layout_role_name->id) ? $layout_role_name->portal_layout_role_name : '',
                    "portal_layout_role" => isset($layout_role_name->id) ? $layout_role_name->portal_layout_role : '',

                );

                $newdata[] = $single_row_data;
            }

            //count of total tasks
            $csql = DB::table('zc_contacts')->where('Portal_Status', 'Active');
            //search conditions for count
            if (isset($_GET['search']) && ($_GET['search'] != "")) {
                $q = $_GET['search'];
                $csql->where(function ($query) use ($q) {
                    $query->where('module_id', 'LIKE', '%' . $q . '%')
                        ->orWhere('Account_Name', 'LIKE', '%' . $q . '%')
                        ->orWhere('Full_Name', 'LIKE', '%' . $q . '%')
                        ->orWhere('Email', 'LIKE', '%' . $q . '%');
                });
            }
            $data_count = $csql->count();
        }

        //response
        return response([
            'message' => "",
            'pgData' => $newdata,
            'totalRows' => $data_count,
        ], 200);

        // }catch(Exception $e){
        //     return response([
        //         'message' => $e->getMessage()
        //     ], 400);
        // }
    }

    function attempt_login_client(Request $request)
    {
        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
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

    function master_assign_vaults($id)
    {

        try {

            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }


            $zv = array();
            if (\Schema::hasTable('zc_contacts')) $zv["contacts"] = $crm_con = DB::table('zc_contacts')->where('module_id', $id)->first();
            if (!isset($crm_con->Account_Name_ID) || ($crm_con->Account_Name_ID == "")) {

                return response([
                    'message' => "Account not associate with this contacts."
                ], 400);
            }

            $zv['z_contacts_id'] = $id;
            $zv['z_accounts_id'] = $user_id = $crm_con->Account_Name_ID;
            if (\Schema::hasTable('zv_chambers')) $zv['chamberList'] = DB::table('zv_chambers')->get();
            if (\Schema::hasTable('zv_assign_vaults')) $zv['assign_vaults'] = DB::table('zv_assign_vaults')->where('user_id', $user_id)->first();


            $z_contacts_id = isset($zv['z_contacts_id']) ? $zv['z_contacts_id'] : "";
            $z_accounts_id = isset($zv['z_accounts_id']) ? $zv['z_accounts_id'] : "";
            $chamberList = isset($zv['chamberList']) ? $zv['chamberList'] : array();
            $assign_vaults = isset($zv['assign_vaults']) ? $zv['assign_vaults'] : array();

            $userScList = array();
            $userChList = array();


            $usersecretsArr = (isset($assign_vaults->id)) ? json_decode($assign_vaults->secrets, true) : array();
            if (isset($usersecretsArr) && (count($usersecretsArr) > 0)) {
                foreach ($usersecretsArr as $key => $value) {

                    $jsAr = explode("_", $value);
                    $userChList[] = $jsAr[0];
                    $userScList[] = $jsAr[1];
                }
            }

            $uchambersArr = isset($assign_vaults->chambers) ? json_decode($assign_vaults->chambers, true) : array();


            return response([
                'message' => "",
                'zv' => $zv,
                'userScList' => $userScList,
                'uchambersArr' => $uchambersArr
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }


    function save_assign_vaults(Request $request)
    {

        try {

            $data['user_id'] = $user_id = $request->user_id;
            $data['chambers'] = !empty($request->chambers) ? json_encode($request->chambers) : json_encode(array());
            $data['secrets'] =  !empty($request->secrets) ? json_encode($request->secrets) : json_encode(array());

            if (\Schema::hasTable('zv_assign_vaults')) $ck_assign = DB::table('zv_assign_vaults')->where('user_id', $user_id)->first();
            if (isset($ck_assign->id)) {
                $data['updated_at'] = date('Y-m-d H:i:s');
                DB::table('zv_assign_vaults')->where('id', $ck_assign->id)->update($data);
                $msg = "Updated Successfully";
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                DB::table('zv_assign_vaults')->insert($data);
                $msg = "Inserted Successfully";
            }

            return response([
                'message' => $msg
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | User Functions
    |--------------------------------------------------------------------------
    */


    public function userLists()
    {

        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            $offset = ($_GET['page'] - 1) * $_GET['per_page'];
            $sort_field = (isset($_GET['sort_field'])) ? $_GET['sort_field'] : "id";
            $sort_order = (isset($_GET['sort_order'])) ? $_GET['sort_order'] : "desc";

            $data = array();
            $data_count = 0;

            if (Schema::hasTable('admin_users')) {
                $sql = DB::table('admin_users');
                //search conditions
                if (isset($_GET['search']) && ($_GET['search'] != "")) {
                    $q = $_GET['search'];
                    $sql->where(function ($query) use ($q) {
                        $query->where('name', 'LIKE', '%' . $q . '%')
                            ->orWhere('email', 'LIKE', '%' . $q . '%')
                            ->orWhere('phone', 'LIKE', '%' . $q . '%');
                    });
                }
                $data = $sql->offset($offset)->limit($_GET['per_page'])->orderBy($sort_field, $sort_order)->get();

                //count of total tasks
                $csql = DB::table('admin_users');
                //search conditions for count
                if (isset($_GET['search']) && ($_GET['search'] != "")) {
                    $q = $_GET['search'];
                    $csql->where(function ($query) use ($q) {
                        $query->where('name', 'LIKE', '%' . $q . '%')
                            ->orWhere('email', 'LIKE', '%' . $q . '%')
                            ->orWhere('phone', 'LIKE', '%' . $q . '%');
                    });
                }
                $data_count = $csql->count();
            }

            //response
            return response([
                'message' => "",
                'pgData' => $data,
                'totalRows' => $data_count,
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | All saved layouts 
    |--------------------------------------------------------------------------
    */
    public function all_layouts()
    {

        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            $offset = ($_GET['page'] - 1) * $_GET['per_page'];
            $sort_field = (isset($_GET['sort_field'])) ? $_GET['sort_field'] : "id";
            $sort_order = (isset($_GET['sort_order'])) ? $_GET['sort_order'] : "desc";

            $data = array();
            $data_count = 0;

            if (Schema::hasTable('layout_setting')) {
                $sql = DB::table('layout_setting')
                        ->join('portal_user_role', 'portal_user_role.id', '=', 'layout_setting.user_role_id')
                        ->join('module_list', 'module_list.api_name', '=', 'layout_setting.module_api_name')
                        ->select('layout_setting.*', 'portal_user_role.name', 'module_list.singular_label');
                //search conditions  portal_user_role  user_role_id
                if (isset($_GET['search']) && ($_GET['search'] != "")) {
                    $q = $_GET['search'];
                    $sql->where(function ($query) use ($q) {
                        $query->where('module_api_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('layout_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('user_role_id', 'LIKE', '%' . $q . '%');
                    });
                }
                $data = $sql->offset($offset)->limit($_GET['per_page'])->orderBy('layout_setting.'.$sort_field, $sort_order)->get();

                //return $data;

                //count of total tasks
                $csql = DB::table('layout_setting');
                //search conditions for count
                if (isset($_GET['search']) && ($_GET['search'] != "")) {
                    $q = $_GET['search'];
                    $csql->where(function ($query) use ($q) {
                        $query->where('module_api_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('layout_name', 'LIKE', '%' . $q . '%')
                            ->orWhere('user_role_id', 'LIKE', '%' . $q . '%');
                    });
                }
                $data_count = $csql->count();
            }

            //response
            return response([
                'message' => "",
                'pgData' => $data,
                'totalRows' => $data_count,
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function save_master_user(Request $request)
    {
        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }


            $assignedAccounts = json_encode($request->accounts);

            $adminUser = AdminUser::findOrFail($request->id);
            $adminUser->assignedAccounts = ($assignedAccounts == "null") ? NULL : $assignedAccounts;
            $adminUser->save();

            return response([
                'message' => "Account Assigned Successfully.",
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function edit_master_user()
    {
        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired"
                ], 400);
            }

            $zoho_auth = array();
            if (Schema::hasTable('admin_users')) {

                if (isset($_GET['id'])) {
                    $users = DB::table('admin_users')->where('id', $_GET['id'])->first();
                }

                return response([
                    'message' => "",
                    'pgData' => $users
                ], 200);
            }
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update_master_user(Request $request)
    {
        try {

            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            //type code here

            $data = $request->all();
            $data['password'] = Hash::make($request->password);
            unset($data['AuthToken']);
            unset($data['AuthTokenAdminUser']);

            DB::table("admin_users")->where("id" , $request->id )->update($data);

            return response([
                'message' => "Successfull",
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function insert_master_user(Request $request)
    {
        try {

            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            $validated = $request->validate([
                'email' => 'required|unique:admin_users',
                'password' => 'required|min:6',
            ]);

            //type code here



            $data = $request->all();
            unset($data['AuthToken']);
            unset($data['AuthTokenAdminUser']);
            $data['password'] = Hash::make($request->password);

            DB::table("admin_users")->insert($data);

            return response([
                'message' => "Successfull",
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => "Email should be unique and password should at least 6 character"
            ], 400);
        }
    }

    public function delete_master_user(Request $request)
    {
        try {

            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            $id = $_GET['id'];

            DB::table("admin_users")->where('id' , $id)->delete();

            //type code here

            return response([
                'message' => "Successfull",
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function delete_layout(Request $request)
    {
        try {

            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            $id = $_GET['id'];

            DB::table('layout_setting')->where("id",$id)->delete();

            //type code here

            return response([
                'message' => "Layout Successfully Deleted",
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function userAssignAccount(Request $request)
    {
        try {

            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            $id = $_GET['id'];

            $accounts =  DB::table('zc_accounts')->get();
            $userData = DB::table('admin_users')->where('id', $id)->first();
            $assignedAccounts = json_decode($userData->assignedAccounts);
            if (is_null($assignedAccounts)) {
                $assignedAccounts = array();
            }

            return response([
                'message' => "Successfull",
                'accounts' => $accounts,
                'userData' => $userData,
                'alreadyAssigned' => $assignedAccounts
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Settings Functions
    |--------------------------------------------------------------------------
    */

    function Setting()
    {

        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            $Mainprofile = DB::table('setting')->first();
            $q_title = json_decode($Mainprofile->quick_title);
            $q_link = json_decode($Mainprofile->quick_link);

            foreach ($q_title as $key => $value) {
                if ($value) {
                    $quick_title[] = $value;
                    $quick_link[] = $q_link[$key];
                }
            }

            $zoho_auth = array();
            if (\Schema::hasTable('zoho_auth')) $zoho_auth = DB::table('zoho_auth')->orderBy('id', 'DESC')->first();

            return response([
                'message' => "Successfull",
                'logo' => $Mainprofile,
                'quick_title' => $quick_title,
                'quick_link' => $quick_link,
                'phoneNo' => (int) str_replace("-", "", $Mainprofile->phone),
                'zoho_auth' => $zoho_auth,
                'base_url' => url("/") . "/public",
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    function updateSetting(Request $request)
    {
        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            $data = [];
            $data['name'] = $request->name;
            $data['phone'] = $request->phone;
            $data['fax'] = $request->fax;
            $data['email'] = $request->email;
            $data['website'] = $request->website;
            $data['youtubelink'] = $request->youtubelink;
            $data['client_dashboard'] = $request->client_dashboard;

            $data['quick_title'] = json_encode($request->quick_title);
            $data['quick_link'] = json_encode($request->quick_link);

            $data['street'] = $request->street;
            $data['city'] = $request->city;
            $data['state'] = $request->state;
            $data['zip'] = $request->zip;
            $data['country'] = $request->country;

            //echo "<pre>";print_r($data);exit();

            $checkExistsUser = DB::table('setting')->first();

            if (isset($checkExistsUser)) {
                DB::table('setting')->where('id', $checkExistsUser->id)->update($data);
                $err = "Update";
            } else {

                DB::table('setting')->insert($data);
                $err = "Insert";
            }

            return response([
                'message' => "Settings " . $err . " Successfull"
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    function setting_logo(Request $request)
    {
        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }


            $data = [];
            $file = $request->file('logo');

            if ($file != "") {
                $destinationPath = public_path('psettings/logo');
                if (!is_dir($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $upc_o = $file->getClientOriginalName();
                $upc = preg_replace("/[^A-Z0-9._-]/i", "_", $upc_o);

                $upcPath = public_path('psettings/logo/' . $upc);

                if (!file_exists($upcPath)) {
                    $file->move($destinationPath, $upc);
                }
                $data['logo'] = $upc;
            }


            $fileM = $request->file('minilogo');
            if ($fileM != "") {
                $destinationPathM = public_path('psettings/logo');
                if (!is_dir($destinationPathM)) {
                    mkdir($destinationPathM, 0777, true);
                }
                $upc_oM = $fileM->getClientOriginalName();
                $upcM = preg_replace("/[^A-Z0-9._-]/i", "_", $upc_oM);

                $upcPathM = 'psettings/logo/' . $upcM;

                if (!file_exists($upcPathM)) {
                    $fileM->move($destinationPathM, $upcM);
                }

                $data['minilogo'] = $upcM;
            }

            $data['logo_default'] = 0;
            if (isset($request->logo_default)) {
                $data['logo_default'] = 1;
            }

            $checkExistsUser = DB::table('setting')->first();

            if (isset($checkExistsUser)) {
                DB::table('setting')->where('id', $checkExistsUser->id)->update($data);
                $msg = "Updated";
            } else {

                DB::table('setting')->insert($data);
                $msg = "Inserted";
            }

            return response([
                'message' => "Successfully" . $msg
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    function setting_color(Request $request)
    {
        $data = [];

        $data['head'] = ($request->headerColor != '#000000') ? $request->headerColor : 'linear-gradient(45deg, #59a3e8 0%, #646cd2 100%)';
        $data['nav'] = ($request->menubarcolor != '#000000') ? $request->menubarcolor : '#f8f9fd9e';
        $data['font'] = $request->fontcolor;
        $data['heading'] = $request->headingcolor;
        $data['footer'] = ($request->footer != '#000000') ? $request->footer : 'linear-gradient(45deg, #59a3e8 0%, #646cd2 100%)';
        $data['deafult'] = 0;
        if (isset($request->deafult)) {
            $data['deafult'] = 1;

            // $data['head'] = "linear-gradient(45deg, #59a3e8 0%, #646cd2 100%)";
            // $data['nav'] = "#f8f9fd9e";
            // $data['font'] = "#212529";
            // $data['heading'] = "#03adea";

            $data['head'] = "linear-gradient(45deg, #59a3e8 0%, #646cd2 100%)";
            $data['nav'] = "#f8f9fd9e";
            $data['font'] = "#1F2537";
            $data['heading'] = "#646cd2";
            $data['footer'] = "linear-gradient(45deg, #59a3e8 0%, #646cd2 100%)";
        }

        $checkExistsUser = DB::table('setting')->first();

        if (isset($checkExistsUser)) {
            DB::table('setting')->where('id', $checkExistsUser->id)->update($data);
            $msg = "Updated";
        } else {

            DB::table('setting')->insert($data);
            $msg = "Inserted";
        }

        return response([
            'message' => "Successfully " . $msg
        ], 200);
    }

    //setup twilio

    function setup_twilio()
    {

        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            if (\Schema::hasTable('twillio_setting')) $twilioData = DB::table('twillio_setting')->get()->first();

            return response([
                'message' => "Successfull",
                'td' => $twilioData,
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function setup_twilio_save(Request $request)
    {

        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            $data = array(
                'number' => $request->number,
                'sid' => $request->sid,
                'TwiML_Apps_SID' => $request->app_sid,
                'token' => $request->token
            );


            DB::table('twillio_setting')->truncate();
            DB::table('twillio_setting')->insert($data);

            return response([
                'message' => "Successfull Updated",
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // google api

    public function setup_google_api()
    {

        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            $googleData = DB::table('google_api_setting')->get()->first();
            $moduleList = DB::table('module_list')->get();

            $google_fields_setting = DB::table('google_fields_setting')->get()->first();
            $zohoFields = array();
            $selected_module = '';
            $selected_street = '';
            if (isset($google_fields_setting)) {
                $selected_module = $google_fields_setting->module;
                $zohoFields = DB::table('zohofields')->where('module', $selected_module)->get();
                $selected_street = DB::table('google_fields_setting')->where('google_fields', 'Street')->first();
                $selected_City = DB::table('google_fields_setting')->where('google_fields', 'City')->first();
                $selected_State = DB::table('google_fields_setting')->where('google_fields', 'State')->first();
                $selected_Zip_Code = DB::table('google_fields_setting')->where('google_fields', 'Zip_Code')->first();
                $selected_County = DB::table('google_fields_setting')->where('google_fields', 'County')->first();
                $selected_Country = DB::table('google_fields_setting')->where('google_fields', 'Country')->first();
            }

            return response([
                'googleData' => $googleData,
                'moduleList' => $moduleList,
                'selected_module' => $selected_module,
                'google_fields_setting' => $google_fields_setting,
                'zohoFields' => $zohoFields,
                'selected_street' => $selected_street,
                'selected_City' => $selected_City,
                'selected_State' => $selected_State,
                'selected_Zip_Code' => $selected_Zip_Code,
                'selected_County' => $selected_County,
                'selected_Country' => $selected_Country,
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function setup_google_save(Request $request)
    {

        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }


            $data = array(
                'api_key' => $request->api_key,
            );

            DB::table('google_api_setting')->truncate();
            DB::table('google_api_setting')->insert($data);

            return response([
                'message' => "Google API setup successfully."
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function google_field_setup(Request $request)
    {

        try {
            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            if (!Master::check_master_token($AuthToken)) {
                return response([
                    'message' => "Token Expired",
                    'status' => 'TokenExpired'
                ], 400);
            }

            $module = $request->module;
            if (isset($request->fields_name) && count($request->fields_name) > 0) {
                DB::table('google_fields_setting')->truncate();
                for ($i = 0; $i < count($request->fields_name); $i++) {
                    $google_fields = '';
                    if ($i == 0) {
                        $google_fields = "Street";
                    } elseif ($i == 1) {
                        $google_fields = "City";
                    } elseif ($i == 2) {
                        $google_fields = "State";
                    } elseif ($i == 3) {
                        $google_fields = "Zip_Code";
                    } elseif ($i == 4) {
                        $google_fields = "County";
                    } elseif ($i == 5) {
                        $google_fields = "Country";
                    }
                    $data = array(
                        'module'        => $module,
                        'google_fields' => $google_fields,
                        'zoho_fields'   => $request->fields_name[$i],
                    );

                    DB::table('google_fields_setting')->insert($data);
                }
            }

            return response([
                'message' => "Google field map setup successfully."
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }

        return redirect('google-api-setup')->with('success', 'Google field map setup successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | Master After Login Functions
    |--------------------------------------------------------------------------
    */
    function loggedin_data()
    {


        try {

            if (isset($_REQUEST['AuthToken'])) {
                $AuthToken = $_REQUEST['AuthToken'];
            } else {
                $AuthToken = '';
            }
            
            $masterloging = DB::table('master_token')->select('master.id', 'master.fname', 'master.lname', 'master.email', 'master_token.token', 'master_token.expires_at')->join("master", "master.id", '=', 'master_token.master_id')->where('token', $AuthToken)->where('expires_at', '>', date('Y-m-d H:i:s'))->first();
            $setting = DB::table('setting')->first();

            if (isset($masterloging->id)) {
                return response([
                    'message' => "Successfully Login",
                    'token' => $masterloging->token,
                    'user' => $masterloging,
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

    /*
    |--------------------------------------------------------------------------
    | Master Login Functions
    |--------------------------------------------------------------------------
    */
    public function login(Request $request)
    {
        try {
            $masterToken = Str::random(128);
            $email = trim($request->email);
            $password = $request->password;

            $result = DB::table('master')->where('email', $email)->where('password', md5($password))->first();
            if (isset($result->id)) {
                $value = array(
                    'id' => $result->id,
                    'name' => $result->fname . ' ' . $result->lname,
                    'token' => $masterToken
                );
                DB::table('master_token')->where('expires_at', '<', date('Y-m-d H:i:s'))->delete();
                DB::table('master_token')->insert(array('token' => $masterToken, 'master_id' => $result->id, 'expires_at' => date('Y-m-d H:i:s', strtotime("+1day"))));
                return response([
                    'message' => "Successfully Login",
                    'token' => $masterToken,
                    'user' => $value,
                ], 200);
            } else {
                return response([
                    'message' => "Invalid Email or Password."
                ], 401);
            }
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }


    public function FormElement(Request $request)
    {
        try {

            return $request->all();
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function selectOptions(Request $request, $page, $search)
    {
        try {
            $var = array();

            // select2
            $offset = ($page - 1) * 10;

            if ($search != "none") {
                $zohofields = DB::table('zohofields')->where('display_label', 'like', '%' . $search . '%')->offset($offset)->limit(10)->get();
            } else {
                $zohofields = DB::table('zohofields')->offset($offset)->limit(10)->get();
            }

            // charts

            $data = [
                "labels" => ["Janu", "Feb", "Mar", "April", "May"],
                "data" => [12, 19, 3, 5, 2, 3]
            ];

            foreach ($zohofields as $key => $value) {
                $var[] = [
                    "value" => $value->field_id . "-" . $value->id,
                    "label" => $value->display_label . "-" . $value->id
                ];
            }

            return response([
                'data' => $var,
                'page' => $page + 1,
                'charts' => $data,
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

}
