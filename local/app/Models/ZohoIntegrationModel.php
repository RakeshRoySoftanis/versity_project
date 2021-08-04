<?php
/**
*** @12/09/2019 by Softanis
*** @arifin@catalystconnect.com
*** @tutorial: Zoho Integration class
**/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DOMDocument;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ZohoIntegrationModel{


	/*
	|--------------------------------------------------------------------------
	|  ZOHO-CRM Functions
	|--------------------------------------------------------------------------
	*/

    /**
     * get crm module list and process
     *
     * @return void
     */
	public function processCrmModule()
	{
		$zoho = New Nzoho;
		$table_name = "module_list";
		$moduleList = $zoho->getModule();

		$this->createModulListTable($moduleList, $table_name);
		$this->syncModuleList($moduleList, $table_name);

		return count($moduleList).' Module Found';
	}

    /**
     * Create crm modules table
     *
     * @param [type] $ZohoData
     * @param [type] $table_name
     * @return void
     */
    public function createModulListTable($ZohoData, $table_name){

		$fields = array();
        if (!Schema::hasTable($table_name)){
            $ignore = array('created_at');

            foreach($ZohoData[0] as $key2 => $val2){
                if(!in_array($key2, $ignore)){
                    if ($key2 == 'id') {
                         $fields[] = array('name' => 'module_id', 'type' => 'text');
                    }
                    elseif(is_array($val2)){
                        $fields[] = array('name' => $key2, 'type' => 'mediumtext');
                    }else {
                    	$fields[] = array('name' => $key2, 'type' => 'text');
                    }
                }
            }
            $fields[] = array('name' => "create_time", 'type' => 'datetime');

            $response = $this->createTable($table_name, $fields);
        }else{

            $fieldList =  $this->getTableColumns($table_name);
            foreach($ZohoData[0] as $key => $val2){
                if (!in_array($key, $fieldList)){

                    if(is_array($val2)){
                        $fields[] = array('name' => $key, 'type' => 'mediumtext');
                    }else {
                        $fields[] = array('name' => $key, 'type' => 'text');
                    }
                } //if end

            } //foreach end

            $this->addColumnTotable($table_name, $fields);
        }

	}

    /**
     * sync module data
     *
     * @param [type] $moduleList
     * @param [type] $table_name
     * @return void
     */
	public function syncModuleList($moduleList, $table_name){		
		if ($moduleList) {
			DB::table($table_name)->delete();
            
            $fields =  $this->getTableColumns($table_name);
			foreach ($moduleList as $key => $m_data) { 
			    $insertArr = array();
			    foreach ($m_data as $field => $value) {
			    	if($field =='id')$insertArr['module_id'] = $value;
				    else if(in_array($field, $fields)){
						if(is_array($value))$value = json_encode($value);
						$insertArr[$field] = $value;
				    } 
			    }
			    $insertArr['create_time'] = date("Y-m-d H:i:s");
				DB::table($table_name)->insert($insertArr); 
			}
		}
	}


    function getFields($module){
		
	    $zoho = New Nzoho;
		$ZohoData = false;
		$n_module = $this->getModuleApiName($module);
		$ZohoData = $zoho->getFields($n_module);	
		if($ZohoData){
			$this->createFieldTable($n_module);
			$this->createCrmTable($ZohoData,$n_module);
			$this->syncFields($ZohoData,$n_module);
		}
	}
    

    public function createCrmTable($ZohoData,$module)
	{
		$table_name = "zc_".strtolower($module);
		$fieldList =  $this->getTableColumns($table_name);
		// set your dynamic fields (you can fetch this data from database this is just an example)
		$fields = array();

        if (!Schema::hasTable($table_name)) {
        	$fields[] = array('name' => 'module_id', 'type' => 'text');
			foreach($ZohoData as $key2 => $val2){
				if($val2['api_name'] == 'id') {}//$fields[] = array('name' => $val2['module_id'], 'type' => 'text');
				else if($val2['type'] =='lookup' || $val2['type'] =='ownerlookup'){
					$fields[] = array('name' => $val2['api_name'], 'type' => 'text');
					$fields[] = array('name' => $val2['api_name'].'_ID', 'type' => 'text');
				}
				else if($val2['type'] == 'date') $fields[] = array('name' => $val2['api_name'], 'type' => 'date');
				else if($val2['type'] == 'datetime') $fields[] = array('name' => $val2['api_name'], 'type' => 'text');
				else if ($val2['type'] == 'currency') $fields[] = array('name' => $val2['api_name'], 'type' => 'text');
				else if ($val2['type'] == 'double') $fields[] = array('name' => $val2['api_name'], 'type' => 'text');
				else $fields[] = array('name' => $val2['api_name'], 'type' => 'text');
			}

	        $response = $this->createTable($table_name, $fields);
	        echo "</br>Table Create";
	        echo "</br>Column";echo "<pre>";var_dump($fields);echo "</pre>";
        }else{
			foreach($ZohoData as $key => $val2){
	        	if (!in_array($val2['api_name'], $fieldList)){
					if($val2['type'] =='lookup' || $val2['type'] =='ownerlookup'){
						$fields[] = array('name' => $val2['api_name'], 'type' => 'text');
						$fields[] = array('name' => $val2['api_name'].'_ID', 'type' => 'text');
					}
					else if($val2['type'] == 'date') $fields[] = array('name' => $val2['api_name'], 'type' => 'date');
					else if($val2['type'] == 'datetime') $fields[] = array('name' => $val2['api_name'], 'type' => 'text');
					else if ($val2['type'] == 'currency') $fields[] = array('name' => $val2['api_name'], 'type' => 'text');
					else if ($val2['type'] == 'double') $fields[] = array('name' => $val2['api_name'], 'type' => 'text');
					else $fields[] = array('name' => $val2['api_name'], 'type' => 'text');
	        	}
	    	}

	        $this->addColumnTotable($table_name, $fields);
	        echo "</br>Table Exist";
	        echo "</br>New Column";echo "<pre>";var_dump($fields);echo "</pre>";

	        return response()->json(['message' => 'Given table is already existis.'], 400);
	    }
	}

    function createFieldTable($module = "Contacts"){
		$zoho = New Nzoho;
	    $fieldLst = $zoho->getFieldsNew($module);
	    $table_name = 'zohofields';
	    $fields = array();
	    if (!Schema::hasTable($table_name)) {
	    	$fields[] = array('name' => 'module', 'type' => 'text');
	    	foreach ($fieldLst[0] as $key => $value) {
	    		if($key == 'id') { $fields[] = array('name' => 'field_id', 'type' => 'text'); }
	    		else{
	    			$fields[] = array('name' => $key, 'type' => 'text');
	    		}
	    	}

	    	$response = $this->createTable($table_name, $fields);
	        echo "</br>Table Create";
	        echo "</br>Column";echo "<pre>";var_dump($fields);echo "</pre>";
	    }

	}

    function syncFields($fieldLst, $module){
		$zoho = New Nzoho;
		$fieldLst = $zoho->getFieldsNew($module);

		$fields = $this->getTableColumns('zohofields');

		DB::table('zohofields')->where('module',$module)->delete();

		foreach($fieldLst as $fvalue){

			unset($fieldArray);
			unset($fieldArrayValue);
			$fieldArray = array();
			$fieldArray['module'] = $module; 
			foreach ($fvalue as $field => $value) {            

				if($field =='id')$fieldArray['field_id'] = $value;
				else if(in_array($field, $fields)){
					if(is_array($value) || is_object($value))$value = json_encode($value);
					$fieldArray[$field] = $value;
				}
			}  

	        // echo "</br>fieldArray ";echo "<pre>";var_dump($fieldArray);echo "</pre>";

			DB::table('zohofields')->insert($fieldArray); 
		}
	}

    function getModuleApiName($module){
		$mdData = DB::table('module_list')->where('module_name', $module)->first();
		return isset($mdData->api_name) ? $mdData->api_name : false;
	}

	/*
	|--------------------------------------------------------------------------
	|  ZOHO-Books Functions
	|--------------------------------------------------------------------------
	*/
	
    function processSyncZBData($module, $tableData){
        $table_name = "zb_".$module;
        $ZohoBooks = New ZohoBooks;

        if($module == 'contacts'){
            foreach ($tableData as $key => $value) {
                $id = $value->contact_id;
                $singleData = $ZohoBooks->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZohoBookRecord($module,$id, $singleDataArr->contact);
            }
        }

        if($module == 'items'){
            foreach ($tableData as $key => $value) {
                $id = $value->item_id;
                $singleData = $ZohoBooks->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZohoBookRecord($module,$id, $singleDataArr->item);
            }
        }

        if($module == 'estimates'){
            foreach ($tableData as $key => $value) {
                $id = $value->estimate_id;
                $singleData = $ZohoBooks->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZohoBookRecord($module,$id, $singleDataArr->estimate);
            }
        }

        if($module == 'salesorders'){
            foreach ($tableData as $key => $value) {
                $id = $value->salesorder_id;
                $singleData = $ZohoBooks->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZohoBookRecord($module,$id, $singleDataArr->salesorder);
            }
        }


        if($module == 'invoices'){
            foreach ($tableData as $key => $value) {
                $id = $value->invoice_id;
                $singleData = $ZohoBooks->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZohoBookRecord($module,$id, $singleDataArr->invoice);
            }
        }

        if($module == 'recurringinvoices'){
            foreach ($tableData as $key => $value) {
                $id = $value->recurring_invoice_id;
                $singleData = $ZohoBooks->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZohoBookRecord($module,$id, $singleDataArr->recurring_invoice);
            }
        }

        if($module == 'purchaseorders'){
            foreach ($tableData as $key => $value) {
                $id = $value->purchaseorder_id;
                $singleData = $ZohoBooks->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZohoBookRecord($module,$id, $singleDataArr->purchaseorder);
            }
        }

    }


    public function syncZohoBookRecord($module, $id, $data)
    {
        $table_name = "zb_".$module;
        $fieldList =  $this->getTableColumns($table_name);
        $fields = array();
        $insertArr = array();
        foreach ($data as $key => $val) {

            if(is_array($val) || is_object($val))$val = json_encode($val);
            
            if (in_array($key, $fieldList)) {
                
                $insertArr[$key] = $val;

            }else {
                $fields[] = array('name' => $key, 'type' => 'text');
                $insertArr[$key] = $val;                
            }   

        }

        if (!empty($fields)) {
            $this->addColumnTotable($table_name, $fields);
        } 

        if($module == 'contacts')DB::table($table_name)->where('contact_id', $id)->delete();
        if($module == 'items')DB::table($table_name)->where('item_id', $id)->delete();
        if($module == 'estimates')DB::table($table_name)->where('estimate_id', $id)->delete();
        if($module == 'salesorders')DB::table($table_name)->where('salesorder_id', $id)->delete();
        if($module == 'invoices')DB::table($table_name)->where('invoice_id', $id)->delete();
        if($module == 'recurringinvoices')DB::table($table_name)->where('recurring_invoice_id', $id)->delete();
        if($module == 'purchaseorders')DB::table($table_name)->where('purchaseorder_id', $id)->delete();
        
        $insert_res = "";
        if ($insertArr) {
            $insert_res = DB::table($table_name)->insert($insertArr);
        }
        if ($insert_res) {
            return true;
        }else{
            return false;
        }
    }




    /*
    |--------------------------------------------------------------------------
    |  ZOHO-Subscriptions Functions
    |--------------------------------------------------------------------------
    */
    function process_zs_auth_token($table_name, $request){
        if (Schema::hasTable($table_name)){
            $insertArr = array();
            $insertArr['organization_id'] = $request->organization_id;
            $insertArr['create_time'] = date('Y-m-d H:i:s');
            $insertArr['access_token'] = $request->zs_access_token;

            DB::table($table_name)->insert($insertArr);
            $msg = "Table created and Saved Successfully!";
        }
    }

    function processSyncZSData($module, $tableData){
        $ZohoSubscription = New ZohoSubscription;

        if($module == 'products'){
            foreach ($tableData as $key => $value) {
                $id = $value->product_id;
                $singleData = $ZohoSubscription->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZSRecord($module,$id, $singleDataArr->product);
            }
        }

        if($module == 'plans'){
            foreach ($tableData as $key => $value) {
                $id = $value->plan_code;
                $singleData = $ZohoSubscription->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZSRecord($module,$id, $singleDataArr->plan);
            }
        }

        if($module == 'coupons'){
            foreach ($tableData as $key => $value) {
                $id = $value->coupon_code;
                $singleData = $ZohoSubscription->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZSRecord($module,$id, $singleDataArr->coupon);
            }
        }

        if($module == 'addons'){
            foreach ($tableData as $key => $value) {
                $id = $value->addon_code;
                $singleData = $ZohoSubscription->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZSRecord($module,$id, $singleDataArr->addon);
            }
        }

        if($module == 'customers'){
            foreach ($tableData as $key => $value) {
                $id = $value->customer_id;
                $singleData = $ZohoSubscription->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZSRecord($module,$id, $singleDataArr->customer);
            }
        }

        if($module == 'subscriptions'){
            foreach ($tableData as $key => $value) {
                $id = $value->subscription_id;
                $singleData = $ZohoSubscription->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZSRecord($module,$id, $singleDataArr->subscription);
            }
        }

        if($module == 'invoices'){
            foreach ($tableData as $key => $value) {
                $id = $value->invoice_id;
                $singleData = $ZohoSubscription->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZSRecord($module,$id, $singleDataArr->invoice);
            }
        }



    }

    public function syncZSRecord($module, $id, $data)
    {
        $table_name = "zs_".$module;
        $fieldList =  $this->getTableColumns($table_name);
        $fields = array();
        $insertArr = array();
        foreach ($data as $key => $val) {

            if(is_array($val) || is_object($val))$val = json_encode($val);
            
            if (in_array($key, $fieldList)) {
                
                $insertArr[$key] = $val;

            }else {
                $fields[] = array('name' => $key, 'type' => 'text');
                $insertArr[$key] = $val;                
            }   

        }

        if (!empty($fields)) {
            $this->addColumnTotable($table_name, $fields);
        } 

        if($module == 'products')DB::table($table_name)->where('product_id', $id)->delete();
        if($module == 'plans')DB::table($table_name)->where('plan_code', $id)->delete();
        if($module == 'coupons')DB::table($table_name)->where('coupon_code', $id)->delete();
        if($module == 'addons')DB::table($table_name)->where('addon_code', $id)->delete();
        if($module == 'customers')DB::table($table_name)->where('customer_id', $id)->delete();
        if($module == 'subscriptions')DB::table($table_name)->where('subscription_id', $id)->delete();
        if($module == 'invoices')DB::table($table_name)->where('invoice_id', $id)->delete();
        
        $insert_res = "";
        if ($insertArr) {
            $insert_res = DB::table($table_name)->insert($insertArr);
        }
        if ($insert_res) {
            return true;
        }else{
            return false;
        }
    }

    

    /*
	|--------------------------------------------------------------------------
	|  ZOHO-Projects Functions
	|--------------------------------------------------------------------------
	*/

	function processSyncZPData($module, $tableData){
        $table_name = "zp_".$module;
        $ZohoProjects = New ZohoProjects;
        $getinfo =  $this->syncZohoProjectsRecord($module,$tableData->id_string, $tableData);
       
        return $getinfo;
    }


    public function syncZohoProjectsRecord($module, $id, $data)
    {
        $table_name = "zp_".$module;
        $fieldList =  $this->getTableColumns($table_name);
        $fields = array();
        $insertArr = array();
        //echo "<pre>";print_r($data);exit();
        $ignoreArr = array('id');

        foreach ($data as $key => $val) {
            if(!in_array($key, $ignoreArr)){
                if(is_array($val) || is_object($val))$val = json_encode($val);
                
                if (in_array($key, $fieldList)) {
                    if ($key == 'id_string') {
                        $idkey = $module.'_id';
                        $insertArr[$idkey] = $val;
                        $insertArr[$key] = $val;
                    }
                    else {
                        $insertArr[$key] = $val;
                    }
                }else {
                    if ($key == 'id_string') {
                        $fields[] = array('name' => $module.'_id', 'type' => 'text');
                        $fields[] = array('name' => $key, 'type' => 'text');
                        $idkey = $module.'_id';
                        $insertArr[$idkey] = $val;
                        $insertArr[$key] = $val;   
                    }
                    else {
                        $fields[] = array('name' => $key, 'type' => 'text');
                        $insertArr[$key] = $val;   
                    }             
                }  
            } 

        }

        if (!empty($fields)) {
            $this->addColumnTotable($table_name, $fields);
        } 

        if($module == 'projects')DB::table($table_name)->where('projects_id', $id)->delete();
        if($module == 'portals')DB::table($table_name)->where('portals_id', $id)->delete();
        if($module == 'tasks')DB::table($table_name)->where('tasks_id', $id)->delete();
        if($module == 'events')DB::table($table_name)->where('events_id', $id)->delete();
        if($module == 'bugs')DB::table($table_name)->where('bugs_id', $id)->delete();
        if($module == 'milestones')DB::table($table_name)->where('milestones_id', $id)->delete();
        if($module == 'clients')DB::table($table_name)->where('clients_id', $id)->delete();
        
        $insert_res = "";
        if ($insertArr) {
            $insert_res = DB::table($table_name)->insert($insertArr);
        }
        if ($insert_res) {
            return true;
        }else{
            return false;
        }
    }



    /*
	|--------------------------------------------------------------------------
	|  ZOHO-Desk Functions
	|--------------------------------------------------------------------------
	*/
    function processSyncZDData($module, $tableData,$url=''){
        $table_name = "zd_".$module;
        $ZohoDesks = New ZohoDesk;

        if( $module == 'contacts' || $module == 'tickets' || $module == 'tasks'){
            $data = $tableData['data'];
            foreach ($data as $key => $value) {
                $id            = $value['id'];
                $singleData    = $ZohoDesks->getRecordById($id, $module, $tableData['organization_id']);
                // $singleData    = str_replace('"id"', '"'.$module.'_id"', $singleData);
                $singleDataArr = json_decode($singleData, true);
                $response = $this->syncZohoDeskRecord($module,$id, $singleDataArr);
            }
        }

        if( $module == 'tickettime' || $module == 'tasktime'){
            $data = $tableData['data'];
            foreach ($data as $key => $value) {
                $id = $value['id'];
                $modulet = 'timeEntry';
                $singleData    = $ZohoDesks->getRecordById($id, $modulet, $tableData['organization_id'],$url);
                // $singleData    = str_replace('"id"', '"'.$module.'_id"', $singleData);
                $singleDataArr = json_decode($singleData, true);
                $response = $this->syncZohoDeskRecord($module,$id, $singleDataArr);
            }
        }

        if( $module == 'threads' ){
            $data = $tableData['data'];
            foreach ($data as $key => $value) {
                $id = $value['id'];

                $singleData    = $ZohoDesks->getRecordById($id, $module, $tableData['organization_id'],$url);
                // $singleData    = str_replace('"id"', '"'.$module.'_id"', $singleData);
                $singleDataArr = json_decode($singleData, true);
                $singleDataArr['tickets_id'] = $tableData['tickets_id'];
                $response = $this->syncZohoDeskRecord($module,$id, $singleDataArr);
            }
        }

      
        return $response;

    }


    public function syncZohoDeskRecord($module, $id, $data)
    {
        //echo "<pre>";print_r($module);exit();
        $table_name = "zd_".$module;
        $fieldList  =  $this->getTableColumns($table_name);
        $fields     = array();
        $insertArr  = array();

        foreach ($data as $key => $val) {
            if($key == 'id'){
                $insertArr[$module.'_id'] = $val;
            }else{
                if(is_array($val) || is_object($val))$val = json_encode($val);
                
                if (in_array($key, $fieldList)) {
                    
                    $insertArr[$key] = $val;

                }else {
                    $fields[] = array('name' => $key, 'type' => 'text');
                    $insertArr[$key] = $val;                
                }
            }   

        }

        if (!empty($fields)) {
            $this->addColumnTotable($table_name, $fields);
        }

        if($module == 'contacts')DB::table($table_name)->where($module.'_id', $id)->delete();
        if($module == 'tickets')DB::table($table_name)->where($module.'_id', $id)->delete();
        if($module == 'tasks')DB::table($table_name)->where($module.'_id', $id)->delete();
        if($module == 'tasks')DB::table($table_name)->where($module.'_id', $id)->delete();
        if($module == 'threads')DB::table($table_name)->where($module.'_id', $id)->delete();
        if($module == 'tasktime')DB::table($table_name)->where('tasktime_id', $id)->delete();
        if($module == 'tickettime')DB::table($table_name)->where('tickettime_id', $id)->delete();
        
        $insert_res = "";
        if ($insertArr) {
            $insert_res = DB::table($table_name)->insert($insertArr);
        }
        if ($insert_res) {
            return true;
        }else{
            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    |  Zoho-Sign Functions
    |--------------------------------------------------------------------------
    */
    function processSyncZSgData($module, $tableData){
        $ZohoSign = New ZohoSign;

        if($module == 'users'){
            foreach ($tableData as $key => $value) {
                $id = $value->user_id;
                $this->syncZSgRecord($module,$id, $value);
            }
        }

        if($module == 'requests'){
            foreach ($tableData as $key => $value) {
                $id = $value->request_id;
                $singleData = $ZohoSign->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZSgRecord($module,$id, $singleDataArr->requests);
            }
        }

        if($module == 'templates'){
            foreach ($tableData as $key => $value) {
                $id = $value->template_id;
                $singleData = $ZohoSign->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZSgRecord($module,$id, $singleDataArr->templates);
            }
        }

    }


    public function syncZSgRecord($module, $id, $data)
    {
        $table_name = "zsg_".$module;
        $fieldList =  $this->getTableColumns($table_name);
        $fields = array();
        $insertArr = array();
        foreach ($data as $key => $val) {

            if(is_array($val) || is_object($val))$val = json_encode($val);
            
            if (in_array($key, $fieldList)) {
                
                $insertArr[$key] = $val;

            }else {
                $fields[] = array('name' => $key, 'type' => 'text');
                $insertArr[$key] = $val;                
            }   

        }

        if (!empty($fields)) {
            $this->addColumnTotable($table_name, $fields);
        } 

        if($module == 'users')DB::table($table_name)->where('user_id', $id)->delete();
        if($module == 'requests')DB::table($table_name)->where('request_id', $id)->delete();
        if($module == 'templates')DB::table($table_name)->where('template_id', $id)->delete();
        
        $insert_res = "";
        if ($insertArr) {
            $insert_res = DB::table($table_name)->insert($insertArr);
        }
        if ($insert_res) {
            return true;
        }else{
            return false;
        }
    }


    /*
    |--------------------------------------------------------------------------
    |  Zoho-Vaults Functions
    |--------------------------------------------------------------------------
    */
    function create_assign_vaults_table(){
        $table_name = "zv_assign_vaults";
        $fields = array();
        if (!Schema::hasTable($table_name)){

            $fields[] = array('name' => 'user_id', 'type' => 'text');
            $fields[] = array('name' => 'chambers', 'type' => 'mediumtext');
            $fields[] = array('name' => 'secrets', 'type' => 'mediumtext');
            
            $response = $this->createTable($table_name, $fields);
        }

    }

    function processSyncZVData($module, $tableData){
        $ZohoVault = New ZohoVault;

        if($module == 'user'){
            foreach ($tableData as $key => $value) {
                $id = $value->user_auto_id;
                $this->syncZVRecord($module,$id, $value);
            }
        }

        if($module == 'secrets'){
            foreach ($tableData as $key => $value) {
                $id = $value->secretid;
                $this->syncZVRecord($module,$id, $value);
            }
        }

        if($module == 'chambers'){
            foreach ($tableData as $key => $value) {
                $id = $value->chamberid;
                $singleData = $ZohoVault->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);

                $this->syncZVRecord($module,$id, $singleDataArr->operation->Details);
            }
        }


    }


    public function syncZVRecord($module, $id, $data)
    {
        $table_name = "zv_".$module;
        $fieldList =  $this->getTableColumns($table_name);
        $fields = array();
        $insertArr = array();
        foreach ($data as $key => $val) {

            if(is_array($val) || is_object($val))$val = json_encode($val);
            
            if (in_array($key, $fieldList)) {
                
                $insertArr[$key] = $val;

            }else {
                $fields[] = array('name' => $key, 'type' => 'text');
                $insertArr[$key] = $val;                
            }   

        }

        if (!empty($fields)) {
            $this->addColumnTotable($table_name, $fields);
        } 

        if($module == 'user')DB::table($table_name)->where('user_auto_id', $id)->delete();
        if($module == 'secrets')DB::table($table_name)->where('secretid', $id)->delete();
        if($module == 'chambers')DB::table($table_name)->where('chamber_auto_id', $id)->delete();
        if(($module == 'get_login') || ($module == 'open_vault'))DB::table($table_name)->truncate();
        
        $insert_res = "";
        if ($insertArr) {
            $insert_res = DB::table($table_name)->insert($insertArr);
        }
        if ($insert_res) {
            return true;
        }else{
            return false;
        }
    }


    /*
    |--------------------------------------------------------------------------
    |  ZOHO-Inventory Functions
    |--------------------------------------------------------------------------
    */
    
    function processSyncZIData($module, $tableData){
        $table_name = "zi_".$module;
        $ZohoInventory = New ZohoInventory;

        if($module == 'contacts'){
            foreach ($tableData as $key => $value) {
                $id = $value->contact_id;
                $singleData = $ZohoInventory->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);
                $this->syncZohoInventoryRecord($module,$id, $singleDataArr->contact);
            }
        }

        if($module == 'items'){
            foreach ($tableData as $key => $value) {
                $id = $value->item_id;
                $singleData = $ZohoInventory->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);
                $this->syncZohoInventoryRecord($module,$id, $singleDataArr->item);
            }
        }

        if($module == 'salesorders'){
            foreach ($tableData as $key => $value) {
                $id = $value->salesorder_id;
                $singleData = $ZohoInventory->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);
                $this->syncZohoInventoryRecord($module,$id, $singleDataArr->salesorder);
            }
        }

        if($module == 'packages'){
            foreach ($tableData as $key => $value) {
                $id = $value->package_id;
                $singleData = $ZohoInventory->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);
                $this->syncZohoInventoryRecord($module,$id, $singleDataArr->package);
            }
        }

        if($module == 'invoices'){
            foreach ($tableData as $key => $value) {
                $id = $value->invoice_id;
                $singleData = $ZohoInventory->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);
                $this->syncZohoInventoryRecord($module,$id, $singleDataArr->invoice);
            }
        }

        if($module == 'customerpayments'){
            foreach ($tableData as $key => $value) {
                $id = $value->payment_id;
                $singleData = $ZohoInventory->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);
                $this->syncZohoInventoryRecord($module,$id, $singleDataArr->payment);
            }
        }

        if($module == 'purchaseorders'){
            foreach ($tableData as $key => $value) {
                $id = $value->purchaseorder_id;
                $singleData = $ZohoInventory->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);
                $this->syncZohoInventoryRecord($module,$id, $singleDataArr->purchaseorder);
            }
        }

        if($module == 'bills'){
            foreach ($tableData as $key => $value) {
                $id = $value->bill_id;
                $singleData = $ZohoInventory->getRecordById($id, $module);
                $singleDataArr = json_decode($singleData);
                $this->syncZohoInventoryRecord($module,$id, $singleDataArr->bill);
            }
        }


        

    }


    public function syncZohoInventoryRecord($module, $id, $data)
    {
        $table_name = "zi_".$module;
        $fieldList =  $this->getTableColumns($table_name);
        $fields = array();
        $insertArr = array();
        foreach ($data as $key => $val) {

            if(is_array($val) || is_object($val))$val = json_encode($val);
            
            if (in_array($key, $fieldList)) {
                
                $insertArr[$key] = $val;

            }else {
                $fields[] = array('name' => $key, 'type' => 'text');
                $insertArr[$key] = $val;                
            }   

        }

        if (!empty($fields)) {
            $this->addColumnTotable($table_name, $fields);
        } 

        if($module == 'contacts')DB::table($table_name)->where('contact_id', $id)->delete();
        if($module == 'items')DB::table($table_name)->where('item_id', $id)->delete();
        if($module == 'salesorders')DB::table($table_name)->where('salesorder_id', $id)->delete();
        if($module == 'packages')DB::table($table_name)->where('package_id', $id)->delete();
        if($module == 'invoices')DB::table($table_name)->where('invoice_id', $id)->delete();
        if($module == 'customerpayments')DB::table($table_name)->where('payment_id', $id)->delete();
        if($module == 'purchaseorders')DB::table($table_name)->where('purchaseorder_id', $id)->delete();
        if($module == 'bills')DB::table($table_name)->where('bill_id', $id)->delete();
        
        $insert_res = "";
        if ($insertArr) {
            $insert_res = DB::table($table_name)->insert($insertArr);
        }
        if ($insert_res) {
            return true;
        }else{
            return false;
        }
    }






    /*
	|--------------------------------------------------------------------------
	|  Common Functions
	|--------------------------------------------------------------------------
	*/
	function createZohoModuleTable($module, $tableData, $table_name = ""){
        $fields = array();
        if (!Schema::hasTable($table_name)){
            $ignore = array('created_at');
            if($module == "threads")$fields[] = array('name' => "tickets_id", 'type' => 'text');
            foreach($tableData as $key2 => $val2){
                if(!in_array($key2, $ignore)){
                    if ($key2 == 'id') {
                         $fields[] = array('name' => $module.'_id', 'type' => 'text');
                    }
                    elseif(is_array($val2)){
                        $fields[] = array('name' => $key2, 'type' => 'mediumtext');
                    }else {
                    	$fields[] = array('name' => $key2, 'type' => 'text');
                    }
                }
            }

            $response = $this->createTable($table_name, $fields);
        }else{

            $fieldList =  $this->getTableColumns($table_name);
            foreach($tableData as $key => $val2){
                if (!in_array($key, $fieldList)){

                    if(is_array($val2)){
                        $fields[] = array('name' => $key, 'type' => 'mediumtext');
                    }else {
                        $fields[] = array('name' => $key, 'type' => 'text');
                    }
                } //if end

            } //foreach end

            $this->addColumnTotable($table_name, $fields);
        }

    }

	function createZohoAuthTable($table_name){
        
        $fields = array();
        if (!Schema::hasTable($table_name)){

            $fields[] = array('name' => 'organization_id', 'type' => 'text');
            $fields[] = array('name' => 'access_token', 'type' => 'text');
            $fields[] = array('name' => 'create_time', 'type' => 'datetime');
            $fields[] = array('name' => 'authorized_client_name', 'type' => 'text');
            $fields[] = array('name' => 'authorized_redirect_url', 'type' => 'text');
            $fields[] = array('name' => 'client_id', 'type' => 'text');
            $fields[] = array('name' => 'client_secret', 'type' => 'text');
            $fields[] = array('name' => 'code', 'type' => 'text');
            $fields[] = array('name' => 'refresh_token', 'type' => 'text');
           
            $response = $this->createTable($table_name, $fields);
        }

    }


    function process_auth_token($table_name, $request){
    	if (Schema::hasTable($table_name)){

            $insertArr = array();
            $insertArr['organization_id'] = $request->organization_id;
            $insertArr['authorized_client_name'] = $request->authorized_client_name;
            $insertArr['authorized_redirect_url'] = $request->authorized_redirect_url;
            $insertArr['client_id'] = $request->client_id;
            $insertArr['client_secret'] = $request->client_secret;
            $insertArr['code'] = $request->code;
            $insertArr['create_time'] = date('Y-m-d H:i:s');

            $token_data = $this->generate_authtoken($insertArr);
            $token_dataArr = json_decode($token_data);

            if(isset($token_dataArr->access_token)){

                $insertArr['access_token'] = $token_dataArr->access_token;
                $insertArr['refresh_token'] = $token_dataArr->refresh_token;

                DB::table($table_name)->insert($insertArr);

                return array('success' => 'Table created and API token generated.');
            }

            $err = isset($token_dataArr->error) ? 'Table created but '.$token_dataArr->error : 'Table created but something wrong for generate API Token.';

            return array('error'=> $err);
        }
    }




    function getTableColumns($table)
    {
        return  DB::getSchemaBuilder()->getColumnListing($table);
    }

    public function createTable($table_name, $fields = [])
    {
        // check if table is not already exists
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($fields, $table_name) {
                $table->increments('id');
                if (count($fields) > 0) {
                    foreach ($fields as $field) {
                        $table->{$field['type']}($field['name'])->nullable();
                    }
                }
                $table->timestamps();
            });

            DB::statement("ALTER TABLE ".$table_name." CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");

            return response()->json(['message' => 'Given table has been successfully created!'], 200);
        }

        return response()->json(['message' => 'Given table is already existis.'], 400);
    }

    public function addColumnTotable($table_name, $fields = [])
    {
        Schema::table($table_name, function (Blueprint $table) use ($fields, $table_name) {
            if (count($fields) > 0) {
                foreach ($fields as $field) {
                    if (!Schema::hasColumn($table_name, $field['name']))$table->{$field['type']}($field['name'])->nullable();
                }
            }
        });
    }


    public function generate_authtoken($data)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://accounts.zoho.com/oauth/v2/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"client_id\"\r\n\r\n".$data['client_id']."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"client_secret\"\r\n\r\n".$data['client_secret']."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"redirect_uri\"\r\n\r\n".$data['authorized_redirect_url']."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"code\"\r\n\r\n".$data['code']."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"grant_type\"\r\n\r\nauthorization_code\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
            CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return $err;
        } else {
          return $response;
        }
    }










}
?>