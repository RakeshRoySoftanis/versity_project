<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;


use App\Models\Nzoho;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class Common extends Model
{
  public static function getAddressInfo($module = '', $google_Filed_name = '')
  {
    $contact_id = Auth::user()->contact_id;
    $profile  = Contacts::where('module_id', $contact_id)->first();
    $fields = DB::table('google_fields_setting')->select('*')->where('google_fields', $google_Filed_name)->where('module', $module)->first();
    if (isset($fields->zoho_fields)) {

      if ($module == "Accounts") {
        $module_id = $profile->Account_Name_ID;
      } else {
        $module_id = $contact_id;
      }

      $module = strtolower($module);
      $table_name = "zc_" . $module;
      $zoho_fields = $fields->zoho_fields;
      $AddressInformation = DB::table($table_name)->where('module_id', $module_id)->first();
      return isset($AddressInformation->$zoho_fields) ? $AddressInformation->$zoho_fields : false;
    } else {
      return false;
    }
  }


  public static function insertActivityLog($page = '', $action = '', $message = '')
  {
    $contact_id = Auth::user()->contact_id;
    $insertArr = array();
    $insertArr['page'] = $page;
    $insertArr['action'] = $action;
    $insertArr['message'] = $message;

    $contacts   = Contacts::where('module_id', $contact_id)->first();
    $ACCOUNTID  = $contacts->Account_Name_ID;
    $full_name = $contacts->Full_Name;

    $insertArr['ACCOUNTID'] = $ACCOUNTID;
    $insertArr['CONTACTID'] = $contact_id;
    $insertArr['modified_by'] = $full_name;

    DB::table('portal_activity_log')->insert($insertArr);
    return true;
  }

  public static function getActiveTheme()
  {
    $ActivateTheme = DB::table('template_setting')->select('*')->where('status', 1)->first();
    if (isset($ActivateTheme->template_api_name)) {
      return $ActivateTheme->template_api_name;
    } else {
      return false;
    }
  }


  public static function getPortalLayoutNameByID($user_role_id = '', $module = '', $layout_id = '')
  {
    $layout_setting = DB::table('layout_setting')->where('module_api_name', $module)->where('user_role_id', $user_role_id)->where('layout_id', $layout_id)->first();
    if ($layout_setting) {
      return $layout_setting->layout_name;
    } else {
      return false;
    }
  }

  function send_mail_to_portal_credential($data, $path)
  {

    $domain_email = "no-replay@" . $_SERVER['SERVER_NAME'];
    $headers  = "From: Client Portal < " . $domain_email . " >\n";
    $headers .= "X-Sender: Client Portal < " . $domain_email . " >\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();
    $headers .= "X-Priority: 1\n"; // Urgent message!
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=iso-8859-1\n";

    $first_name = $data['First_Name'];
    $email = $data['Email'];
    $password = $data['Password'];
    $subject = 'Portal Credential';
    $msg = '<style type="text/css">
                  body{
                    background-color: #e8e6e6;
                    margin: 0;
                  }
                </style>
                <div style="background-color: #DFE3E8; padding: 50px 20px;">
                  <div style="margin: 0px auto; width: 640px; background-color:#fff;padding: 1px;">
                    <table style="font-size: 11px; color: rgb(0, 0, 0);padding: 30px;" cellpadding="" cellspacing="0" border="0" width="100%">
                      <tbody>
                        <tr>
                          <td style="text-align: center;font-size: 25px;font-weight: bold;">Welcome to Client Portal</td>
                        </tr>
                        <tr><td style="width: 100%;height: 25px">&nbsp;</td></tr>
                        <tr>
                          <td style="text-align: left;"> 
                            <p style="font-size: 12pt; color: #414141;margin: 0;margin-top: 10px;">Hello ' . $first_name . '</p>
                          </td>
                        </tr>
                        <tr><td style="width: 100%;height: 10px">&nbsp;</td></tr>
                        <tr>
                          <td style="text-align: left;"> 
                            <p style="font-size: 11pt; color: #414141;margin: 0;margin-top: 10px;">
                              Your Portal Access Credential : <br>
                              User Name: ' . $email . '<br>
                              Password: ' . $password . '<br><br><br>
                            </p>
                          </td>
                        </tr>
                        <tr>
                          <td style="text-align: center;font-size: 11pt;"> 
                            <a href="' . ('' . $path . '/login') . '" style="color: #fff;text-decoration: none;background: #1155CC;padding: 10px 30px;">Click Here</a><br><br><br>
                          </td>
                        </tr>
                        <tr>
                          <td style="text-align: left;"> 
                            <p style="font-size: 11pt; color: #414141;margin: 0;margin-top: 10px;">Thank you,<br><b>Admin</b> 
                            </p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>';
    mail($email, $subject, $msg, $headers);
  }


  function send_mail_to_portal_credential_localhost($data, $path)
  {

    $first_name = $data['First_Name'];
    $email = $data['Email'];
    $password = $data['Password'];
    $subject = 'Portal Credential';
    $msg = '<style type="text/css">
                  body{
                    background-color: #e8e6e6;
                    margin: 0;
                  }
                </style>
                <div style="background-color: #DFE3E8; padding: 50px 20px;">
                  <div style="margin: 0px auto; width: 640px; background-color:#fff;padding: 1px;">
                    <table style="font-size: 11px; color: rgb(0, 0, 0);padding: 30px;" cellpadding="" cellspacing="0" border="0" width="100%">
                      <tbody>
                        <tr>
                          <td style="text-align: center;font-size: 25px;font-weight: bold;">Welcome to Client Portal</td>
                        </tr>
                        <tr><td style="width: 100%;height: 25px">&nbsp;</td></tr>
                        <tr>
                          <td style="text-align: left;"> 
                            <p style="font-size: 12pt; color: #414141;margin: 0;margin-top: 10px;">Hello ' . $first_name . '</p>
                          </td>
                        </tr>
                        <tr><td style="width: 100%;height: 10px">&nbsp;</td></tr>
                        <tr>
                          <td style="text-align: left;"> 
                            <p style="font-size: 11pt; color: #414141;margin: 0;margin-top: 10px;">
                              Your Portal Access Credential : <br>
                              User Name: ' . $email . '<br>
                              Password: ' . $password . '<br><br><br>
                            </p>
                          </td>
                        </tr>
                        <tr>
                          <td style="text-align: center;font-size: 11pt;"> 
                            <a href="' . ('' . $path . '/login') . '" style="color: #fff;text-decoration: none;background: #1155CC;padding: 10px 30px;">Click Here</a><br><br><br>
                          </td>
                        </tr>
                        <tr>
                          <td style="text-align: left;"> 
                            <p style="font-size: 11pt; color: #414141;margin: 0;margin-top: 10px;">Thank you,<br><b>Admin</b> 
                            </p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>';

    return $msg;
  }

  function send_mail_to_reset_link($data, $path)
  {
    $token = $data['Token'];
    $email = $data['Email'];
    $first_name = $data['First_Name'];
    $subject = 'Reset Password';

    $msg = '<style type="text/css">
                  body{
                    background-color: #e8e6e6;
                    margin: 0;
                  }
                </style>
                <div style="background-color: #DFE3E8; padding: 50px 20px;">
                  <div style="margin: 0px auto; width: 640px; background-color:#fff;padding: 1px;">
                    <table style="font-size: 11px; color: rgb(0, 0, 0);padding: 30px;" cellpadding="" cellspacing="0" border="0" width="100%">
                      <tbody>
                        <tr>
                          <td style="text-align: center;font-size: 25px;font-weight: bold;">Reset Password</td>
                        </tr>
                        <tr><td style="width: 100%;height: 25px">&nbsp;</td></tr>
                        <tr>
                          <td style="text-align: left;"> 
                            <p style="font-size: 12pt; color: #414141;margin: 0;margin-top: 10px;">Hello ' . $first_name . '</p>
                          </td>
                        </tr>
                        <tr>
                          <td style="text-align: left;"> 
                            <p style="font-size: 11pt; color: #414141;margin: 0;margin-top: 10px;">
                              Click the button to reset your password.<br><br><br>
                            </p>
                          </td>
                        </tr>
                        <tr>
                          <td style="text-align: center;font-size: 11pt;"> 
                            <a href="' . ('' . $path . '/password-reset/' . $token) . '" style="color: #fff;text-decoration: none;background: #1155CC;padding: 10px 30px;">Click Here</a><br><br><br>
                          </td>
                        </tr>
                        <tr>
                          <td style="text-align: left;"> 
                            <p style="font-size: 11pt; color: #414141;margin: 0;margin-top: 10px;">Thank you,<br><b>Admin</b> 
                            </p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>';

    $domain_email = "no-replay@" . $_SERVER['SERVER_NAME'];
    $headers  = "From: Client Portal < " . $domain_email . " >\n";
    $headers .= "X-Sender: Client Portal < " . $domain_email . " >\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();
    $headers .= "X-Priority: 1\n"; // Urgent message!
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=iso-8859-1\n";

    mail($email, $subject, $msg, $headers);
  }


  function send_mail_to_reset_link_mailtrap($data, $path)
  {
    $token = $data['Token'];
    $email = $data['Email'];
    $first_name = $data['First_Name'];
    $subject = 'Reset Password';

    $msg = '<style type="text/css">
                  body{
                    background-color: #e8e6e6;
                    margin: 0;
                  }
                </style>
                <div style="background-color: #DFE3E8; padding: 50px 20px;">
                  <div style="margin: 0px auto; width: 640px; background-color:#fff;padding: 1px;">
                    <table style="font-size: 11px; color: rgb(0, 0, 0);padding: 30px;" cellpadding="" cellspacing="0" border="0" width="100%">
                      <tbody>
                        <tr>
                          <td style="text-align: center;font-size: 25px;font-weight: bold;">Reset Password</td>
                        </tr>
                        <tr><td style="width: 100%;height: 25px">&nbsp;</td></tr>
                        <tr>
                          <td style="text-align: left;"> 
                            <p style="font-size: 12pt; color: #414141;margin: 0;margin-top: 10px;">Hello ' . $first_name . '</p>
                          </td>
                        </tr>
                        <tr>
                          <td style="text-align: left;"> 
                            <p style="font-size: 11pt; color: #414141;margin: 0;margin-top: 10px;">
                              Click the button to reset your password.<br><br><br>
                            </p>
                          </td>
                        </tr>
                        <tr>
                          <td style="text-align: center;font-size: 11pt;"> 
                            <a href="' . ('' . $path . '/password-reset/' . $token) . '" style="color: #fff;text-decoration: none;background: #1155CC;padding: 10px 30px;">Click Here</a><br><br><br>
                          </td>
                        </tr>
                        <tr>
                          <td style="text-align: left;"> 
                            <p style="font-size: 11pt; color: #414141;margin: 0;margin-top: 10px;">Thank you,<br><b>Admin</b> 
                            </p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>';

    return $msg;

    // $domain_email = "no-replay@".$_SERVER['SERVER_NAME'];
    // $headers  = "From: Client Portal < ".$domain_email." >\n";
    // $headers .= "X-Sender: Client Portal < ".$domain_email." >\n";
    // $headers .= 'X-Mailer: PHP/' . phpversion();
    // $headers .= "X-Priority: 1\n"; // Urgent message!
    // $headers .= "MIME-Version: 1.0\r\n";
    // $headers .= "Content-Type: text/html; charset=iso-8859-1\n";

    // mail($email, $subject, $msg, $headers);
  }


  //sync crm data to local
  public function sync_getRecordsById($crmid, $module, $is_api_name = false)
  {
    set_time_limit(186400);
    $zoho = new Nzoho;

    if ($is_api_name == false) {
      $n_module = $this->getModuleApiName($module);
    } else {
      $n_module = $module;
    }

    $result = $zoho->getRecordsById($crmid, $n_module);

    if ($result['count'] > 0) {
      foreach ($result['data'] as $data) {
        $table_name = "zc_" . strtolower($n_module);
        if (Schema::hasTable($table_name)) {
          $this->saveDataToDb($table_name, $data);
          if ($module == "Contacts") {
            $this->SavePortalUserIngo($data);
          }
        }
      }
    }
  }

  public function SavePortalUserIngo($data)
  {
    if ((isset($data['User_Name'])) && ($data['User_Name'] != '') && (isset($data['Password'])) && ($data['Password'] != '')) {
      if ((isset($data['Portal_Status'])) && ($data['Portal_Status'] == 'Active')) {
        $checkExistsUser = DB::table('users')->where('contact_id', $data['id'])->first();
        $userArr = array();

        $userArr['contact_id'] = (isset($data['id'])) ? $data['id'] : '';
        $userArr['portal_user_name'] = (isset($data['User_Name'])) ? $data['User_Name'] : '';
        $userArr['first_name'] = (isset($data['First_Name'])) ? $data['First_Name'] : '';
        $userArr['last_name'] = (isset($data['Last_Name'])) ? $data['Last_Name'] : '';
        $userArr['full_name'] = (isset($data['Full_Name'])) ? $data['Full_Name'] : '';
        $userArr['email'] = (isset($data['User_Name'])) ? $data['User_Name'] : '';
        $userArr['password'] = (isset($data['Password'])) ? bcrypt($data['Password']) : '';
        $userArr['remember_token'] = '';
        $userArr['role'] = '';
        $userArr['status'] = (isset($data['Portal_Status'])) ? $data['Portal_Status'] : '';
        if (isset($checkExistsUser)) {
          $userArr['updated_at'] = date('Y-m-d H:i:s');
          DB::table('users')->where('contact_id', $data['id'])->update($userArr);
        } else {
          $userArr['created_at'] = date('Y-m-d H:i:s');
          DB::table('users')->insert($userArr);
        }
      } else {
        DB::table('users')->where('contact_id', $data['id'])->delete();
      }
    }
  }


  function getModuleApiName($module)
  {
    $mdData = DB::table('module_list')->where('module_name', $module)->first();
    return isset($mdData->api_name) ? $mdData->api_name : false;
  }

  function getModuleLabelName($module)
  {
    $mdData = DB::table('module_list')->where('api_name', $module)->first();
    return isset($mdData->plural_label) ? $mdData->plural_label : false;
  }

  function getTableColumns($table)
  {
    return  DB::getSchemaBuilder()->getColumnListing($table);
  }

  function saveDataToDb($table, $data)
  {
    $fieldList =  $this->getTableColumns($table);

    $insertArr = array();
    $uc = 0;

    DB::table($table)->where('module_id', $data['id'])->delete();

    if ($table == 'SalesOrders') $this->saveSalesOrdersItem($data);

    foreach ($data as $key => $value) {

      if (strpos($key, '$') !== false) {
      } else {
        if ($key == 'id') {
          $insertArr['module_id'] = $value;
        } else {

          if (is_array($value)) {
            foreach ($value as $key1 => $val) {
              if ($key1 === 'id') {
                if (in_array($key . '_ID', $fieldList)) {
                  if (is_array($val)) $val = json_encode($val);
                  $insertArr[$key . '_ID'] = $val;
                } else {
                  $uc++;
                }
              } else if ($key1 === 'name') {
                if (in_array($key, $fieldList)) {
                  if (is_array($val)) $val = json_encode($val);
                  $insertArr[$key] = $val;
                } else {
                  $uc++;
                }
              } else {
                if (in_array($key, $fieldList)) {
                  $insertArr[$key] = json_encode($value);
                } else {
                  $uc++;
                }
              }
            }
          } else {
            if (in_array($key, $fieldList)) {
              $insertArr[$key] = $value;
            } else {
              $uc++;
            }
          }
        }
      }
    }

    if (isset($insertArr['Modified_Time'])) $insertArr['Modified_Time'] = date("Y-m-d H:i:s", strtotime($insertArr['Modified_Time']));
    if (isset($insertArr['Created_Time'])) $insertArr['Created_Time'] = date("Y-m-d H:i:s", strtotime($insertArr['Created_Time']));
    if (isset($insertArr['Last_Activity_Time'])) $insertArr['Last_Activity_Time'] = date("Y-m-d H:i:s", strtotime($insertArr['Last_Activity_Time']));

    $insertArr['created_at'] = date('Y-m-d H:i:s');
    DB::table($table)->insert($insertArr);
  }


  public function saveSalesOrdersItem($data)
  {
    $table = 'SalesOrdersItem';
    $itemsList = $data["Product_Details"];
    foreach ($itemsList as $key => $items) {

      $insertArr = array();
      $checkExistsUser = DB::table($table)->where('item_id', $items['id'])->first();

      $insertArr['item_id'] = $items['id'];
      $insertArr['SalesOrderId'] = $data['id'];
      $insertArr['Product_Code'] = $items['product']['Product_Code'];
      $insertArr['Product_Name'] = $items['product']['name'];
      $insertArr['Product_id'] = $items['product']['id'];
      $insertArr['quantity'] = $items['quantity'];
      $insertArr['Discount'] = $items['Discount'];
      $insertArr['total_after_discount'] = $items['total_after_discount'];
      $insertArr['net_total'] = $items['net_total'];
      $insertArr['book'] = $items['book'];
      $insertArr['Tax'] = $items['Tax'];
      $insertArr['list_price'] = $items['list_price'];
      $insertArr['unit_price'] = $items['unit_price'];
      $insertArr['quantity_in_stock'] = $items['quantity_in_stock'];
      $insertArr['total'] = $items['total'];
      $insertArr['product_description'] = $items['product_description'];

      if (isset($checkExistsUser)) {
        $insertArr['updated_at'] = date('Y-m-d H:i:s');
        DB::table($table)->where('item_id', $data['id'])->update($insertArr);
      } else {
        $insertArr['created_at'] = date('Y-m-d H:i:s');
        DB::table($table)->insert($insertArr);
      }
    }
  }

  public function getModulesArrayByuserID($userID)
  {
    $zc_portal_modules = array();
    $zc_portal_modules = DB::table('user_module_permission')
      ->where('user_role_id', $userID)
      ->where(function ($query) {
        $query->where('view', '1')
          ->orWhere('create', '1')
          ->orWhere('edit', '1')
          ->orWhere('delete', '1');
      })
      ->get();
    if ($zc_portal_modules) {
      return $zc_portal_modules;
    } else {
      return array();
    }
  }

  public function getRelatedModuleList($module = '')
  {
    if ($module != '') {
      $modules = array();
      $Nzoho = new Nzoho;
      $related_modules = $Nzoho->getRelatedListsMetaData($module);

      //return json_encode($related_modules);
      if (isset($related_modules) && isset($related_modules->related_lists)) {
        $related_modules = $related_modules->related_lists;
        foreach ($related_modules as $r_key => $r_value) {
          $_tmp = array();
          if ($r_value->type == 'custom_lookup' || $r_value->api_name == 'Notes' || $r_value->api_name == 'Attachments' || $r_value->api_name == 'Activities') {
            $_tmp['module_name'] = $this->getModuleNameByApi($r_value->module);
            $_tmp['display_label'] = $this->getModuleNameByApi($r_value->module);
            $_tmp['field_label'] = $this->getModuleNameByApi($r_value->module);
            // $_tmp['module_name'] = $r_value->display_label;
            $_tmp['api_name'] = $r_value->module;
            $_tmp['related_api_name'] = $r_value->api_name;
            $_tmp['type'] = $r_value->type;

            $_tmp['sectionView'] = "no";
            $_tmp['sectionCreate'] = "no";
            $_tmp['sectionEdit'] = "no";
            $_tmp['sectionDelete'] = "no";
            $_tmp['relatedModuleView'] = "no";
            $_tmp['relatedModuleField'] = "";

            array_push($modules, $_tmp);
          }
        }
      } else {
        return array();
      }
      return (array)$modules;
    }
    return array();
  }

  public function getModulesArray()
  {
    $modules_array = array(
      array(
        'module_name' => "Contacts",
        'api_name' => "Contacts",
      ),
      array(
        'module_name' => "Incidents",
        'api_name' => "Incidents",
      ),
      array(
        'module_name' => "PDAs",
        'api_name' => "Passengers",
      ),
      array(
        'module_name' => "Groups",
        'api_name' => "Groups",
      ),
      array(
        'module_name' => "Family Members",
        'api_name' => "Family_Members",
      ),
      array(
        'module_name' => "Relationships",
        'api_name' => "Group_Relationships",
      ),
      array(
        'module_name' => "Support Personnel",
        'api_name' => "Support_Personnel",
      ),
      array(
        'module_name' => "Deployments",
        'api_name' => "Deployments",
      ),
      array(
        'module_name' => "Inquiries",
        'api_name' => "Inquiries1",
      ),
      array(
        'module_name' => "Payments",
        'api_name' => "Payments",
      ),
      array(
        'module_name' => "Locations",
        'api_name' => "Locations",
      )
    );
    return $modules_array;
  }

  public function getModuleLinkable($module = '')
  {
    $getModulesArray = $this->getModulesArray();
    foreach ($getModulesArray as $key => $value) {
      if ($module == $value['api_name']) {
        return true;
      }
    }
    return false;
  }

  public function getModuleNameByApi($module = '')
  {
    $moduleList = DB::table('module_list')->where('api_name', $module)->first();
    if ($moduleList) {
      return $moduleList->plural_label;
    }
    return false;
  }

  public function getAllfieldListView($module = '')
  {
    $allfieldList = DB::table('zohofields')->select('*')->where('module', $module)->where('data_type', "!=", 'profileimage')->get();

    $fieldList = array();
    foreach ($allfieldList as $key => $field) {
      $fieldList[$field->api_name] = (array)$field;
    }
    return $fieldList;
  }

  public function getSubformData($module = '', $module_id = '')
  {
    $table = "zc_" . strtolower($module);
    $subformdata = DB::table($table)->where('module_id', $module_id)->get();

    if (isset($subformdata)) {
      return $subformdata;
    } else {
      return false;
    }
  }


  public function getSubformFields($module = '')
  {

    $nzohonew = new Nzoho();
    // $resp = $nzohonew->SubformFields($module);
    $resp = $nzohonew->getFieldsNew($module);
    return $resp;
  }


  public function getDefaultView($module = '')
  {
    $contact_id = Auth::user()->contact_id;
    $default_view = DB::table('view_creation')->where('contact_id', $contact_id)->where('module', $module)->where('is_default', 1)->where('is_related_module', 0)->first();
    if ($default_view) {
      return $default_view->id;
    } else {
      return false;
    }
  }

  public function getAllActivitisViewCreation($module = '')
  {
    $contact_id = Auth::user()->contact_id;
    $default_view = DB::table('view_creation')->where('contact_id', $contact_id)->where('module', $module)->whereNotNull('order_module_for_activitis')->where('is_default', 1)->where('is_related_module', 0)->first();
    if ($default_view) {
      return $default_view;
    } else {
      return false;
    }
  }

  public static function getModulePermission($module = '')
  {
    $User_Role_ID = '';
    $contact_id = Auth::user()->contact_id;
    $contact_info  = Contacts::where('module_id', $contact_id)->first();
    $Account_Name_ID = $contact_info->Account_Name_ID;
    $User_Role_ID = Auth::user()->portal_layout_role;
    if (!isset($User_Role_ID)) {
      $User_Role_ID = 1;
    }

    // $User_Role_ID = 1;

    // $user_permission = DB::table('user_module_permission')->where('module_id',$module)->where('user_role_id',$User_Role_ID)->first();
    $user_permission = DB::table('user_module_permission')->where('module_id', $module)->where('user_role_id', $User_Role_ID)->first();


    if ($user_permission) {
      //return response()->json([$user_permission]);
      return $user_permission;
    } else {
      return false;
    }
  }

  public function createDefaultView($module = '')
  {
    $contact_id = Auth::user()->contact_id;
    $contact_info  = DB::table('zc_contacts')->where('module_id', $contact_id)->first();
    $Account_Name_ID = $contact_info->Account_Name_ID;

    DB::table('view_creation')
      ->where('contact_id', $contact_id)
      ->where('module', $module)
      ->where('is_public', 1)
      ->delete();

    $required =  DB::table('zohofields')
      ->select('api_name')
      ->where('module', $module)
      ->where('system_mandatory', 1)
      ->get()->toArray();

    $systemReqField = array_map('current', $required);

    $systemReqField[] = 'Owner';
    $systemReqField[] = 'Modified_Time';

    // $systemReqField = array('Name', 'Owner', 'Modified_Time');


    $selected_fields = implode(',', $systemReqField);

    $modified_criteria = array(
      'field_name' => array("Modified_Time___datetime"),
      'condition' => array("ORDER_BY")
    );

    $module_name = $this->getModuleNameByApi($module);

    $data = array(
      array(
        'contact_id' => $contact_id,
        'account_id' => $Account_Name_ID,
        'module' => $module,
        'view_name' => "All " . $module_name,
        'selected_fields' => $selected_fields,
        'criteria' => "",
        'is_default' => 1,
        'is_public' => 1
      ),
      array(
        'contact_id' => $contact_id,
        'account_id' => $Account_Name_ID,
        'module' => $module,
        'view_name' => "Recently Modified",
        'selected_fields' => $selected_fields,
        'criteria' => json_encode($modified_criteria),
        'is_default' => 0,
        'is_public' => 1
      ),
    );

    $default_view = DB::table('view_creation')->insert($data);

    $id = DB::getPdo()->lastInsertId();
    if ($id) {
      return $id;
    } else {
      return false;
    }
  }



  public function createDefaultViewAllActivitis($module = '' , $systemReqField = [] )
  {
    $contact_id = Auth::user()->contact_id;
    $contact_info  = DB::table('zc_contacts')->where('module_id', $contact_id)->first();
    $Account_Name_ID = $contact_info->Account_Name_ID;

    DB::table('view_creation')
      ->where('contact_id', $contact_id)
      ->where('module', $module)
      ->where('is_public', 1)
      ->delete();

    $required =  DB::table('zohofields')
      ->select('api_name')
      ->where('module', "Tasks")
      ->where('module', "Events")
      ->where('module', "Calls")
      ->where('system_mandatory', 1)
      ->get()->toArray();

    $systemReqField = array_map('current', $required);

    $systemReqField[] = 'Owner';
    $systemReqField[] = 'Modified_Time';

    // $systemReqField = array('Name', 'Owner', 'Modified_Time');


    $selected_fields = implode(',', $systemReqField);

    $modified_criteria = array(
      'field_name' => array("Modified_Time___datetime"),
      'condition' => array("ORDER_BY")
    );

    //$module_name = $this->getModuleNameByApi($module);

    $data = array(
      array(
        'contact_id' => $contact_id,
        'account_id' => $Account_Name_ID,
        'module' => "activities",
        'view_name' => "All Activities",
        'selected_fields' => $selected_fields,
        'criteria' => "",
        'is_default' => 1,
        'is_public' => 1
      ),
      array(
        'contact_id' => $contact_id,
        'account_id' => $Account_Name_ID,
        'module' => "activities",
        'view_name' => "Recently Modified",
        'selected_fields' => $selected_fields,
        'criteria' => json_encode($modified_criteria),
        'is_default' => 0,
        'is_public' => 1
      ),
    );

    $default_view = DB::table('view_creation')->insert($data);

    $id = DB::getPdo()->lastInsertId();
    if ($id) {
      return $id;
    } else {
      return false;
    }
  }
  // for activities module
  public function getMyViewListByModule_activities($module = '')
  {
    $contact_id = Auth::user()->contact_id;
    $view_creation = DB::table('view_creation')
      ->where('contact_id', $contact_id)
      ->where('module', "activities")
      ->where('is_public', 0)
      ->where('is_related_module', 0)
      ->get();
    if ($view_creation) {
      return $view_creation;
    } else {
      return array();
    }
  }
  // for activities module
  public function getPublicViewList_activities($module = '')
  {
    $contact_id = Auth::user()->contact_id;
    $view_creation = DB::table('view_creation')
      ->where('contact_id', $contact_id)
      ->where('module', "activities")
      ->where('is_related_module', 0)
      ->where('is_public', 1)
      
      ->get();

    if (count($view_creation) > 0) {
      return $view_creation;
    } else {
      //$this->createDefaultViewAllActivitis("activities",array());
      return $this->getPublicViewList_activites($contact_id, "activities");
    }
  }


  public function getMyViewListByModule($module = '')
  {
    $contact_id = Auth::user()->contact_id;
    $view_creation = DB::table('view_creation')
      ->where('contact_id', $contact_id)
      ->where('module', $module)
      ->where('is_public', 0)
      ->where('is_related_module', 0)
      ->get();
    if ($view_creation) {
      return $view_creation;
    } else {
      return array();
    }
  }

  public function getPublicViewList($module = '')
  {
    $contact_id = Auth::user()->contact_id;
    $view_creation = DB::table('view_creation')
      ->where('contact_id', $contact_id)
      ->where('module', $module)
      ->where('is_related_module', 0)
      ->where('is_public', 1)
      ->get();

    if (count($view_creation) > 0) {
      return $view_creation;
    } else {
      $this->createDefaultView($module);
      return $this->getPublicViewList1($contact_id, $module);
    }
  }

  public function getPublicViewList1($contact_id = '', $module = '')
  {
    $view_creation = DB::table('view_creation')
      ->where('contact_id', $contact_id)
      ->where('module', $module)
      ->where('is_related_module', 0)
      ->where('is_public', 1)
      ->get();

    if (count($view_creation) > 0) {
      return $view_creation;
    } else {
      return array();
    }
  }

  public function getPublicViewList_activites($contact_id = '', $module = '')
  {
    $view_creation = DB::table('view_creation')
      ->where('contact_id', $contact_id)
      ->where('module', $module)
      ->where('is_related_module', 0)
      ->where('is_public', 1)
      ->get();

    if (count($view_creation) > 0) {
      return $view_creation;
    } else {
      return array();
    }
  }

  public function getSelectedView($view_id = '')
  {
    $selectedView = DB::table('view_creation')->where('id', $view_id)->first();
    if ($selectedView) {
      return $selectedView;
    } else {
      return false;
    }
  }


  // public function getDataByViewID($view_id='', $module_name , $comoun_name)
  // {
  //     $contact_id = Auth::user()->contact_id;
  //     $contact_info  = Contacts::where('module_id', $contact_id)->first();
  //     $User_Role_ID = Auth::user()->portal_layout_role;
  //     if(!isset($User_Role_ID)){
  //       $User_Role_ID = 1;
  //     }
  //     $Account_Name_ID = $contact_info->Account_Name_ID;

  //     // if ($comoun_name =="zc_leads") {
  //     //   $result = DB::table('zc_leads')->get();
  //     //   return $result;
  //     // }

  //     // if ($comoun_name =="zc_campaigns") {
  //     //   $result = DB::table('zc_campaigns')->get();
  //     //   return $result;
  //     // }

  //     // if($comoun_name =="Vendor_Name_ID"){
  //     //   $vendor_id = DB::table('zc_vendors')->where('Account_Name_ID', $Account_Name_ID)->first();
  //     //   $result = DB::table('zc_products')->where('Vendor_Name_ID', $vendor_id->module_id)->get();
  //     //   return $result;
  //     // }

  //     $view_creation = DB::table('view_creation')->where('id',$view_id)->first();
  //     if ($view_creation) {
  //         if ($view_creation->criteria) {
  //             $criteria = json_decode( $view_creation->criteria );
  //             $field_name = $criteria->field_name;
  //             $condition = $criteria->condition;
  //             $field_value = isset($criteria->field_value) ? $criteria->field_value : [];
  //             $picklist_value = isset($criteria->picklist_value) ? $criteria->picklist_value : [] ;
  //             $date_value = isset($criteria->date_value) ? $criteria->date_value : [] ;
  //             $from_date = isset($criteria->from_date) ? $criteria->from_date : [];
  //             $to_date = isset($criteria->to_date) ? $criteria->to_date : [] ;
  //             $index_condition = isset($criteria->index_condition) ? $criteria->index_condition : [] ;
  //         }

  //         $where = '';
  //         if (isset($condition[0]) && $condition[0] != "ORDER_BY") {
  //             $where = "WHERE";
  //         }

  //         // DB::enableQueryLog();
  //         $module_tbl = 'zc_' . strtolower($module_name);

  //         $count = 0;
  //         $query = "SELECT * FROM ". $module_tbl ." $where ";



  //         if ($view_creation->criteria) {
  //             for ($i=2; $i < count($field_name); $i++) { 
  //                 $query .= "(";
  //             }



  //             $field_api_name = '';
  //             $field_type = '';
  //             $zz = 0;
  //             //return json_encode($condition);
  //             foreach ($field_name as $key => $value) {

  //                 $clause = "";

  //                 $field_name_info = explode("___", $value);
  //                 $field_api_name = $field_name_info[0];
  //                 $field_type = $field_name_info[1];

  //                 if($count == 0 && count($field_name) > 2){
  //                     $query .= "(";
  //                 }

  //                 if($key != 0){
  //                     if(!empty($index_condition) && $index_condition[$key-1] == "OR"){
  //                         $clause = " OR";
  //                     }else{
  //                         $clause = " AND";
  //                     }
  //                 }

  //                 if( $condition[$key] == "is" ){
  //                     if($field_type == "picklist"){
  //                         if(count($picklist_value) > 0){
  //                             $query .= $clause." ".$field_api_name."='".$picklist_value[$key]."' ";
  //                         }
  //                         $key ++;
  //                     }else if($field_type == "datetime"){
  //                         if(!empty($date_value)){
  //                             $time_format = 'Y-m-d h:i';
  //                             if( isset($date_value[$key]) && $date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name." LIKE '".date($time_format, strtotime($date_value[$key]))."%' ";
  //                             }
  //                             $key ++;
  //                         }

  //                     }else if($field_type == "date"){
  //                         if(!empty($date_value)){
  //                             if( isset($date_value[$key]) && $date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name."='".date('Y-m-d', strtotime($date_value[$key]))."' ";
  //                             }
  //                             $key ++;
  //                         }

  //                     }
  //                     else{
  //                         if($field_type != 'booleans'){
  //                             if(count($field_value) > 0){
  //                                 $query .= $clause." ".$field_api_name."='".$field_value[$key]."' ";
  //                             }
  //                             $key ++;
  //                         }
  //                     } 
  //                 }else if($condition[$key] == "is_not"){
  //                     if($field_type[$key] == "picklist"){
  //                         if(count($picklist_value) > 0){
  //                             $query .= $clause." ".$field_api_name."!='".$picklist_value[$key]."' ";
  //                         }
  //                         $key ++;
  //                     }else if( $field_type == "datetime"){
  //                         if(!empty($date_value)){
  //                             $time_format = 'Y-m-d h:i';
  //                             if(isset($date_value[$key]) && $date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name." LIKE '".date($time_format, strtotime($date_value[$key]))."%' ";
  //                             }
  //                             $key ++;
  //                         }
  //                     }else if($field_type == "date"){
  //                         if(!empty($date_value)){
  //                             if( isset($date_value[$key]) && $date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name."='".date('Y-m-d', strtotime($date_value[$key]))."' ";
  //                             }
  //                             $key ++;
  //                         }
  //                     }else{
  //                         if($field_type[$key] != 'booleans'){
  //                             if(count($field_value) > 0){
  //                                 $query .= $clause." ".$field_api_name."!='".$field_value[$key]."' ";
  //                             }
  //                             $key++;
  //                         }
  //                     }                
  //                 }else if($condition[$key] == "between"){
  //                     if(isset($from_date[$key]) && isset($to_date[$key]) && ($from_date[$key] != '' && $to_date[$key] != '')){
  //                         $query .= $clause." ".$field_api_name." BETWEEN '".date("Y-m-d h:i:s", strtotime($from_date[$key]))."' AND '".date("Y-m-d h:i:s", strtotime($to_date[$key]))."' ";
  //                     }
  //                 }else if($condition[$key] == "not_between"){

  //                     if(isset($from_date[$key]) && isset($to_date[$key]) && ($from_date[$key] != '' && $to_date[$key] != '')){
  //                         $query .= $clause." ".$field_api_name." NOT BETWEEN '".date("Y-m-d h:i:s", strtotime($from_date[$key]))."' AND '".date("Y-m-d h:i:s", strtotime($to_date[$key]))."' ";
  //                     }
  //                 }else if($condition[$key] == "selected"){
  //                     $query .= $clause." ".$field_api_name." = 1 ";
  //                 }else if($condition[$key] == "not_selected"){
  //                     $query .= $clause." ".$field_api_name." = 0 ";
  //                 }else if($condition[$key] == "is_before"){
  //                   //return json_encode($date_value);
  //                     if( isset($date_value[$key]) && $date_value[$key] != ''){


  //                         $query .= $clause." ".$field_api_name." < '".date("Y-m-d h:i:s", strtotime($date_value[$key]))."' ";
  //                     }
  //                     $key ++;
  //                 }else if($condition[$key] == "is_after"){
  //                   //return json_encode($date_value);
  //                     if(  isset($date_value[$key]) && $date_value[$key] != ''){
  //                         $query .= $clause." ".$field_api_name." > '".date("Y-m-d h:i:s", strtotime($date_value[$key]))."' ";
  //                     }
  //                     $key ++;
  //                 }else if( $condition[$key] == "is_empty" ){
  //                     $query .= $clause." ".$field_api_name." IS NULL ";
  //                 }else if( $condition[$key] == "is_not_empty" ){
  //                     $query .= $clause." ".$field_api_name." IS NOT NULL ";
  //                 }else if($condition[$key] == "contains"){
  //                     if($field_type[$key] == "picklist"){
  //                         if(count($picklist_value) > 0){
  //                           $query .= $clause." ".$field_api_name." LIKE '%".$picklist_value[$key]."%' ";
  //                         }
  //                         $key ++;
  //                     }else if($field_type[$key] == "datetime"){
  //                         if(!empty($date_value)){
  //                             $time_format = 'Y-m-d h:i';
  //                             if( isset($date_value[$key]) && $date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name." LIKE '".date($time_format, strtotime($date_value[$key]))."%' ";
  //                             }
  //                             $key ++;
  //                         }
  //                     }else if($field_type == "date"){
  //                         if(!empty($date_value)){
  //                             if(  isset($date_value[$key]) && $date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name."='".date('Y-m-d', strtotime($date_value[$key]))."' ";
  //                             }
  //                             $key ++;
  //                         }
  //                     }else{
  //                         if($field_type[$key] != 'booleans'){
  //                             if(count($field_value) > 0){
  //                                 $query .= $clause." ".$field_api_name." LIKE '%".$field_value[$key]."%' ";
  //                             }
  //                             $key ++;
  //                         }
  //                     }
  //                 }else if($condition[$key] == "does_not_contain"){
  //                     if($field_type[$key] == "picklist"){
  //                         if(count($picklist_value) > 0){
  //                             $query .= $clause." ".$field_api_name." NOT LIKE '%".$picklist_value[$key]."%' ";
  //                         }
  //                         $key ++;
  //                     }else if($field_type[$key] == "datetime"){
  //                         if(!empty($date_value)){
  //                             $time_format = 'Y-m-d h:i';
  //                             if(  isset($date_value[$key]) && $date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name." LIKE '".date($time_format, strtotime($date_value[$key]))."%' ";
  //                             }
  //                             $key ++;
  //                         }
  //                     }else if($field_type == "date"){
  //                         if(!empty($date_value)){
  //                             if(  isset($date_value[$key]) && $date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name."='".date('Y-m-d', strtotime($date_value[$key]))."' ";
  //                             }
  //                             $key ++;
  //                         }
  //                     }else{
  //                         if($field_type[$key] != 'booleans'){
  //                             if(count($field_value) > 0){
  //                                 $query .= $clause." ".$field_api_name." NOT LIKE '%".$field_value[$key]."%' ";
  //                             }
  //                             $key++;
  //                         }
  //                     }
  //                 }

  //                 if($count > 0 && count($field_name) > 2){
  //                   $query .= ")";
  //                 }

  //                 $count += 1;

  //                 $zz++;
  //             }
  //         }

  //         if (isset($condition[0]) && $condition[0] != "ORDER_BY") {
  //             $query .= "AND ";
  //         }else{
  //             $query .= "WHERE ";
  //         }

  //         if ($comoun_name =="zc_leads") {
  //           $f_val ='module_id';
  //           $s_val =null;
  //           $query .=" ".$f_val." !='".$s_val."'";
  //         }else if ($comoun_name =="zc_campaigns") {
  //           $f_val ='module_id';
  //           $s_val =null;
  //           $query .=" ".$f_val." !='".$s_val."'";
  //         }else if ($comoun_name =="Vendor_Name_ID") {
  //           $vendor_id = DB::table('zc_vendors')->where('Account_Name_ID', $Account_Name_ID)->first();
  //           // $f_val ='Vendor_Name_ID';
  //           $f_val ='module_id';
  //           // $s_val =isset($vendor_id->module_id)?$vendor_id->module_id : '';
  //           $s_val =null;
  //           $query .=" ".$f_val." !='".$s_val."'";
  //         }else{
  //           $query .=" ".$comoun_name." ='".$Account_Name_ID."'";
  //         }



  //         if (isset($condition[0]) && $condition[0] == "ORDER_BY") {
  //             $query .= "AND ";
  //             $query .=  $field_api_name ." >= NOW() - INTERVAL 1 DAY ";
  //             $query .= " ORDER BY ".$field_api_name." DESC ";
  //         }

  //         $result = DB::select($query);

  //         // echo "<pre>";
  //         // print_r($query);
  //         // print_r($result);
  //         // exit();

  //         return $result;
  //     }else{
  //         return array();
  //     }
  // }



  public function getDataByViewID($view_id = '', $module_name = "", $skip=0 , $take=0 ,$search_data="" , $orderForAllActivitis ="" , $orderbyForAllActivitis="" )
  {

    $contact_id = Auth::user()->contact_id;

    $contact_info  = Contacts::where('module_id', $contact_id)->first();
    $User_Role_ID = $contact_info->User_Role_ID;
    $Account_Name_ID = $contact_info->Account_Name_ID;
    $is_call_center = $this->getCallCenterRoleTypeByID($User_Role_ID);
    $view_creation = DB::table('view_creation')->where('id', $view_id)->first();

    $selected_fields = isset($view_creation->selected_fields)?$view_creation->selected_fields:"";

    $for_Search_Server_side = explode(',', $selected_fields);
    $Portal_User_Role_ID = Auth::user()->portal_layout_role;
    if (!isset($Portal_User_Role_ID)) {
      $Portal_User_Role_ID = 1;
    }

    //$columnIndex = $request['order'][0]['column']; // Column index
    //$columnName = $request['columns'][$columnIndex]['name']; // Column name
    //$columnSortOrder = $request['order'][0]['dir']; // asc or desc
    // $q = $request['search']['value'];

    $columnIndex = ''; // Column index
    $columnName = ''; // Column name
    $columnSortOrder = ''; // asc or desc
    $q = '';

    // $skip = $request->start;
    // $take = $request->length;

    $result = array();
    if (!$view_creation) {
      return $result;
    }

    //Make table name
    $module_tbl = 'zc_' . strtolower($module_name);
    $sql = DB::table($module_tbl);

    $clause = '';

    

    //View creation exists check
    if (isset($view_creation->criteria) && $view_creation->criteria != '') {
      $loop_check = 0;

      $criteria = isset($view_creation->criteria) ? json_decode($view_creation->criteria) : array();
      $field_name = isset($criteria->field_name) ? $criteria->field_name : array();
      $condition = isset($criteria->condition) ? $criteria->condition : array();
      $field_value = isset($criteria->field_value) ? $criteria->field_value : array();
      $picklist_value = isset($criteria->picklist_value) ? $criteria->picklist_value : array();
      $date_value = isset($criteria->date_value) ? $criteria->date_value : array();
      $from_date = isset($criteria->from_date) ? $criteria->from_date : array();
      $to_date = isset($criteria->to_date) ? $criteria->to_date : array();
      $index_condition = isset($criteria->index_condition) ? $criteria->index_condition : array();
      foreach ($field_name as $key => $value) {
        $field_name_info = explode("___", $value);
        $field_api_name = $field_name_info[0];
        $field_type = $field_name_info[1];
        $date_time_format = 'Y-m-d h:i';
        $date_format = 'Y-m-d';

        if (Schema::hasColumn($module_tbl, $field_api_name) ==true) //continue;
        {
          $loop_check++;
           //return array(); 
          // $sql->where("module_id", '=', null);
          // $result = $sql->get();
          // return $result;
          //continue;

          // if ($clause == "OR") {
          //   $sql->orWhere("module_id","=",null);
          // } else {
          //   $sql->where("module_id","=",null);
          // }
         // $sql->orWhere("module_id","=",null);
          //continue;
          //return $result;





          if ($key != 0) {
            if (!empty($index_condition) && $index_condition[$key - 1] == "OR") {
              $clause = "OR";
            } else {
              $clause = "AND";
            }
          }



          if (isset($condition[$key]) && $condition[$key] == "is") {
            if ($field_type == "picklist") {
              if (count($picklist_value) > 0 && isset($picklist_value[$key]) && $picklist_value[$key] != '') {
                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, $picklist_value[$key]);
                } else {
                  $sql->where($field_api_name, $picklist_value[$key]);
                }
              }
            } else if ($field_type == "datetime") {
              if (count($date_value) > 0 && isset($date_value[$key]) && $date_value[$key] != '') {
                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, 'LIKE', date($date_time_format, strtotime($date_value[$key])) . '%');
                } else {
                  $sql->where($field_api_name, 'LIKE', date($date_time_format, strtotime($date_value[$key])) . '%');
                }
              }
            } else if ($field_type == "date") {
              if (count($date_value) > 0 && isset($date_value[$key]) && $date_value[$key] != '') {
                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, date($date_format, strtotime($date_value[$key])));
                } else {
                  $sql->where($field_api_name, date($date_format, strtotime($date_value[$key])));
                }
              }
            } else {
              if ($field_type != 'boolean' && count($field_value) > 0 && isset($field_value[$key]) && $field_value[$key] != '') {

                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, $field_value[$key]);
                } else {
                  $sql->where($field_api_name, $field_value[$key]);
                }
              }
            }
          } else if (isset($condition[$key]) && $condition[$key] == "is_not") {
            if ($field_type == "picklist" && isset($picklist_value[$key]) && count($picklist_value) > 0) {

              if ($clause == "OR") {
                $sql->orWhere($field_api_name, "!=", $picklist_value[$key]);
              } else {
                $sql->where($field_api_name, "!=", $picklist_value[$key]);
              }
            } else if ($field_type == "datetime") {
              if (!empty($date_value) && isset($date_value[$key]) && $date_value[$key] != '') {
                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, "!=", date($date_time_format, strtotime($date_value[$key])));
                } else {
                  $sql->where($field_api_name, "!=", date($date_time_format, strtotime($date_value[$key])));
                }
              }
            } else if ($field_type == "date") {
              if (!empty($date_value) && isset($date_value[$key]) && $date_value[$key] != '') {
                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, "!=", date($date_format, strtotime($date_value[$key])));
                } else {
                  $sql->where($field_api_name, "!=", date($date_format, strtotime($date_value[$key])));
                }
              }
            } else {
              if ($field_type != 'boolean' && count($field_value) > 0 && isset($field_value[$key]) && $field_value[$key] != '') {

                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, "!=", $field_value[$key]);
                } else {
                  $sql->where($field_api_name, "!=", $field_value[$key]);
                }
              }
            }
          } else if (isset($condition[$key]) && $condition[$key] == "between") {
            if (isset($from_date[$key]) && isset($to_date[$key]) && $from_date[$key] != '' && $to_date[$key] != '') {
              $from_date__ = date($date_time_format, strtotime($from_date[$key]));
              $to_date__ = date($date_time_format, strtotime($to_date[$key]));

              if ($clause == "OR") {
                $sql->orWhereBetween($field_api_name, [$from_date__, $to_date__]);
              } else {
                $sql->whereBetween($field_api_name, [$from_date__, $to_date__]);
              }
            }
          } else if (isset($condition[$key]) && $condition[$key] == "not_between") {
            if (isset($from_date[$key]) && isset($to_date[$key]) && $from_date[$key] != '' && $to_date[$key] != '') {

              $from_date__ = date($date_time_format, strtotime($from_date[$key]));
              $to_date__ = date($date_time_format, strtotime($to_date[$key]));

              if ($clause == "OR") {
                $sql->orWhereNotBetween($field_api_name, [$from_date__, $to_date__]);
              } else {
                $sql->whereNotBetween($field_api_name, [$from_date__, $to_date__]);
              }
            }
          } else if (isset($condition[$key]) && $condition[$key] == "selected") {
            if ($clause == "OR") {
              $sql->orWhere($field_api_name, "=", 1);
            } else {
              $sql->where($field_api_name, "=", 1);
            }
          } else if (isset($condition[$key]) && $condition[$key] == "not_selected") {

            if ($clause == "OR") {
              $sql->orWhere($field_api_name, "=", 0);
            } else {
              $sql->where($field_api_name, "=", 0);
            }
          } else if (isset($condition[$key]) && $condition[$key] == "is_before" && isset($date_value[$key])  && $date_value[$key] != '') {
            if ($clause == "OR") {
              $sql->orWhere($field_api_name, "<", date($date_time_format, strtotime($date_value[$key])));
            } else {
              $sql->where($field_api_name, "<", date($date_time_format, strtotime($date_value[$key])));
            }
          } else if (isset($condition[$key]) && $condition[$key] == "is_after" && isset($date_value[$key]) && $date_value[$key] != '') {
            if ($clause == "OR") {
              $sql->orWhere($field_api_name, ">", date($date_time_format, strtotime($date_value[$key])));
            } else {
              $sql->where($field_api_name, ">", date($date_time_format, strtotime($date_value[$key])));
            }
          } else if (isset($condition[$key]) && $condition[$key] == "is_empty") {
            if ($clause == "OR") {
              $sql->orWhere($field_api_name, "");
            } else {
              $sql->whereNull($field_api_name);
            }
          } else if (isset($condition[$key]) && $condition[$key] == "is_not_empty") {
            if ($clause == "OR") {
              $sql->orWhere($field_api_name, "!=", "");
            } else {
              $sql->whereNotNull($field_api_name);
            }
          } else if (isset($condition[$key]) && $condition[$key] == "contains") {
            if ($field_type == "picklist" && isset($picklist_value[$key]) && count($picklist_value) > 0) {

              if ($clause == "OR") {
                $sql->orWhere($field_api_name, "LIKE", "%" . $picklist_value[$key] . "%");
              } else {
                $sql->where($field_api_name, "LIKE", "%" . $picklist_value[$key] . "%");
              }
            } else if ($field_type == "datetime") {
              if (!empty($date_value) && isset($date_value[$key]) && $date_value[$key] != '') {
                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, "LIKE", date($date_time_format, strtotime($date_value[$key])) . "%");
                } else {
                  $sql->where($field_api_name, "LIKE", date($date_time_format, strtotime($date_value[$key])) . "%");
                }
              }
            } else if ($field_type == "date") {
              if (!empty($date_value) && isset($date_value[$key]) && $date_value[$key] != '') {
                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, "LIKE", date($date_format, strtotime($date_value[$key])) . "%");
                } else {
                  $sql->where($field_api_name, "LIKE", date($date_format, strtotime($date_value[$key])) . "%");
                }
              }
            } else {
              if ($field_type != 'boolean' && isset($field_value[$key]) && count($field_value) > 0) {

                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, "LIKE", "%" . $field_value[$key] . "%");
                } else {
                  $sql->where($field_api_name, "LIKE", "%" . $field_value[$key] . "%");
                }
              }
            }
          } else if (isset($condition[$key]) && $condition[$key] == "does_not_contain") {
            if ($field_type == "picklist" && count($picklist_value) > 0 && isset($picklist_value[$key])) {

              if ($clause == "OR") {
                $sql->orWhere($field_api_name, "NOT LIKE", "%" . $picklist_value[$key] . "%");
              } else {
                $sql->where($field_api_name, "NOT LIKE", "%" . $picklist_value[$key] . "%");
              }
            } else if ($field_type == "datetime") {
              if (!empty($date_value) && isset($date_value[$key]) && $date_value[$key] != '') {

                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, "NOT LIKE", "%" . date($date_time_format, strtotime($date_value[$key])) . "%");
                } else {
                  $sql->where($field_api_name, "NOT LIKE", "%" . date($date_time_format, strtotime($date_value[$key])) . "%");
                }
              }
            } else if ($field_type == "date") {
              if (!empty($date_value) && isset($date_value[$key]) && $date_value[$key] != '') {

                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, "NOT LIKE", "%" . date($date_format, strtotime($date_value[$key])) . "%");
                } else {
                  $sql->where($field_api_name, "NOT LIKE", "%" . date($date_format, strtotime($date_value[$key])) . "%");
                }
              }
            } else {
              if ($field_type != 'boolean' && isset($field_value[$key])  && count($field_value) > 0) {

                if ($clause == "OR") {
                  $sql->orWhere($field_api_name, "NOT LIKE", "%" . $field_value[$key] . "%");
                } else {
                  $sql->where($field_api_name, "NOT LIKE", "%" . $field_value[$key] . "%");
                }
              }
            }
          }

        }
        
      }
    }



    //For recently modified view
    if (isset($condition[0]) && $condition[0] == "ORDER_BY") {
      $sql->where($field_api_name, '>=', Carbon::now()->subDay(1));
      $sql->orderBy($field_api_name, 'DESC');
    } else { 

      if ($orderForAllActivitis !="" ) {
        $sql->orderBy($orderbyForAllActivitis, $orderForAllActivitis);
      }
      if ($columnName != '' && $columnSortOrder != '') {
        
        $sql->orderBy($columnName, $columnSortOrder);
        //Set default sort for view
        $this->setSortingFieldByViewID($columnName, $columnSortOrder, $view_id);
      }
    }
    // if (!$is_call_center)$sql->where("module_id",'!=',null);

    $locupRltArr = DB::table("user_module_permission")->where("module_id", $module_name)->where('user_role_id', $Portal_User_Role_ID)->first();

    if (isset($locupRltArr->id)) {
      if ($locupRltArr->foriegn_module == "Accounts") {
        $sql->where($locupRltArr->foriegn_key . "_ID", $Account_Name_ID);
      } else if ($locupRltArr->foriegn_module == "") {

        $sql->where("module_id", '!=', null);
      } else {

        $zohofieldsDt = DB::table('zohofields')->select('id', 'api_name', 'field_label', 'lookup')->where('module', $locupRltArr->foriegn_module)->where('data_type', 'lookup')->where('lookup', "LIKE", "%Accounts%")->first();

        if (isset($zohofieldsDt->id)) {

          $relMdlDt = DB::table("zc_" . strtolower($locupRltArr->foriegn_module))->select('module_id')->where($zohofieldsDt->api_name . "_ID", $Account_Name_ID)->get();

          $relMdlIds = [];
          foreach ($relMdlDt as $key => $value) {
            $relMdlIds[]  = $value->module_id;
          }

          $sql->whereIn($locupRltArr->foriegn_key . "_ID", $relMdlIds);
        } else {
          $sql->where("module_id", '=', null);
        }
      }
    } else {
      $sql->where("module_id", '!=', null);
    }

    // $parentModuleName = "zc_".strtolower($locupRltArr->foriegn_module);
    // $parentModuleName = DB::table("module_list")->where("api_name" , $locupRltArr->foriegn_module)->first();

    //$foriegnModule = DB::table($parentModuleName)->where("module_id" , $module_name )->where('user_role_id',$Portal_User_Role_ID)->first();


    // if ($locupRltArr !="") {

    //   $sql->where($locupRltArr->foriegn_key."_ID",$parentModuleName->module_id );

    // }else{

    //   $sql->where("module_id",'!=',null);
    // }

    // else if ($comoun_name =="zc_campaigns") {
    //   $sql->where("module_id",'!=',null);
    // }
    // else if ($comoun_name =="Vendor_Name_ID") {
    //   $vendor_id = DB::table('zc_vendors')->where('Account_Name_ID', $Account_Name_ID)->first();

    //   $f_val ='Vendor_Name_ID';
    //   $s_val =isset($vendor_id->module_id)?$vendor_id->module_id : '';
    //   $sql->where("Vendor_Name_ID",$s_val);
    //   $sql->where("module_id",'!=',null);

    // }else if ($comoun_name =="Account_Name_ID") {
    //    $sql->where("Account_Name_ID",$Account_Name_ID);

    // }

    // else{

    //   $sql->where("module_id",'!=',null);
    // }

    //All count for paginate

    
    //Search
    if ($search_data != '') {
      $sql->where(function ($query) use ($search_data, $selected_fields) {
        foreach (explode(",", $selected_fields) as $key => $value) {
          $query->orWhere($value, 'LIKE', '%' . $search_data . '%');
        }
      });
    }

    $totalRecords = $sql->count();

    if ($take != 0 ) {
      $sql->skip($skip)->take($take);
    }else{
      $sql->take(50);
    }

    //Paginating
    // $sql->skip($skip);
    // $sql->take($take);

     // DB::enableQueryLog();



    $result = $sql->get();
    $result_count = $sql->get()->count();

    // return DB::getQueryLog();

    // echo "<pre>";
    // print_r($criteria);
    // print_r($result);
    // exit();

    if(isset($loop_check) && $loop_check == 0){
      $result = array();
    }

    $data = array(
      'totalRecords' => $totalRecords,
      'result' => $result,
      'result_count' => $result_count,
    );

    return $data;
  }

  // public function getDataByViewID($view_id='', $module_name , $comoun_name)
  // {
  //     $contact_id = Auth::user()->contact_id;
  //     $contact_info  = Contacts::where('module_id', $contact_id)->first();
  //     $User_Role_ID = $contact_info->User_Role_ID;
  //     $Account_Name_ID = $contact_info->Account_Name_ID;

  //     $view_creation = DB::table('view_creation')->where('id',$view_id)->first();
  //     if ($view_creation) {
  //         if ($view_creation->criteria) {
  //             $criteria = json_decode( $view_creation->criteria );
  //             $field_name = $criteria->field_name;
  //             $condition = $criteria->condition;
  //             $field_value = isset($criteria->field_value) ? $criteria->field_value : [];
  //             $picklist_value = isset($criteria->picklist_value) ? $criteria->picklist_value : [] ;
  //             $date_value = isset($criteria->date_value) ? $criteria->date_value : [] ;
  //             $from_date = isset($criteria->from_date) ? $criteria->from_date : [];
  //             $to_date = isset($criteria->to_date) ? $criteria->to_date : [] ;
  //             $index_condition = isset($criteria->index_condition) ? $criteria->index_condition : [] ;
  //         }

  //         $where = '';

  //         // DB::enableQueryLog();
  //         $module_tbl = 'zc_' . strtolower($module_name);

  //         $count = 0;
  //         $query = "SELECT * FROM ". $module_tbl ." $where ";

  //         if ($comoun_name !='') {
  //           $query .=" ".$comoun_name." ='".$Account_Name_ID."'";
  //         }

  //         if ($view_creation->criteria) {
  //             for ($i=2; $i < count($field_name); $i++) { 
  //                 $query .= "(";
  //             }

  //             $field_api_name = '';
  //             $field_type = '';
  //             foreach ($field_name as $key => $value) {
  //                 $clause = "";

  //                 $field_name_info = explode("___", $value);
  //                 $field_api_name = $field_name_info[0];
  //                 $field_type = $field_name_info[1];

  //                 if($count == 0 && count($field_name) > 2){
  //                     $query .= "(";
  //                 }

  //                 if($key != 0){
  //                     if(!empty($index_condition) && $index_condition[$key-1] == "OR"){
  //                         $clause = " OR";
  //                     }else{
  //                         $clause = " AND";
  //                     }
  //                 }

  //                 if( $condition[$key] == "is" ){
  //                     if($field_type == "picklist"){
  //                         if(count($picklist_value) > 0){
  //                             $query .= $clause." ".$field_api_name."='".$picklist_value[$key]."' ";
  //                         }
  //                         $key ++;
  //                     }else if($field_type == "datetime"){
  //                         if(!empty($date_value)){
  //                             $time_format = 'Y-m-d h:i';
  //                             if($date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name." LIKE '".date($time_format, strtotime($date_value[$key]))."%' ";
  //                             }
  //                         }
  //                     }else if($field_type == "date"){
  //                         if(!empty($date_value)){
  //                             if($date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name."='".date('Y-m-d', strtotime($date_value[$key]))."' ";
  //                             }
  //                         }
  //                     }
  //                     else{
  //                         if($field_type != 'booleans'){
  //                             if(count($field_value) > 0){
  //                                 $query .= $clause." ".$field_api_name."='".$field_value[$key]."' ";
  //                             }
  //                             $key ++;
  //                         }
  //                     } 
  //                 }else if($condition[$key] == "is_not"){
  //                     if($field_type[$key] == "picklist"){
  //                         if(count($picklist_value) > 0){
  //                             $query .= $clause." ".$field_api_name."!='".$picklist_value[$key]."' ";
  //                         }
  //                         $key ++;
  //                     }else if($field_type[$key] == "datetime"){
  //                         if(!empty($date_value)){
  //                             $time_format = 'Y-m-d h:i';
  //                             if($date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name." LIKE '".date($time_format, strtotime($date_value[$key]))."%' ";
  //                             }
  //                         }
  //                     }else if($field_type == "date"){
  //                         if(!empty($date_value)){
  //                             if($date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name."='".date('Y-m-d', strtotime($date_value[$key]))."' ";
  //                             }
  //                         }
  //                     }else{
  //                         if($field_type[$key] != 'booleans'){
  //                             if(count($field_value) > 0){
  //                                 $query .= $clause." ".$field_api_name."!='".$field_value[$key]."' ";
  //                             }
  //                             $key++;
  //                         }
  //                     }                
  //                 }else if($condition[$key] == "between"){
  //                     if($from_date[$key] != '' && $to_date[$key] != ''){
  //                         $query .= $clause." ".$field_api_name." BETWEEN '".date("Y-m-d h:i:s", strtotime($from_date[$key]))."' AND '".date("Y-m-d h:i:s", strtotime($to_date[$key]))."' ";
  //                     }
  //                 }else if($condition[$key] == "not_between"){
  //                     if($from_date[$key] != '' && $to_date[$key] != ''){
  //                         $query .= $clause." ".$field_api_name." NOT BETWEEN '".date("Y-m-d h:i:s", strtotime($from_date[$key]))."' AND '".date("Y-m-d h:i:s", strtotime($to_date[$key]))."' ";
  //                     }
  //                 }else if($condition[$key] == "selected"){
  //                     $query .= $clause." ".$field_api_name." = 1 ";
  //                 }else if($condition[$key] == "not_selected"){
  //                     $query .= $clause." ".$field_api_name." = 0 ";
  //                 }else if($condition[$key] == "is_before"){
  //                     if($date_value[$key] != ''){
  //                         $query .= $clause." ".$field_api_name." < '".date("Y-m-d h:i:s", strtotime($date_value[$key]))."' ";
  //                     }
  //                 }else if($condition[$key] == "is_after"){
  //                     if($date_value[$key] != ''){
  //                         $query .= $clause." ".$field_api_name." > '".date("Y-m-d h:i:s", strtotime($date_value[$key]))."' ";
  //                     }
  //                 }else if( $condition[$key] == "is_empty" ){
  //                     $query .= $clause." ".$field_api_name." IS NULL ";
  //                 }else if( $condition[$key] == "is_not_empty" ){
  //                     $query .= $clause." ".$field_api_name." IS NOT NULL ";
  //                 }else if($condition[$key] == "contains"){
  //                     if($field_type[$key] == "picklist"){
  //                         if(count($picklist_value) > 0){
  //                           $query .= $clause." ".$field_api_name." LIKE '%".$picklist_value[$key]."%' ";
  //                         }
  //                         $key ++;
  //                     }else if($field_type[$key] == "datetime"){
  //                         if(!empty($date_value)){
  //                             $time_format = 'Y-m-d h:i';
  //                             if($date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name." LIKE '".date($time_format, strtotime($date_value[$key]))."%' ";
  //                             }
  //                         }
  //                     }else if($field_type == "date"){
  //                         if(!empty($date_value)){
  //                             if($date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name."='".date('Y-m-d', strtotime($date_value[$key]))."' ";
  //                             }
  //                         }
  //                     }else{
  //                         if($field_type[$key] != 'booleans'){
  //                             if(count($field_value) > 0){
  //                                 $query .= $clause." ".$field_api_name." LIKE '%".$field_value[$key]."%' ";
  //                             }
  //                             $key ++;
  //                         }
  //                     }
  //                 }else if($condition[$key] == "does_not_contain"){
  //                     if($field_type[$key] == "picklist"){
  //                         if(count($picklist_value) > 0){
  //                             $query .= $clause." ".$field_api_name." NOT LIKE '%".$picklist_value[$key]."%' ";
  //                         }
  //                         $key ++;
  //                     }else if($field_type[$key] == "datetime"){
  //                         if(!empty($date_value)){
  //                             $time_format = 'Y-m-d h:i';
  //                             if($date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name." LIKE '".date($time_format, strtotime($date_value[$key]))."%' ";
  //                             }
  //                         }
  //                     }else if($field_type == "date"){
  //                         if(!empty($date_value)){
  //                             if($date_value[$key] != ''){
  //                                 $query .= $clause." ".$field_api_name."='".date('Y-m-d', strtotime($date_value[$key]))."' ";
  //                             }
  //                         }
  //                     }else{
  //                         if($field_type[$key] != 'booleans'){
  //                             if(count($field_value) > 0){
  //                                 $query .= $clause." ".$field_api_name." NOT LIKE '%".$field_value[$key]."%' ";
  //                             }
  //                             $key++;
  //                         }
  //                     }
  //                 }

  //                 if($count > 0 && count($field_name) > 2){
  //                   $query .= ")";
  //                 }

  //                 $count += 1;
  //             }
  //         }

  //         if (isset($condition[0]) && $condition[0] != "ORDER_BY") {
  //             $query .= "AND ";
  //         }else{
  //             $query .= "WHERE ";
  //         }

  //         $query .=" ".$comoun_name." ='".$Account_Name_ID."'";

  //         if (isset($condition[0]) && $condition[0] == "ORDER_BY") {
  //             $query .= "AND ";
  //             $query .=  $field_api_name ." >= NOW() - INTERVAL 1 DAY ";
  //             $query .= " ORDER BY ".$field_api_name." DESC ";
  //         }

  //         //check this

  //         if ($comoun_name =="") {
  //           $result = DB::table('zc_leads')->get();
  //         }else{
  //           $result = DB::select($query);
  //         }

  //         // echo "<pre>";
  //         // print_r($query);
  //         // print_r($result);
  //         // exit();

  //         return $result;
  //     }else{
  //         return array();
  //     }
  // }

  public function getCallCenterRoleTypeByID($User_Role_ID = '')
  {
    return false;
    // $zc_portal_user_roles = DB::table('zc_portal_user_roles')->where('module_id', $User_Role_ID)->first();
    // if ($zc_portal_user_roles->User_Type == "Call Center") {
    //     return true;
    // }else{
    //     return false;
    // }
  }

  public function getLookupFieldsByModule($module = '')
  {
    if ($module != '') {
      $_fields = DB::table('zohofields')->select('api_name', 'field_label', 'lookup')->where('module', $module)->where('data_type', 'lookup')->get();
      return $_fields;
    } else {
      return false;
    }
  }

  public function getFieldNameByAPI($field_api_name = '', $module = '')
  {
    $field_name = DB::table('zohofields')
      ->select('field_label', 'json_type', 'api_name', 'data_type')
      ->where("api_name", "=", $field_api_name)
      ->where("module", "=", $module)
      ->first();

    if ($field_name) {
      return $field_name->field_label;
    } else {
      return '';
    }
  }

  public function getFieldNameByAPI_activities($field_api_name = '', $module = '')
  {
    $field_name = DB::table('zohofields')
      ->select('field_label', 'json_type', 'api_name', 'data_type')
      ->where("api_name", "=", $field_api_name)
      ->first();

    if ($field_name) {
      return $field_name->field_label;
    } else {
      return '';
    }
  }

  public function getSystemMandatoryByAPI($field_api_name = '', $module = '')
  {
    $field_name = DB::table('zohofields')
      ->select('system_mandatory', 'auto_number', 'custom_field')
      ->where("api_name", "=", $field_api_name)
      ->where("module", "=", $module)
      // ->where("custom_field", "=", 0)
      ->first();

    // echo "<pre>";
    // var_dump($field_name);
    // exit();
    if ((isset($field_name->system_mandatory) && $field_name->system_mandatory  == '1') || (isset($field_name->auto_number) &&  $field_name->auto_number != "{}" && isset($field_name->custom_field) && $field_name->custom_field == "0")) {
      return true;
    } else {
      return false;
    }
  }

  public function getTypeByAPINameInModule($api_name = '', $module = '')
  {
    $zohofields = DB::table('zohofields')->select('data_type')->where('module', $module)->where('api_name', $api_name)->first();
    if (isset($zohofields) && $zohofields != '') {
      return $zohofields->data_type;
    } else {
      return '';
    }
  }

  public function dateByTimeZone($original_datetime = '', $target_timeformat = '')
  {
    if ($original_datetime == '') return " ";
    if ($target_timeformat == '') $target_timeformat = 'Y-m-d H:i:s T';
    $original_timezone = new \DateTimeZone('UTC');
    $target_timezone = $this->getTimeZone();
    $datetime = new \DateTime($original_datetime, $original_timezone);
    $target_timezone = new \DateTimeZone($target_timezone);
    $datetime->setTimeZone($target_timezone);
    $triggerOn = $datetime->format($target_timeformat);
    return $triggerOn;
  }

  public function getTimeZone()
  {
    return date_default_timezone_get();

    // $setting = DB::table('setting')->first();

    // if (isset($setting)) {
    //     $time_zone = $setting->time_zone;
    // }

    // if (isset($time_zone) && $time_zone != '')  {
    //     return $time_zone;
    // } else {
    //     return date_default_timezone_get();
    // }
  }

  public function getLookupModuleName($api_name = '', $module = '')
  {
    $zohofields = DB::table('zohofields')->select('data_type', 'lookup')->where('module', $module)->where('api_name', $api_name)->first();
    if (isset($zohofields) && $zohofields->data_type == 'lookup') {
      $lookup_value = $zohofields->lookup;
      $lookup_value = json_decode($lookup_value);
      $module = $lookup_value->module;
      if (!$this->getModuleLinkable($module)) {
        return false;
      }
      return $module;
    } else {
      return false;
    }
  }

  public function getAllfieldList($module = '')
  {
    $allfieldList = DB::table('zohofields')->select('*')->where('module', $module)->orderby('field_label', 'asc')->get();
    $fieldList = array();
    foreach ($allfieldList as $key => $field) {
      $fieldList[$field->api_name] = (array)$field;
    }
    return $fieldList;
  }

  public function checkLayoutIsExists($module = '')
  {

    $nzoho = new Nzoho;
    $contact_id = Auth::user()->contact_id;
    $contact_info  = Contacts::where('module_id', $contact_id)->first();
    $Account_Name_ID = $contact_info->Account_Name_ID;
    $User_Role_ID = Auth::user()->portal_layout_role;
    if (!isset($User_Role_ID)) {
      $User_Role_ID = 1;
    }
    $User_Role = Auth::user()->portal_layout_role_name;
    if ($module) {
      $layout_setting = $this->getDefaultLayout($module, $User_Role_ID);
      if ($layout_setting) {
        return $layout_setting;
      } else {
         $this->makeDefaultLayout($module, $User_Role_ID, $User_Role);
        $layout_setting = $this->getDefaultLayout($module, $User_Role_ID);
        return $layout_setting;
      }
    } else {
      return false;
    }
  }
  public function getDefaultLayout($module = '', $User_Role_ID = '')
  {
    $layout_setting = DB::table('layout_setting')->where('module_api_name', $module)->where('user_role_id', $User_Role_ID)->where('layout_id', '!=', null)->where('is_default', 1)->first();
    if ($layout_setting) {
      return $layout_setting->layout_id;
    } else {
      return false;
    }
  }

  public function makeDefaultLayout($module = '', $User_Role_ID = '', $User_Role = '')
  {
    $nzoho = new Nzoho;
    $getLayouts = $nzoho->getLayouts($module);
    $module_name = $this->getModuleNameByApi($module);

    $defaultLayout = array();
    $ignorLayoutFld = array('subform', 'profileimage');

    $defaultLayout['module_api_name'] = $module;
    $defaultLayout['user_role_id'] = $User_Role_ID;

    $relatedModuleList = $this->getRelatedModuleList($module);

    $crmLayouts = $nzoho->getLayouts($module);
    //return json_encode($crmLayouts);

    if (isset($crmLayouts[0])) {
      foreach ($crmLayouts as $key => $lDtl) {
        if (isset($lDtl->name) && $lDtl->name == 'Standard') {
          $DefSections = $lDtl->sections;
          $defaultLayout['layout_id'] = $lDtl->id;
          $defaultLayout['layout_name'] = $lDtl->name;

          $secInd = 0;
          $sectionId = 1;
          foreach ($DefSections as $skey => $secDtl) {
            if (empty($secDtl->fields)) continue;
            $sectionRequiredCheck = "false";

            $defaultLayout['sectionId'][$secInd] = $sectionId;
            $defaultLayout['sectiontype'][$secInd] = $secDtl->generated_type;
            $defaultLayout['sectiontitle'][$secInd] = $secDtl->display_label;

            $fldaddedInsec = 0;
            foreach ($secDtl->fields as $fkey => $fldDetails) {


              // if ($fldDetails->data_type != "RRULE" && $fldDetails->data_type !="ALARM" && $fldDetails->data_type !="multiselectlookup" && $fldDetails->data_type !="fileupload" ) {
              $fldaddedInsec++;

              $defaultLayout['fieldName'][] = $fldDetails->api_name . '___' . $fldDetails->field_label;
              $defaultLayout['fieldApiName'][] = $fldDetails->api_name;
              $defaultLayout['fieldId'][] = $fldDetails->id;
              $defaultLayout['layoutColumn'][] = '6';
              $defaultLayout['layoutFieldList'][] = (string) $skey . '_' . $fldDetails->api_name;
              $defaultLayout['fieldRequired'][] = $fldDetails->required == true ? "true" : "false";
              $defaultLayout['data_type'][] = $fldDetails->data_type;

              if ($fldDetails->required == true) {
                $sectionRequiredCheck = "true";
              }

              // }


            }

            $defaultLayout['columnAdded'][$secInd] = (string)$fldaddedInsec;

            if ($fldaddedInsec < 1) {
              unset($defaultLayout['sectiontype'][$secInd]);
              unset($defaultLayout['sectiontitle'][$secInd]);
              unset($defaultLayout['columnAdded'][$secInd]);
            } else {
              $secInd++;
            }
            $sectionId++;

            $defaultLayout['sectionRequired'][] = $sectionRequiredCheck;
          }

          foreach ($relatedModuleList as $key => $value) {

            if ($value['module_name'] == false) continue;
            $defaultLayout['relatedSectionTitle'][] = $value["display_label"];
            $defaultLayout['relatedModulesName'][] = $value["display_label"];

            $defaultLayout['relatedModulesAPIName'][] = $value['api_name'];
            $defaultLayout['relatedModulesLabel'][] = $value['display_label'];
            $defaultLayout['relatedModuleView'][] = "";
            $defaultLayout['relatedModuleField'][] = "";
            $defaultLayout['relatedModuleSectionTitle'][] = $value['display_label'];
            $defaultLayout['sectionView'][] = "";
            $defaultLayout['sectionCreate'][] = "";
            $defaultLayout['sectionEdit'][] = "";
            $defaultLayout['sectionDelete'][] = "";
          }


          $defaultLayout['lastSectionId'] = $secInd;
          $defaultLayout['lastSectionId'] = 6;
        }
      }
    }

    if (!empty($defaultLayout)) {
      $layData = array(
        'module_api_name' => $module,
        'user_role_id' => $User_Role_ID,
        'layout_id' => $defaultLayout['layout_id'],
        'lastSectionId' => $secInd,
        'layout_name' => $User_Role . " - " . $module_name . " - " . $defaultLayout['layout_name'],
        'section_data' => json_encode($defaultLayout),
        'is_default' => 1,
      );

      // return json_encode($defaultLayout);
      //return json_encode($layData);
      DB::table('layout_setting')->insert($layData);
      return $defaultLayout['layout_id'];
    }
  }

  public static function getLayoutSettingByModule($module = '', $Layout_ID = '')
  {
    $contact_id = Auth::user()->contact_id;
    $contact_info  = Contacts::where('module_id', $contact_id)->first();
    $Account_Name_ID = $contact_info->Account_Name_ID;
    $User_Role_ID = Auth::user()->portal_layout_role;
    if (!isset($User_Role_ID)) {
      $User_Role_ID = 1;
    }
    if ($module) {
      // $layout_setting = DB::table('layout_setting')->where('module_api_name',$module)->where('user_role_id',$User_Role_ID)->where('layout_id',$Layout_ID)->first();
      $layout_setting = DB::table('layout_setting')->where('module_api_name', $module)->where('user_role_id', $User_Role_ID)->where('layout_id', $Layout_ID)->first();
      if ($layout_setting) {
        return $layout_setting;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public static function getLayoutSettingByModuleUserRoleID($module = '')
  {
    $contact_id = Auth::user()->contact_id;
    $contact_info  = Contacts::where('module_id', $contact_id)->first();
    $Account_Name_ID = $contact_info->Account_Name_ID;
    $User_Role_ID = Auth::user()->portal_layout_role;
    if (!isset($User_Role_ID)) {
      $User_Role_ID = 1;
    }
    if ($module) {
      // $layout_setting = DB::table('layout_setting')->where('module_api_name',$module)->where('user_role_id',$User_Role_ID)->where('layout_id',$Layout_ID)->first();
      $layout_setting = DB::table('layout_setting')->where('module_api_name', $module)->where('user_role_id', $User_Role_ID)->first();
      if ($layout_setting) {
        return $layout_setting;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function getRelatedView($module = '', $relatedModule = '')
  {
    $contact_id = Auth::user()->contact_id;
    $relatedView = DB::table('view_creation')
      ->where('contact_id', $contact_id)
      ->where('module', $module)
      ->where('related_module', $relatedModule)
      ->where('is_default', 1)
      ->where('is_related_module', 1)
      ->first();
    if ($relatedView != "") {
      return $relatedView;
    } else {
      $this->createDefaultViewRelatedModule($module, $relatedModule);
      return $this->getDefaultViewRelated($module, $relatedModule);
    }
  }

  public function makeDefaultView($view_id = '', $module = '')
  {
    $contact_id = Auth::user()->contact_id;
    //Unset default for old record dont remove this
    DB::table('view_creation')->where('contact_id', $contact_id)->where('module', $module)->where('is_default', 1)->where('is_related_module', 0)->update(['is_default' => 0]);
    if ($view_id) {
      $default_view = DB::table('view_creation')->where('id', $view_id)->update(['is_default' => 1]);
      // print_r($default_view);
      if ($default_view) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function makeDefaultView_activities($view_id = '', $module = '')
  {
    $contact_id = Auth::user()->contact_id;
    //Unset default for old record dont remove this
    DB::table('view_creation')->where('contact_id', $contact_id)->where('module', $module)->where('is_default', 1)->where('is_related_module', 0)->update(['is_default' => 0]);

    DB::table('view_creation')->where('contact_id', $contact_id)->where('module', "activities")->where('is_default', 1)->where('is_related_module', 0)->update(['is_default' => 0]);
    if ($view_id) {
      $default_view = DB::table('view_creation')->where('id', $view_id)->update(['is_default' => 1]);
      // print_r($default_view);
      if ($default_view) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function createDefaultViewRelatedModule($module = '', $relatedModule = '')
  {
    $contact_id = Auth::user()->contact_id;
    $contact_info  = DB::table('zc_contacts')->where('module_id', $contact_id)->first();
    $Account_Name_ID = $contact_info->Account_Name_ID;

    DB::table('view_creation')
      ->where('contact_id', $contact_id)
      ->where('module', $module)
      ->where('related_module', $relatedModule)
      ->where('is_related_module', 1)
      ->delete();

    $systemReqField = array('Name', 'Owner', 'Modified_Time');
    $selected_fields = implode(',', $systemReqField);

    $modified_criteria = array(
      'field_name' => array("Modified_Time___datetime"),
      'condition' => array("ORDER_BY")
    );

    $module_name = $this->getModuleNameByApi($module);

    $data = array(
      array(
        'contact_id' => $contact_id,
        'account_id' => $Account_Name_ID,
        'module' => $module,
        'related_module' => $relatedModule,
        'view_name' => "All " . $module_name,
        'selected_fields' => $selected_fields,
        'criteria' => "",
        'is_default' => 1,
        'is_related_module' => 1
      )
    );

    $default_view = DB::table('view_creation')->insert($data);
    if ($default_view) {
      return true;
    } else {
      return false;
    }
  }

  public function getDefaultViewRelated($module = '', $relatedModule = '')
  {
    $contact_id = Auth::user()->contact_id;
    $default_view = DB::table('view_creation')
      ->where('contact_id', $contact_id)
      ->where('module', $module)
      ->where('related_module', $relatedModule)
      ->where('is_default', 1)
      ->where('is_related_module', 1)
      ->first();
    if ($default_view) {
      return $default_view;
    } else {
      return false;
    }
  }

  public function getAllfieldListViewRelated($module = '', $selected_fields = '')
  {
    $selected_fields = explode(",", $selected_fields);
    $getAllfieldListView = $this->getAllfieldListView($module);

    return $getAllfieldListView;

    foreach ($selected_fields as $viewFld) {
      if (isset($getAllfieldListView[$viewFld])) {
        $getAllFieldsList[$viewFld] = $getAllfieldListView[$viewFld];
        unset($getAllfieldListView[$viewFld]);
      }
    }
    $getAllfieldListView = array_merge($getAllFieldsList, $getAllfieldListView);
    return $getAllfieldListView;
  }

  public static function getFieldPermission($module = '', $fieldApiName)
  {
    $User_Role_ID = '';
    $contact_id = Auth::user()->contact_id;
    $contact_info  = Contacts::where('module_id', $contact_id)->first();
    $Account_Name_ID = $contact_info->Account_Name_ID;
    $User_Role_ID = Auth::user()->portal_layout_role;
    if (!isset($User_Role_ID)) {
      $User_Role_ID = 1;
    }

    // $user_permission = DB::table('layout_field_permission')->where('module_id',$module)->where('user_role_id',$User_Role_ID)->where('field_api_name',$fieldApiName)->first();
    $user_permission = DB::table('layout_field_permission')->where('module_id', $module)->where('user_role_id', $User_Role_ID)->where('field_api_name', $fieldApiName)->first();
    if ($user_permission) {
      return $user_permission->field_permission;
    } else {
      return 1;
    }
  }

  public function portalUserActivityLog($page = '', $action = "", $message = '')
  {
    $CONTACTID = '';
    $ACCOUNTID = '';
    $CONTACTID = Auth::user()->contact_id;
    $full_name = Auth::user()->full_name;
    $profile   = Contacts::where('module_id', $CONTACTID)->first();
    $ACCOUNTID = $profile->Account_Name_ID;

    $insertArr1 = array();
    $insertArr1['ACCOUNTID'] = $ACCOUNTID;
    $insertArr1['CONTACTID'] = $CONTACTID;
    $insertArr1['modified_by'] = $full_name;
    $insertArr1['page'] = $page;
    $insertArr1['action'] = $action;
    $insertArr1['message'] = $message;
    $insert = DB::table('portal_user_activity_log')->insert($insertArr1);
  }
  public function getOpenMyTasks($module = '', $module_id = '')
  {
    $zc_tasks = array();
    $zc_calls = array();
    $zc_events = array();
    $allActivitis = array();
    $finalActivitis = array();
    if ($module == 'Contacts') {
      if (\Schema::hasTable('zc_tasks')) $zc_tasks = DB::table('zc_tasks')->where('Who_Id_ID', '=', $module_id)->get();
      if (\Schema::hasTable('zc_calls')) $zc_calls = DB::table('zc_calls')->where('Who_Id_ID', '=', $module_id)->get();
      if (\Schema::hasTable('zc_events')) $zc_events = DB::table('zc_events')->where('Who_Id_ID', '=', $module_id)->get();

      $allActivitis[] = $zc_tasks;
      $allActivitis[] = $zc_calls;
      $allActivitis[] = $zc_events;
    } else {
      if (\Schema::hasTable('zc_tasks')) $zc_tasks = DB::table('zc_tasks')->where('What_Id_ID', '=', $module_id)->get();
      if (\Schema::hasTable('zc_calls')) $zc_calls = DB::table('zc_calls')->where('What_Id_ID', '=', $module_id)->get();
      if (\Schema::hasTable('zc_events')) $zc_events = DB::table('zc_events')->where('What_Id_ID', '=', $module_id)->get();

      $allActivitis[] = $zc_tasks;
      $allActivitis[] = $zc_calls;
      $allActivitis[] = $zc_events;
    }

    foreach ($allActivitis as $key => $val) {
      foreach ($val as $key => $value) {
        $finalActivitis[] = $value;
      }
    }
    return $finalActivitis;
  }

  public function getAllRelatedSettingsByModule($module = '')
  {
    $settings = DB::table('user_related_module_settings')->where('module_id', $module)->where('status', '1')->where('lookup_field', '!=', '')->get();
    if (isset($settings)) {
      return $settings;
    }
    return false;
  }

  public function getContactByAccountId($acc_id = '')
  {
    $contact_list = array();
    if (isset($acc_id) && $acc_id != '') {
      $contact_list = DB::table('zc_contacts')->select('module_id', 'Last_Name', 'First_Name', 'Account_Name_ID')->where('Account_Name_ID', $acc_id)->orderby('Created_Time', 'asc')->get();
      return $contact_list;
    } else {
      return false;
    }
  }

  public static function getPicListDisplayValue($picklist, $fValue)
  {
    foreach ($picklist as $key => $option) {
      $ac_value = $option["actual_value"];
      $d_value = $option["display_value"];
      if ($fValue == $ac_value || $fValue == $d_value) return $d_value;
    }
    return "";
  }

  public function getLoginAccount()
  {
    $contact_id = Auth::user()->contact_id;
    $contact_info  = Contacts::where('module_id', $contact_id)->first();

    $data = array(
      'Account_Name' => $contact_info->Account_Name,
      'Account_Name_ID' => $contact_info->Account_Name_ID,
    );
    if (isset($contact_info->Account_Name_ID)) {
      return $data;
    } else {
      return false;
    }
  }

  public function getDefaultIncident()
  {
    $contact_id = Auth::user()->contact_id;
    $default_incident = DB::table('default_incident_set')->where('contact_id', $contact_id)->first();
    if ($default_incident) {
      return $default_incident->incident_id;
    } else {
      return false;
    }
  }

  public function getModuleRecordByID($module_id = '', $module = '')
  {
    if (isset($module_id) && $module_id != '' && isset($module) && $module != '') {

      if ($module == "se_module") {
        $record_list = DB::table("module_list")->get();
        return $record_list;
      }

      $table_name = "zc_" . strtolower($module);
      $moduleData = DB::table($table_name)->where('module_id', $module_id)->first();
      if ($moduleData) {
        return $moduleData;
      }
    }
    return false;
  }

  public function getIncidentFieldNameByModule($module = '')
  {
    if ($module == '' || !isset($module)) {
      return false;
    }
    if ($module == "Passengers" || $module == "Groups" || $module == "Family_Members") {
      return "Incidents_ID";
    } elseif ($module == "Support_Personnel" || $module == "Activations" || $module == "Inquiries1" || $module == "Payments" || $module == "Locations" || $module == "Group_Relationships" || $module == "Deployments") {
      return "Incident_ID";
    } elseif ($module == 'Incidents') {
      return "module_id";
    } else {
      return false;
    }
  }

  public function getRelatedLookupFieldByID($user_role_id, $module, $relatedModule)
  {
    $layout = $this->checkLayoutIsExists($module);
    $layout_setting = DB::table('layout_setting')->where('user_role_id', $user_role_id)->where('layout_id', $layout)->first();
    if (isset($layout_setting) && $layout_setting != '') {
      $section_data = $layout_setting->section_data;
      if ($section_data != '') {
        $section_data = json_decode($section_data);
        $relatedModulesAPIName = $section_data->relatedModulesAPIName;
        $relatedModuleField = $section_data->relatedModuleField;
        foreach ($relatedModulesAPIName as $rapi_key => $rapi_value) {
          if ($rapi_value == $relatedModule) {
            return $relatedModuleField[$rapi_key];
          }
        }
      }
    }
    return false;
  }

  public function getDataByModuleAndIncident($module)
  {
    $contact_id = Auth::user()->contact_id;
    $contact_info = Contacts::where('module_id', $contact_id)->first();
    $Account_Name_ID = $contact_info->Account_Name_ID;
    $Portal_User_Role_ID = Auth::user()->portal_layout_role;
    if (!isset($Portal_User_Role_ID)) {
      $Portal_User_Role_ID = 1;
    }
    $module_name = $module;
    $table = 'zc_' . strtolower($module);
    $locupRltArr = DB::table("user_module_permission")->where("module_id", $module_name)->where('user_role_id', $Portal_User_Role_ID)->first();
    // if(\Schema::hasTable($table)){
    $sql = DB::table('zc_' . strtolower($module));

    if (isset($locupRltArr->id)) {

      if ($locupRltArr->foriegn_module == "Accounts") {
        $sql->where($locupRltArr->foriegn_key . "_ID", $Account_Name_ID);
      } else if ($locupRltArr->foriegn_module == "") {

        $sql->where("module_id", '!=', null);
      } else {

        $zohofieldsDt = DB::table('zohofields')->select('id', 'api_name', 'field_label', 'lookup')->where('module', $locupRltArr->foriegn_module)->where('data_type', 'lookup')->where('lookup', "LIKE", "%Accounts%")->first();

        if (isset($zohofieldsDt->id)) {

          $relMdlDt = DB::table("zc_" . strtolower($locupRltArr->foriegn_module))->select('module_id')->where($zohofieldsDt->api_name . "_ID", $Account_Name_ID)->get();

          $relMdlIds = [];
          foreach ($relMdlDt as $key => $value) {
            $relMdlIds[]  = $value->module_id;
          }

          $sql->whereIn($locupRltArr->foriegn_key . "_ID", $relMdlIds);
        } else {
          $sql->where("module_id", '=', null);
        }
      }
    } else {
      $sql->where("module_id", '!=', null);
    }

    $result = $sql->get();

    $val = array();


    if ($table == "zc_accounts") {
      $fieldname = "Account_Name";
    } elseif ($table == "zc_contacts") {
      $fieldname = "Full_Name";
    } else if ($table == "zc_cases") {
      $fieldname = "Subject";
    } elseif ($table == "zc_vendors") {
      $fieldname = "Contact_Person";
    } elseif ($table == "zc_campaigns") {
      $fieldname = "Campaign_Name";
    } elseif ($table == "zc_attachments") {
      $fieldname = "File_Name";
    } elseif ($table == "zc_calls") {
      $fieldname = "Subject";
    } elseif ($table == "zc_leads") {
      $fieldname = "Full_Name";
    } elseif ($table == "zc_deals") {
      $fieldname = "Deal_Name";
    } elseif ($table == "zc_events") {
      $fieldname = "Event_Title";
    } elseif ($table == "zc_notes") {
      $fieldname = "Note_Title";
    } elseif ($table == "zc_products") {
      $fieldname = "Product_Name";
    } elseif ($table == "zc_tasks") {
      $fieldname = "Subject";
    } else {
      $fieldname = "Name";
    }

    if (count($result) == 0) {
      $val[] = [
        "label" => "No data",
        "value" => null
      ];
    }

    foreach ($result as $key => $value) {


      $val[] = [
        "label" => isset($value->$fieldname) ? $value->$fieldname : "",
        "value" => $value->module_id
      ];
    }

    return $val;
    // }else{
    //   $val = array();
    //   // $val[] = [
    //   //             "label" => "no data",
    //   //             "value" => null
    //   //           ];
    //   return $val;
    // }



  }

  public function haveUserModulePermission($user_role_id, $module)
  {
    $var = DB::table('user_module_permission')->where('module_id', $module)->where('user_role_id', $user_role_id)->first();

    if ($var) {
      if ($var->create == 1 || $var->delete == 1 || $var->edit == 1 || $var->view == 1) {
        return true;
      }
      return;
    }
    return;
  }

  public function getModuleSingularNameByApi($module = '')
  {
    $moduleList = DB::table('module_list')->where('api_name', $module)->first();
    if ($moduleList) {
      return $moduleList->singular_label;
    }
    return false;
  }

  public function getLayoutFields($module = '', $layout_id = '')
  {
    $User_Role_ID = '';
    $contact_id = Auth::user()->contact_id;
    $contact_info  = Contacts::where('module_id', $contact_id)->first();
    $Account_Name_ID = $contact_info->Account_Name_ID;
    $User_Role_ID = $contact_info->User_Role_ID;

    $zc_layouts_se = DB::table('layout_setting')
      ->where('module_api_name', $module)
      ->where('layout_id', $layout_id)
      ->where('user_role_id', $User_Role_ID)
      ->first();

    $fields_in_section_added = array();
    if (isset($zc_layouts_se->section_data)) {
      $section_data = json_decode($zc_layouts_se->section_data, true);
      for ($i = 0; $i < count($section_data['fieldName']); $i++) {
        $fieldApiName = $section_data['fieldApiName'][$i];
        $fieldId = $section_data['fieldId'][$i];
        $exfldN = $section_data['fieldName'][$i];
        $fieldnameArr = explode("___", $exfldN);
        if (isset($fieldnameArr[1])) {
          $fieldLabel = $fieldnameArr[1];
        }
        $sectionField = array(
          'api_name' => $fieldApiName,
          'field_id' => $fieldId,
          'field_label' => $fieldLabel,
        );
        $fields_in_section_added[$fieldApiName] = $sectionField;
      }
      ksort($fields_in_section_added);
    }
    return $fields_in_section_added;
  }
}