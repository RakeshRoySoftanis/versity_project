<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Schema;

use App\Models\Contacts;
use App\Models\Nzoho;
use App\Models\Common;
use Exception;
use Illuminate\Support\Facades\DB;
// use App\Nzoho;
// use App\Common;
// use File;

class ActivitiesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    
    /*
    |--------------------------------------------------------------------------
    | Profile Tab Activity Log Functions
    |--------------------------------------------------------------------------
    */
    function my_activity(){

        $offset = ( $_GET['page'] - 1 ) * $_GET['per_page'];
        $sort_field = (isset($_GET['sort_field'])) ? $_GET['sort_field'] : "id";
        $sort_order = (isset($_GET['sort_order'])) ? $_GET['sort_order'] : "desc";

        try{
            $contact_id = Auth::user()->contact_id;
            $contact_info  = Contacts::where('module_id', $contact_id)->first();
            
            $sql = DB::table('portal_activity_log')->where('ACCOUNTID',$contact_info->Account_Name_ID);
                //search conditions
                if(isset($_GET['search']) && ($_GET['search'] != "")){
                    $q = $_GET['search'];
                    $sql->where(function($query) use ($q) {
                        $query->where('page', 'LIKE', '%'.$q.'%')
                            ->orWhere('action', 'LIKE', '%'.$q.'%')
                            ->orWhere('message', 'LIKE', '%'.$q.'%');
                    });
                }
            $data = $sql->offset($offset)->limit($_GET['per_page'])->orderBy($sort_field, $sort_order)->get();
            
            //count of total rows
            $csql = DB::table('portal_activity_log')->where('ACCOUNTID',$contact_info->Account_Name_ID);
                //search conditions for count
                if(isset($_GET['search']) && ($_GET['search'] != "")){
                    $q = $_GET['search'];
                    $csql->where(function($query) use ($q) {
                        $query->where('page', 'LIKE', '%'.$q.'%')
                            ->orWhere('action', 'LIKE', '%'.$q.'%')
                            ->orWhere('message', 'LIKE', '%'.$q.'%');
                    });
                }
            $data_count = $csql->count();
            //response
            return response([
                'message' => "",
                'activity_list' => $data,
                'totalRows' => $data_count,
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    
    /*
    |--------------------------------------------------------------------------
    | Contact Functions
    |--------------------------------------------------------------------------
    */
    public function zc_contacts(){

        $offset = ( $_GET['page'] - 1 ) * $_GET['per_page'];
        $sort_field = (isset($_GET['sort_field'])) ? $_GET['sort_field'] : "id";
        $sort_order = (isset($_GET['sort_order'])) ? $_GET['sort_order'] : "desc";

        try{
            $contact_id = Auth::user()->contact_id;
            $contact_info  = Contacts::where('module_id', $contact_id)->first();
            
            $account_info = array();
            if (!isset($contact_info->module_id) || ($contact_info->Account_Name_ID == ""))$account_info = DB::table('zc_accounts')->where('module_id',$contact_info->Account_Name_ID)->first();


            $sql = DB::table('zc_contacts')->where('Account_Name_ID',$contact_info->Account_Name_ID);
                //search conditions
                if(isset($_GET['search']) && ($_GET['search'] != "")){
                    $q = $_GET['search'];
                    $sql->where(function($query) use ($q) {
                        $query->where('module_id', 'LIKE', '%'.$q.'%')
                            ->orWhere('Account_Name', 'LIKE', '%'.$q.'%')
                            ->orWhere('Full_Name', 'LIKE', '%'.$q.'%')
                            ->orWhere('Email', 'LIKE', '%'.$q.'%');
                    });
                }
            $data = $sql->offset($offset)->limit($_GET['per_page'])->orderBy($sort_field, $sort_order)->get();
            
            //count of total tasks
            $csql = DB::table('zc_contacts')->where('Account_Name_ID',$contact_info->Account_Name_ID);
                //search conditions for count
                if(isset($_GET['search']) && ($_GET['search'] != "")){
                    $q = $_GET['search'];
                    $csql->where(function($query) use ($q) {
                        $query->where('module_id', 'LIKE', '%'.$q.'%')
                            ->orWhere('Account_Name', 'LIKE', '%'.$q.'%')
                            ->orWhere('Full_Name', 'LIKE', '%'.$q.'%')
                            ->orWhere('Email', 'LIKE', '%'.$q.'%');
                    });
                }
            $data_count = $csql->count();
            //response
            return response([
                'message' => "",
                'account_info' => $account_info,
                'contact_info' => $data,
                'totalRows' => $data_count,
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function save_contact_note(Request $request)
    {
        try{
            
            $crmid = $request->module_id;
            $ndata = array();
            $ndata['Parent_Id'] = array('id' => $request->module_id);
            $ndata['Note_Title'] = $request->Note_Title;
            $ndata['Note_Content'] = $request->notes;
            $ndata['Created_By'] = array('id' => $request->owner_id);
            $ndata['Owner'] = array('id' => $request->owner_id);
            $ndata['$se_module'] = 'Contacts';


            $zoho = New Nzoho;
            $zohoData[0] = $ndata;
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->insertRecords($zohoData, 'Notes');
            
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
                if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
                    $notes_id = $insertZoho->data[0]->details->id;
                    $Common = new Common();
                    $Common->sync_getRecordsById($notes_id, "Notes");

                    if($_FILES['notesImage']['error'] == 0){
                        $target_dir = "public/uploadDir/notes/".$crmid."/".$notes_id;
                        $sourcePath = $_FILES['notesImage']['tmp_name'];
                        if (!file_exists($target_dir)) {
                            mkdir($target_dir, 0777, true);
                        }
                        $targetPath = $target_dir.'/'.$_FILES['notesImage']['name'];
                        move_uploaded_file($sourcePath,$targetPath);

                        $file_full_path = base_path()."/".$targetPath;
                        $zoho->uploadFile('Notes',$file_full_path,$notes_id);
                    }
                    
                    return response([
                        'message' => "Information added successfully !",
                    ], 200);

                }else{
                    $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';

                    return response([
                        'message' => $error,
                    ], 200);
                }
            }else{

                return response([
                        'message' => "Information added successfully !",
                    ], 200);
            }

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function save_contact_meeting(Request $request)
    {
        try{

            $crmid = $request->module_id;
            $contact_info  = Contacts::where('module_id', $crmid)->first();
            
            $event_data[0] = array(
                'Event_Title' =>$request->Event_Title, 
                'Venue' => $request->Venue,
                'Start_DateTime' => date('c',strtotime($request->Start_DateTime)),
                'End_DateTime' => date('c',strtotime($request->End_DateTime)),
                'Description' => $request->details,
                'Who_Id' => array('id' => $crmid),
                'se_module' => 'Contacts',
            );

            if (isset($contact_info->module_id) && ($contact_info->Account_Name_ID != "")){
                $event_data[0]['What_Id'] = array('id' => $contact_info->Account_Name_ID);
                $event_data[0]['se_module'] = 'Accounts';
            }

            $zoho = New Nzoho;
            
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->insertRecords($event_data, 'Events');

            if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
                if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
                    $record_id = $insertZoho->data[0]->details->id;
                    $Common = new Common();
                    $Common->sync_getRecordsById($record_id, "Events");

                    return response([
                        'message' => "Information added successfully !",
                    ], 200);

                    return redirect()->back()->with(array("success"=>"Information added successfully !"));
                }else{
                    $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';

                    return response([
                        'message' => $error,
                    ], 200);
                }
            }else{
                return redirect()->back()->with(array("success"=>"Information added successfully !"));
            }


            return response([
                'message' => "Successfull",
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update_contact_meeting(Request $request)
    {
        try{
            $crmid = $request->module_id;
            $contact_id = $request->contact_id;

            $contact_info  = Contacts::where('module_id', $crmid)->first();
            
            $event_data[0] = array(
                'id' => $request->module_id,
                'Event_Title' =>$request->Event_Title, 
                'Venue' => $request->Venue,
                'Start_DateTime' => date('c',strtotime($request->Start_DateTime)),
                'End_DateTime' => date('c',strtotime($request->End_DateTime)),
                'Description' => $request->details,
                'Who_Id' => array('id' => $contact_id),
                'se_module' => 'Contacts',
            );

            if (isset($contact_info->module_id) && ($contact_info->Account_Name_ID != "")){
                $event_data[0]['What_Id'] = array('id' => $contact_info->Account_Name_ID);
                $event_data[0]['se_module'] = 'Accounts';
            }

            $zoho = New Nzoho;
            
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->updateRecordsPO($event_data, 'Events' ,  "false");

            if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
                if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
                    $record_id = $insertZoho->data[0]->details->id;
                    $Common = new Common();
                    $Common->sync_getRecordsById($record_id, "Events");

                    return response([
                        'message' => "Information added successfully !",
                    ], 200);

                    return redirect()->back()->with(array("success"=>"Information added successfully !"));
                }else{
                    $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';
                    return response([
                        'message' => $error,
                    ], 400);
                }
            }else{
                return redirect()->back()->with(array("success"=>"Information added successfully !"));
            }

            return response([
                'message' => "Successfull",
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function delete_contact_meeting()
    {
        try {

            $id = $_GET['module_id'];

            $zoho = New Nzoho;
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$zoho->deleteRecords($id, 'Events');

            DB::table('zc_events')->where('module_id',$id)->delete();

            return response([
                'message' => "Successfully Deleted."
            ], 200);
            
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function save_contact_call(Request $request)
    {
        try{
            $crmid = $request->module_id;
            $contact_info  = Contacts::where('module_id', $crmid)->first();
            
            if( $request->Call_Status == "Completed" ){

                $call_data[0] = array(
                    'Subject' =>$request->call_subject, 
                    'Call_Purpose' => $request->call_purpose,
                    'Contact_Name' => "Contact",
                    'Call_Type' => $request->call_type,
                    'Call_Status' => $request->Call_Status,
                    'Call_Duration' => $request->call_duration,
                    'Call_Start_Time' => date( 'c', strtotime($request->call_start_time)),
                    'Description' => $request->details,
                    'Who_Id' => array('id' => $crmid),
                    'se_module' => "Contacts"
                );

            }else if( $request->Call_Status == "Scheduled" ){

                $call_data[0] = array(
                    'Subject' =>$request->call_subject, 
                    'Call_Purpose' => $request->call_purpose,
                    'Contact_Name' => "Contact",
                    'Call_Type' => $request->call_type,
                    'Call_Status' => $request->Call_Status,
                    'Call_Start_Time' => date( 'c', strtotime($request->call_start_time) ),
                    'Description' => $request->details,
                    'Who_Id' => array('id' => $crmid),
                    'se_module' => "Contacts"
                );

            }

            // else{
            //     $call_data[0] = array(
            //         'Subject' =>$request->call_subject, 
            //         'Call_Purpose' => $request->call_purpose,
            //         'Contact_Name' => "Contact",
            //         'Call_Type' => $request->call_type,
            //         'Call_Status' => $request->call_detail,
            //         'Description' => $request->details,
            //         'Who_Id' => array('id' => $crmid),
            //         'se_module' => "Contacts"
            //     );
            // }
                 
            if (isset($contact_info->module_id) && ($contact_info->Account_Name_ID != "")){
                $call_data[0]['What_Id'] = array('id' => $contact_info->Account_Name_ID);
                $call_data[0]['se_module'] = 'Accounts';
            }


            $zoho = New Nzoho;
            
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->insertRecords($call_data, 'Calls');
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
                if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
                    $record_id = $insertZoho->data[0]->details->id;
                    $Common = new Common();
                    $Common->sync_getRecordsById($record_id, "Calls");

                    return response([
                        'message' => "Information added successfully !",
                    ], 200);
                }else{
                    $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';

                    return response([
                        'message' => $error,
                    ], 400);
                }
            }else{

                return response([
                    'message' => "Information added successfully !",
                ], 200);
            }
 

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    function updateContactCall(Request $request)
    {
        try{
            $crmid = $request->module_id;
            $contact_id = $request->contact_id;
            $contact_info  = Contacts::where('module_id', $crmid)->first();

            $call_data = $request->except(['AuthToken', 'module_id', 'Call_Start_Time' , 'contact_id']);
            $call_data['id'] = $request->module_id;
            $call_data['Call_Start_Time'] = date( 'c', strtotime($request->Call_Start_Time));
            $call_data['Who_Id'] = array('id' => $contact_id);
            $call_data['se_module'] = "Contacts";
            $call_data['Contact_Name'] = "Contact";
            if (isset($contact_info->module_id) && ($contact_info->Account_Name_ID != "")){
                $call_data['What_Id'] = array('id' => $contact_info->Account_Name_ID);
                $call_data['se_module'] = 'Accounts';
            }

            $zoho = New Nzoho;
            
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->updateRecordsPO(array($call_data), 'Calls' , "false");
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
                if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
                    $record_id = $insertZoho->data[0]->details->id;
                    $Common = new Common();
                    $Common->sync_getRecordsById($record_id, "Calls");

                    return response([
                        'message' => "Information Updated successfully !",
                    ], 200);
                }else{
                    $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';

                    return response([
                        'message' => $error
                    ], 400);
                }
            }else{

                return response([
                    'message' => "Information Updated successfully !",
                ], 200);
            }
 

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function deleteContactCall($id)
    {
        try{
            $zoho = New Nzoho;
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$zoho->deleteRecords($id, 'Calls');

            DB::table('zc_calls')->where('module_id',$id)->delete();
            //code here
            return response([
                'message' => "Successfully Deleted."
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }

    }

    public function save_contact_task(Request $request)
    {
        try{
            $crmid = $request->module_id;
            $contact_info  = Contacts::where('module_id', $crmid)->first();

            $task_data[0] = array(
                'Subject' =>$request->task_subject, 
                'Due_Date' => date('Y-m-d',strtotime($request->task_due_date)),
                'Status' => $request->task_status,
                'Priority' => $request->task_priority,
                'Who_Id' => array('id' => $crmid),
                'Description' => $request->details,
                'se_module' => "Contacts",
            );
            
            if (isset($contact_info->module_id) && ($contact_info->Account_Name_ID != "")){
                $task_data[0]['What_Id'] = array('id' => $contact_info->Account_Name_ID);
                $task_data[0]['se_module'] = 'Accounts';
            }

            $zoho = New Nzoho;
            
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->insertRecords($task_data, 'Tasks');

            if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
                if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
                    $record_id = $insertZoho->data[0]->details->id;
                    $Common = new Common();
                    $Common->sync_getRecordsById($record_id, "Tasks");

                    return response([
                        'message' => "Information added successfully !",
                    ], 200);


                }else{
                    $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';
                    return redirect()->back()->with(array("error"=>$error));
                }
            }else{

                return response([
                    'message' => "Information added successfully !",
                ], 200);
            }

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update_contact_task(Request $request)
    {
        try{
            $crmid = $request->module_id;
            $contact_id = $request->contact_id;
            $contact_info  = Contacts::where('module_id', $crmid)->first();

            $task_data[0] = array(
                'id' => $crmid,
                'Subject' =>$request->Subject, 
                'Due_Date' => date('Y-m-d',strtotime($request->Due_Date)),
                'Status' => $request->Status,
                'Priority' => $request->task_priority,
                'Who_Id' => array('id' => $contact_id),
                'Description' => $request->details,
                'se_module' => "Contacts",
            );
            
            if (isset($contact_info->module_id) && ($contact_info->Account_Name_ID != "")){
                $task_data[0]['What_Id'] = array('id' => $contact_info->Account_Name_ID);
                $task_data[0]['se_module'] = 'Accounts';
            }
            $zoho = New Nzoho;
            
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->updateRecordsPO($task_data, 'Tasks' , 'false');

            if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
                if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
                    $record_id = $insertZoho->data[0]->details->id;
                    $Common = new Common();
                    $Common->sync_getRecordsById($record_id, "Tasks");

                    return response([
                        'message' => "Information Updated successfully !",
                    ], 200);


                }else{
                    $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';
                    return response([
                        'message' => $error,
                    ], 400);
                }
            }else{

                return response([
                    'message' => "Information Updated successfully !",
                ], 200);
            }

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    function delete_contact_task($id)
    {
        try{

            $zoho = New Nzoho;
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$zoho->deleteRecords($id, 'Tasks');

            DB::table('zc_tasks')->where('module_id',$id)->delete();
            //code here
            return response([
                'message' => "Successfully Deleted."
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
    }


    // public function contact_details($contact_id='')
    // {
    //     $data = array();
    //     $pg = array();
    //     $data['active_menu'] = 'zc-contacts';

    //     if(Schema::hasTable('zc_contacts'))$pg['contact_info'] = DB::table('zc_contacts')->where('module_id',$contact_id)->first();
    //     if(Schema::hasTable('zc_notes'))$pg['zc_notes'] = DB::table('zc_notes')->where('Parent_Id_ID',$contact_id)->get();
    //     if(Schema::hasTable('zc_events'))$pg['zc_events'] = DB::table('zc_events')->where('Who_Id_ID',$contact_id)->get();
    //     if(Schema::hasTable('zc_calls'))$pg['zc_calls'] = DB::table('zc_calls')->where('Who_Id_ID',$contact_id)->get();
    //     if(Schema::hasTable('zc_tasks'))$pg['zc_tasks'] = DB::table('zc_tasks')->where('Who_Id_ID',$contact_id)->get();

    //     // $common = new Common();
    //     // $data['template_api_name'] = $template_api_name = $common->getActiveTheme();

    //     // return view('template/'.$template_api_name.'/contacts/contacts_details',['data' => $data,'pg'=>$pg]);
    // }

    // public function submit_notes(Request $request)
    // {
    //     $crmid = $request->module_id;
    //     $ndata = array();
    //     $ndata['Parent_Id'] = array('id' => $request->module_id);
    //     $ndata['Note_Title'] = $request->Note_Title;
    //     $ndata['Note_Content'] = $request->notes;
    //     $ndata['Created_By'] = array('id' => $request->owner_id);
    //     $ndata['Owner'] = array('id' => $request->owner_id);
    //     $ndata['$se_module'] = 'Contacts';

    //     $zoho = New Nzoho;
    //     $zohoData[0] = $ndata;
    //     if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
    //     if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->insertRecords($zohoData, 'Notes');
    //     if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
    //         if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
    //             $notes_id = $insertZoho->data[0]->details->id;
    //             $Common = new Common();
    //             $Common->sync_getRecordsById($notes_id, "Notes");

    //             return redirect()->back()->with(array("success"=>"Information added successfully !"));
    //         }else{
    //             $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';
    //             return redirect()->back()->with(array("error"=>$error));
    //         }
    //     }else{
    //         return redirect()->back()->with(array("success"=>"Information added successfully !"));
    //     }
    // }


    // public function submit_meeting(Request $request)
    // {
    //     $crmid = $request->module_id;
    //     $contact_info  = Contacts::where('module_id', $crmid)->first();
        
    //     $event_data[0] = array(
    //         'Event_Title' =>$request->Event_Title, 
    //         'Venue' => $request->Venue,
    //         'Start_DateTime' => date('c',strtotime($request->Start_DateTime)),
    //         'End_DateTime' => date('c',strtotime($request->End_DateTime)),
    //         'Description' => $request->details,
    //         'Who_Id' => array('id' => $crmid),
    //         'se_module' => 'Contacts',
    //     );

    //     if (isset($contact_info->module_id) && ($contact_info->Account_Name_ID != "")){
    //         $event_data[0]['What_Id'] = array('id' => $contact_info->Account_Name_ID);
    //         $event_data[0]['se_module'] = 'Accounts';
    //     }

    //     $zoho = New Nzoho;
        
    //     if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
    //     if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->insertRecords($event_data, 'Events');

    //     if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
    //         if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
    //             $record_id = $insertZoho->data[0]->details->id;
    //             $Common = new Common();
    //             $Common->sync_getRecordsById($record_id, "Events");

    //             return redirect()->back()->with(array("success"=>"Information added successfully !"));
    //         }else{
    //             $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';
    //             return redirect()->back()->with(array("error"=>$error));
    //         }
    //     }else{
    //         return redirect()->back()->with(array("success"=>"Information added successfully !"));
    //     }

    // }


    // public function submit_call(Request $request){
    //     $crmid = $request->module_id;
    //     $contact_info  = Contacts::where('module_id', $crmid)->first();
        
    //     if( $request->call_detail == "CompletedCall" ){

    //         $call_data[0] = array(
    //             'Subject' =>$request->call_subject, 
    //             'Call_Purpose' => $request->call_purpose,
    //             'Contact_Name' => "Contact",
    //             'Call_Type' => $request->call_type,
    //             'Call_Details' => $request->call_detail,
    //             'Call_Duration' => $request->call_duration,
    //             'Call_Start_Time' => date( 'c', strtotime($request->call_start_time)),
    //             'Description' => $request->details,
    //             'Who_Id' => array('id' => $crmid),
    //             'se_module' => "Contacts"
    //         );

    //     }else if( $request->call_detail == "ScheduleCall" ){

    //         $call_data[0] = array(
    //             'Subject' =>$request->call_subject, 
    //             'Call_Purpose' => $request->call_purpose,
    //             'Contact_Name' => "Contact",
    //             'Call_Type' => $request->call_type,
    //             'Call_Details' => $request->call_detail,
    //             'Call_Start_Time' => date( 'c', strtotime($request->call_start_time) ),
    //             'Description' => $request->details,
    //             'Who_Id' => array('id' => $crmid),
    //             'se_module' => "Contacts"
    //         );

    //     }else{
    //         $call_data[0] = array(
    //             'Subject' =>$request->call_subject, 
    //             'Call_Purpose' => $request->call_purpose,
    //             'Contact_Name' => "Contact",
    //             'Call_Type' => $request->call_type,
    //             'Call_Details' => $request->call_detail,
    //             'Description' => $request->details,
    //             'Who_Id' => array('id' => $crmid),
    //             'se_module' => "Contacts"
    //         );
    //     }
             
    //     if (isset($contact_info->module_id) && ($contact_info->Account_Name_ID != "")){
    //         $call_data[0]['What_Id'] = array('id' => $contact_info->Account_Name_ID);
    //         $call_data[0]['se_module'] = 'Accounts';
    //     }


    //     $zoho = New Nzoho;
        
    //     if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
    //     if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->insertRecords($call_data, 'Calls');

    //     if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
    //         if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
    //             $record_id = $insertZoho->data[0]->details->id;
    //             $Common = new Common();
    //             $Common->sync_getRecordsById($record_id, "Calls");

    //             return redirect()->back()->with(array("success"=>"Information added successfully !"));
    //         }else{
    //             $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';
    //             return redirect()->back()->with(array("error"=>$error));
    //         }
    //     }else{
    //         return redirect()->back()->with(array("success"=>"Information added successfully !"));
    //     }
         
    // }


    // public function submit_task(Request $request)
    // {
    //     $crmid = $request->module_id;
    //     $contact_info  = Contacts::where('module_id', $crmid)->first();

    //     $task_data[0] = array(
    //         'Subject' =>$request->task_subject, 
    //         'Due_Date' => date('Y-m-d',strtotime($request->task_due_date)),
    //         'Status' => $request->task_status,
    //         'Priority' => $request->task_priority,
    //         'Who_Id' => array('id' => $crmid),
    //         'Description' => $request->details,
    //         'se_module' => "Contacts",
    //     );
        
    //     if (isset($contact_info->module_id) && ($contact_info->Account_Name_ID != "")){
    //         $task_data[0]['What_Id'] = array('id' => $contact_info->Account_Name_ID);
    //         $task_data[0]['se_module'] = 'Accounts';
    //     }

    //     $zoho = New Nzoho;
        
    //     if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
    //     if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->insertRecords($task_data, 'Tasks');

    //     if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
    //         if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
    //             $record_id = $insertZoho->data[0]->details->id;
    //             $Common = new Common();
    //             $Common->sync_getRecordsById($record_id, "Tasks");

    //             return redirect()->back()->with(array("success"=>"Information added successfully !"));
    //         }else{
    //             $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';
    //             return redirect()->back()->with(array("error"=>$error));
    //         }
    //     }else{
    //         return redirect()->back()->with(array("success"=>"Information added successfully !"));
    //     }

    // }



    /*
    |--------------------------------------------------------------------------
    | Tasks Functions
    |--------------------------------------------------------------------------
    */
    
    public function tasks(){
        $offset = ( $_GET['page'] - 1 ) * $_GET['per_page'];
        $sort_field = (isset($_GET['sort_field'])) ? $_GET['sort_field'] : "id";
        $sort_order = (isset($_GET['sort_order'])) ? $_GET['sort_order'] : "desc";

        try{

            $module_id = $_GET['module_id'];

            if(Schema::hasTable('zc_contacts'))$zc_contacts = DB::table('zc_contacts')->where('module_id',$module_id)->first();
            $Account_Name_ID = $zc_contacts->Account_Name_ID;

            $taskAssignTo = DB::table('zc_contacts')->where('Account_Name_ID',$Account_Name_ID)->get();


            $contact_id = Auth::user()->contact_id;
            $contact_info  = Contacts::where('module_id', $contact_id)->first();
            
            $contacts = DB::table('zc_contacts')->where('Account_Name_ID',$contact_info->Account_Name_ID)->get();

            $sql = DB::table('zc_tasks')->where('What_Id_ID',$contact_info->Account_Name_ID);
                //search conditions
                if(isset($_GET['search']) && ($_GET['search'] != "")){
                    $q = $_GET['search'];
                    $sql->where(function($query) use ($q) {
                        $query->where('Subject', 'LIKE', '%'.$q.'%')
                            ->orWhere('Priority', 'LIKE', '%'.$q.'%')
                            ->orWhere('Status', 'LIKE', '%'.$q.'%')
                            ->orWhere('Due_Date', 'LIKE', '%'.$q.'%')
                            ->orWhere('Who_Id', 'LIKE', '%'.$q.'%');
                    });
                }
            $tasks = $sql->offset($offset)->limit($_GET['per_page'])->orderBy($sort_field, $sort_order)->get();
            //count of total tasks
            $csql = DB::table('zc_tasks')->where('What_Id_ID',$contact_info->Account_Name_ID);
                //search conditions for count
                if(isset($_GET['search']) && ($_GET['search'] != "")){
                    $q = $_GET['search'];
                    $csql->where(function($query) use ($q) {
                        $query->where('Subject', 'LIKE', '%'.$q.'%')
                            ->orWhere('Priority', 'LIKE', '%'.$q.'%')
                            ->orWhere('Status', 'LIKE', '%'.$q.'%')
                            ->orWhere('Due_Date', 'LIKE', '%'.$q.'%')
                            ->orWhere('Who_Id', 'LIKE', '%'.$q.'%');
                    });
                }
            $task_count = $csql->count();
            //response
            return response([
                'message' => "",
                'contact_info' => $contact_info,
                'contacts' => $contacts,
                'tasks' => $tasks,
                'totalRows' => $task_count,
                'taskAssignTo' => $taskAssignTo,
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
        
    }

    
    public function task_details($task_id)
    {
        try{
            $tasks = [];
            $zc_contacts = [];
            if(Schema::hasTable('tasks'))$tasks = DB::table('tasks')->where('id',$task_id)->first();
            if(Schema::hasTable('zc_contacts'))$zc_contacts = DB::table('zc_contacts')->where('module_id',$tasks->contact_id)->first();
            return response([
                'message' => "",
                'contacts' => $zc_contacts,
                'pgData' => $tasks,
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }

    }

    public function task_store(Request $request)
    {
        try{

            $crmid = $request->assigned_to;
            $contact_info  = Contacts::where('module_id', $crmid)->first();

            $task_data[0] = array(
                'Subject' =>$request->task_subject, 
                'Due_Date' => date('Y-m-d',strtotime($request->task_due_date)),
                'Status' => $request->task_status,
                'Priority' => $request->task_priority,
                'Who_Id' => array('id' => $crmid),
                'Description' => $request->details,
                'se_module' => "Contacts",
            );
            
            if (isset($contact_info->module_id) && ($contact_info->Account_Name_ID != "")){
                $task_data[0]['What_Id'] = array('id' => $contact_info->Account_Name_ID);
                $task_data[0]['se_module'] = 'Accounts';
            }

            $zoho = New Nzoho;
            
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->insertRecords($task_data, 'Tasks');

            if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
                if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
                    $record_id = $insertZoho->data[0]->details->id;
                    $Common = new Common();
                    $Common->sync_getRecordsById($record_id, "Tasks");

                    return response([
                        'message' => "Information added successfully !",
                    ], 200);


                }else{
                    $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';

                    return response([
                        'message' => $error,
                    ], 200);

                }
            }else{

                return response([
                    'message' => "Information added successfully !",
                ], 200);
            }

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }

    }

    public function taskAssignTo()
    {
        try{

            $module_id = $_GET['module_id'];

            if(Schema::hasTable('zc_contacts'))$zc_contacts = DB::table('zc_contacts')->where('module_id',$module_id)->first();
            $Account_Name_ID = $zc_contacts->Account_Name_ID;

            $pgData = DB::table('zc_contacts')->where('Account_Name_ID',$Account_Name_ID)->get();

            return response([
                'message' => "",
                'pgData' => $pgData
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }

    }

    public function editTask()
    {
        try{

            $id = $_GET['id'];

            if(Schema::hasTable('tasks'))$tasks = DB::table('tasks')->where('id',$id)->first();

            $module_id = $tasks->contact_id;
            if(Schema::hasTable('zc_contacts'))$zc_contacts = DB::table('zc_contacts')->where('module_id',$module_id)->first();
            $Account_Name_ID = $zc_contacts->Account_Name_ID;
            $assigned_to = DB::table('zc_contacts')->where('Account_Name_ID',$Account_Name_ID)->get();

            return response([
                'message' => "Successfull",
                'tasks' => $tasks,
                'assigned_to' => $assigned_to,
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }

    }

    public function updateTask(Request $request)
    {

        try{
            $crmid = $request->assigned_to;
            $contact_info  = Contacts::where('module_id', $crmid)->first();

            $task_id = $request->module_id;

            $task_data[0] = array(
                'id' =>$request->module_id, 
                'Subject' =>$request->title, 
                'Due_Date' => date('Y-m-d',strtotime($request->task_due_date)),
                'Status' => $request->task_status,
                'Priority' => $request->task_priority,
                'Who_Id' => array('id' => $crmid),
                'Description' => $request->details,
                // 'se_module' => "Contacts",
            );
            
            if (isset($contact_info->module_id) && ($contact_info->Account_Name_ID != "")){
                $task_data[0]['What_Id'] = array('id' => $contact_info->Account_Name_ID);
                $task_data[0]['se_module'] = 'Accounts';
            }

            $zoho = New Nzoho;
            
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$insertZoho = $zoho->updateRecordsPO($task_data, 'Tasks', "false");


            if(isset($setting->want_crm) && ($setting->want_crm == "Yes")){
                if(isset($insertZoho->data[0]->code) && $insertZoho->data[0]->code == 'SUCCESS'){
                    $record_id = $insertZoho->data[0]->details->id;
                    $Common = new Common();
                    $Common->sync_getRecordsById($record_id, "Tasks");

                    return response([
                        'message' => "Information Updated successfully !",
                        'data' => $request->all()
                    ], 200);


                }else{
                    $error = (isset($insertZoho->data[0]->message)) ? $insertZoho->data[0]->message : 'Something wrong! Please try again.';
                    return response([
                        'message' => $error,
                    ], 400);
                }
            }else{

                return response([
                    'message' => "Information Updated successfully !",
                    'data' => $request->all()
                ], 200);
            }

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }

    }

    public function delTask()
    {
        try{

            $id = $_GET['id'];
            
            $zoho = New Nzoho;
            if(Schema::hasTable('setting')) $setting = DB::table('setting')->first();
            if(isset($setting->want_crm) && ($setting->want_crm == "Yes"))$zoho->deleteRecords($id, 'Tasks');

            DB::table('zc_tasks')->where('module_id',$id)->delete();
            //code here
            return response([
                'message' => "Successfully Deleted."
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }

    }

    // public function submit_task_new(Request $request)
    // {
    //     $data = $request->except(['_token']);
    //     DB::table('tasks')->insert($data);
    //     return redirect()->back()->with(array("success"=>"Information added successfully !"));

    // }



    // public function task_edit($task_id='')
    // {
    //     $data = array();
    //     $pg = array();
    //     $data['active_menu'] = 'tasks';

    //     $contact_id = Auth::user()->contact_id;
    //     $pg['contact_info'] = $contact_info = Contacts::where('module_id', $contact_id)->first();
        
        
    //     if(Schema::hasTable('tasks'))$pg['tasks'] = DB::table('tasks')->where('id',$task_id)->first();
    //     if (isset($contact_info->module_id) && ($contact_info->Account_Name_ID != "")){
    //         $pg['contacts'] = DB::table('zc_contacts')->where('Account_Name_ID',$contact_info->Account_Name_ID)->get();
    //     }
    //     $common = new Common();
    //     $data['template_api_name'] = $template_api_name = $common->getActiveTheme();

    //     return view('template/'.$template_api_name.'/tasks/task_edit',['data' => $data,'pg'=>$pg]);
    // }

    // function task_update(Request $request, $task_id) {
    //     $tasks = DB::table('tasks')->where('id',$task_id)->first();
    //     if (isset($tasks->id)) {
    //         $data = $request->except(['_token']);
    //         DB::table('tasks')->where('id',$task_id)->update($data);
    //         return redirect('/tasks')->with(array("success"=>"Information updated successfully !"));
    //     }else {
    //         return redirect()->back()->with(array("error"=>'Something wrong! Please try again.'));
    //     }
        
    // }


    // public function task_delete($task_id='')
    // {
    //     $check = DB::table('tasks')->where('id',$task_id)->delete();
    //     return redirect()->back()->with(array("success"=>"Information deleted successfully !"));
    // }

    
    /*
    |--------------------------------------------------------------------------
    | Common Functions
    |--------------------------------------------------------------------------
    */
    public function fetchOne()
    {
        $table = $_GET['tbl'];
        $colName = $_GET['colName'];
        $ColValue = $_GET['ColValue'];

        $data = DB::table($table)->where($colName,$ColValue)->first();

        return response([
            'data' => $data,
        ], 200);
    }


    public function getFetchList(){
        $offset = ( $_GET['page'] - 1 ) * $_GET['per_page'];

        $table = $_GET['table'];

        $colName = $_GET['colName'];
        $ColValue = $_GET['ColValue'];

        $sort_field = (isset($_GET['sort_field'])) ? $_GET['sort_field'] : "id";
        $sort_order = (isset($_GET['sort_order'])) ? $_GET['sort_order'] : "desc";

        try{

            $contact_info = DB::table('zc_contacts')->where("module_id",$ColValue)->first();
            $sql = DB::table($table)->where($colName,$ColValue);
                //search conditions
                if(isset($_GET['search']) && ($_GET['search'] != "")){
                    $q = $_GET['search'];
                    if ($table == "zc_calls" ) {
                        $sql->where(function($query) use ($q) {
                            $query->where('Subject', 'LIKE', '%'.$q.'%')
                                ->orWhere('Call_Type', 'LIKE', '%'.$q.'%')
                                ->orWhere('Call_Purpose', 'LIKE', '%'.$q.'%')
                                ->orWhere('Call_Duration', 'LIKE', '%'.$q.'%')
                                ->orWhere('Description', 'LIKE', '%'.$q.'%');
                        });
                    }

                    if ($table == "zc_events" ) {
                        $sql->where(function($query) use ($q) {
                            $query->where('Event_Title', 'LIKE', '%'.$q.'%')
                                   ->orWhere('Start_DateTime', 'LIKE', '%'.$q.'%')
                                   ->orWhere('End_DateTime', 'LIKE', '%'.$q.'%');
                        });
                    }

                    if ($table == "zc_tasks" ) {
                        $sql->where(function($query) use ($q) {
                            $query->where('Subject', 'LIKE', '%'.$q.'%')
                                ->orWhere('Priority', 'LIKE', '%'.$q.'%')
                                ->orWhere('Status', 'LIKE', '%'.$q.'%')
                                ->orWhere('Due_Date', 'LIKE', '%'.$q.'%')
                                ->orWhere('Description', 'LIKE', '%'.$q.'%');
                        });
                    }
                }
            $tasks = $sql->offset($offset)->limit($_GET['per_page'])->orderBy($sort_field, $sort_order)->get();
            //count of total tasks
            $csql = DB::table($table)->where($colName,$ColValue);
                //search conditions for count
                if(isset($_GET['search']) && ($_GET['search'] != "")){
                    $q = $_GET['search'];

                    if ($table == "zc_calls" ) {
                        $csql->where(function($query) use ($q) {
                            $query->where('Subject', 'LIKE', '%'.$q.'%')
                                ->orWhere('Call_Type', 'LIKE', '%'.$q.'%')
                                ->orWhere('Call_Purpose', 'LIKE', '%'.$q.'%')
                                ->orWhere('Call_Duration', 'LIKE', '%'.$q.'%')
                                ->orWhere('Description', 'LIKE', '%'.$q.'%')
                                ->orWhere('Call_Start_Time', 'LIKE', '%'.$q.'%');
                        });
                    }

                    if ($table == "zc_events" ) {
                        $csql->where(function($query) use ($q) {
                            $query->where('Event_Title', 'LIKE', '%'.$q.'%')
                                  ->orWhere('Start_DateTime', 'LIKE', '%'.$q.'%')
                                  ->orWhere('End_DateTime', 'LIKE', '%'.$q.'%');
                        });
                    }

                    if ($table == "zc_tasks" ) {
                        $csql->where(function($query) use ($q) {
                            $query->where('Subject', 'LIKE', '%'.$q.'%')
                                ->orWhere('Priority', 'LIKE', '%'.$q.'%')
                                ->orWhere('Status', 'LIKE', '%'.$q.'%')
                                ->orWhere('Due_Date', 'LIKE', '%'.$q.'%')
                                ->orWhere('Description', 'LIKE', '%'.$q.'%');
                        });
                    }
                    
                }
            $task_count = $csql->count();

            // get notes attachments
            $attachments = [];
            if($table == "zc_notes" && count($tasks) > 0){
                foreach($tasks as $rkey => $rval){
                    $path = public_path('uploadDir/notes/'.$ColValue.'/'.$rval->module_id);
                    if (file_exists($path)) {
                        $scan = scandir($path);
                        foreach($scan as $key=>$file)
                        {
                            if($key == 0 || $key == 1) continue;
                            else $attachments[$rval->module_id][] = $file;
                        }
                    }
                }
            }


            //response
            return response([
                'message' => "",
                'tasks' => $tasks,
                'contact_info' => $contact_info,
                'totalRows' => $task_count,
                'attachments' => $attachments,
            ], 200);

        }catch(Exception $e){
            return response([
                'message' => $e->getMessage()
            ], 400);
        }
        
    }

    

}