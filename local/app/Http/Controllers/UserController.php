<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use App\Models\Nzoho;
use App\Models\Common;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    public function user()
    {
        $user = Auth::user();
        $contact_id = $user->contact_id;
        $contact_info  = Contacts::where('module_id', $contact_id)->first();
        $User_Role_ID = Auth::user()->portal_layout_role;
        if(!isset($User_Role_ID)){
          $User_Role_ID = 1;
        }
        $template_setting = [];
        if (Schema::hasTable('template_setting')) $template_setting = DB::table('template_setting')->where('status', 1)->first();
        if (Schema::hasTable('setting')) {
            $setting = DB::table('setting')->first();
            $zh_subscriptions_p_option = DB::table('portal_options')->where('option_name', "zoho_subscriptions")->where('option_index', "zs_menu")->first();
            $zh_inventory_p_option = DB::table('portal_options')->where('option_name', "zoho_inventory")->where('option_index', "zi_menu")->first();
            $zh_project_p_option = DB::table('portal_options')->where('option_name', "zoho_projects")->where('option_index', "zp_menu")->first();
            $zh_desk_p_option = DB::table('portal_options')->where('option_name', "zoho_desks")->where('option_index', "zd_menu")->first();
            $zh_vaults_p_option = DB::table('portal_options')->where('option_name', "zoho_vaults")->where('option_index', "zv_menu")->first();
            $zh_workDrive_p_option = DB::table('portal_options')->where('option_name', "zoho_work_drive")->where('option_index', "zwd_menu")->first();
        }

        $z_option['Subscription'] = json_decode($zh_subscriptions_p_option->option_value, true);
        $z_option['inventory'] = json_decode($zh_inventory_p_option->option_value, true);
        $z_option['project'] = json_decode($zh_project_p_option->option_value, true);
        $z_option['desk'] = json_decode($zh_desk_p_option->option_value, true);
        $z_option['vault'] = json_decode($zh_vaults_p_option->option_value, true);
        $z_option['workDrive'] = json_decode($zh_workDrive_p_option->option_value, true);

        $common = new Common();
        $getModulesArray = $this->permission_user_Module($User_Role_ID);

        $module_listName = array();

        $hasActivitis = false;

        foreach ($getModulesArray as $m_key => $m_value) {
            $modulePermission = $common->getModulePermission($m_value['api_name']);
            
            if (($modulePermission->view == 0) && ($modulePermission->create == 0 )&& ($modulePermission->edit == 0) && ($modulePermission->delete == 0)) continue;
            $view_id_m = $common->getDefaultView($m_value['api_name']);
            $m_url = ($view_id_m) ? ("module-list/" . $m_value['api_name'] . "?view_id=") . $view_id_m : ("module-list/" . $m_value['api_name']);
            $module_listName[] = [
                "url" => $m_url,
                "name" => $m_value['api_name'],
                "display_name" => $common->getModuleLabelName($m_value['api_name']),
            ];

            if ($m_value['api_name'] == "Tasks" || $m_value['api_name'] == "Events" || $m_value['api_name'] == "Calls" ) {
                $hasActivitis = true;
            }
        }

        return response([
            'message' => "",
            'user' => $user,
            'logged_contact' => $contact_info,
            'template_setting' => $template_setting,
            'setting' => $setting,
            'z_option' => $z_option,
            'module_listName' => $module_listName,
            'hasActivitis' => $hasActivitis,
        ], 200);
    }

    public function permission_user_Module($id)
    {
        try {

            $var = array();
            $common = new Common();
            $modules_array = $common->getModulesArrayByuserID($id);

            foreach ($modules_array as $key => $value) {
                $var[] = [
                    "module_name" => $value->module_id,
                    "api_name" => $value->module_id
                ];
            }
            return $var;

            return response([
                'data' => $var
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }
    /**
     * logged_in_contact
     * get logged in contact info
     * 
     * @return json
     */
    public function logged_in_contact()
    {
        try {
            $contact_id = Auth::user()->contact_id;
            $contact_info  = Contacts::where('module_id', $contact_id)->first();
            $twofactor = DB::table('verification_code_send')->where('contact_id', $contact_id)->first();
            $twofactorActive = DB::table('verification_code_send')->where('contact_id', $contact_id)->where('is_active', 1)->first();

            return response([
                'message' => "",
                'contact' => $contact_info,
                'twofactor' => $twofactor,
                'twofactorActive' => $twofactorActive,
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * save_contact
     * save contact info to local & zoho server
     * 
     * @param Request $request
     * @return json 
     */
    public function save_contact(Request $request)
    {
        try {
            $id = $request->module_id;
            $data = $request->except(['module_id', 'Email', 'AuthToken']);
            $data['Full_Name'] = $data['First_Name'] . " " . $data["Last_Name"];
            $data['Phone'] = str_replace("_", "",$data['Phone']);
            //save to local db
            DB::table('zc_contacts')->where('module_id', $id)->update($data);

            //save to zoho
            $zoho = new Nzoho();
            $zohoData[0] = $data;

            if (Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if (isset($setting->want_crm) && ($setting->want_crm == "Yes")) $updateZoho = $zoho->updateRecords($id, $zohoData, 'Contacts');

            if (isset($setting->want_crm) && ($setting->want_crm == "Yes")) {
                if (isset($updateZoho->data[0]->code) && $updateZoho->data[0]->code == 'SUCCESS') {
                    $msg = "Data saved successfully.";
                    $status_code = 200;
                    Common::insertActivityLog("Contact Info.", "Update", json_encode($data));
                } else {
                    $status_code = 401;
                    $msg = (isset($updateZoho->data[0]->message)) ? $updateZoho->data[0]->message : 'Something wrong! Please try again.';
                }
            } else {
                $msg = "Data saved successfully.";
                $status_code = 200;
            }
            $contact_info  = Contacts::where('module_id', $id)->first();

            return response([
                'message' => $msg,
                'contact' => $contact_info,
            ], $status_code);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}