<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
/*
	Zoho API version 2

	$wfTrigger = 'workflow' //if you want to call webhook
*/

namespace App\Models;



use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;
use DOMDocument;
use Exception;
use Illuminate\Support\Facades\DB;

class Nzoho extends Model

{
	private $authtoken;
	private $newFormat;
	private $xml;
	private $error;
	private $code;
	private $msg;
	private $url = "https://www.zohoapis.com/crm/v2/";

	public function getError()
	{
		return $this->error;
	}

	public function flushError()
	{
		$this->error = '';
	}
	public function getCode()
	{
		return $this->code;
	}

	public function flushCode()
	{
		$this->code = '';
	}

	public function getMsg()
	{
		return $this->msg;
	}

	public function flushMsg()
	{
		$this->msg = '';
	}

	public function __construct($authtoken = '', $newFormat = 1)
	{
		@session_start();

		$auth_data = DB::table('zoho_auth')->orderBy('id', 'desc')->first();		

		// $this->authtoken = 'Zoho-oauthtoken 1000.cc974da174f3e6971f8efc3bfc993846.f0124abe09db92edcba29ec4229acf01';   
		$this->authtoken = 'Zoho-oauthtoken '.$auth_data->access_token;   


		$this->newFormat = $newFormat;

		$db_time_with_increase_time = date('Y-m-d H:i:s', strtotime("+59 minutes", strtotime($auth_data->create_time)));
		$dtA = new \DateTime($db_time_with_increase_time);
		$dtB = new \DateTime();
		
		if ( $dtA < $dtB ) {
			
			$insArr['refresh_token'] = $data['refresh_token'] = $auth_data->refresh_token;
			$insArr['client_id'] = $data['client_id'] = $auth_data->client_id;
			$insArr['client_secret'] = $data['client_secret'] =$auth_data->client_secret;
			$data['grant_type'] = 'refresh_token';

		  	$token_data = $this->refresh_authtoken($data);
			$token_dataArr = json_decode($token_data);

			if(isset($token_dataArr->access_token)) $this->authtoken = 'Zoho-oauthtoken '.$token_dataArr->access_token;

			$insArr['access_token'] = $token_dataArr->access_token;
			$insArr['authorized_client_name'] = $auth_data->authorized_client_name;
			$insArr['code'] = $auth_data->code;
			$insArr['organization_id'] = $auth_data->organization_id;
			$insArr['authorized_redirect_url'] = $auth_data->authorized_redirect_url;
			$insArr['create_time'] = new \DateTime();

			DB::table('zoho_auth')->insert($insArr); 
		}
		
	

	}


	function refresh_authtoken($data){


		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://accounts.zoho.com/oauth/v2/token",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"refresh_token\"\r\n\r\n".$data['refresh_token']."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"client_id\"\r\n\r\n".$data['client_id']."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"client_secret\"\r\n\r\n".$data['client_secret']."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"grant_type\"\r\n\r\n".$data['grant_type']."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
		  CURLOPT_HTTPHEADER => array(
		    "Cache-Control: no-cache",
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

	/*
	* Description: Get record details of any module by its zoho record id
	* Parameters:  $id= A valid Zoho_record_id 
	*              $module= The name of the Zoho Module for which record id is provided
	* Returns: An array(count=>NUMBER_OF_RECORDS, data=array(THE ACTUAL DATA WITH ALL FIELDS AND ITS VALUE AS KEY => VALUE PAIR))
	* Notes: It would be nice to check if getError() method returns any value after calling this function for error handling
	*/
	public function getRecordsById($id, $module)
	{
		if (!$id) return false;
		try
		{
			$records = array();
			$resultAr = array();
			$zoho_url = $this->url."$module/$id";
			$result = $this->get_from_zoho($zoho_url);
			
			if(isset($result->data)) $resultAr = $result->data;
			$output = json_encode($resultAr, true);
			$records = json_decode($output, true);
			return array(
				'count' => count($records) ,
				'data' => $records
			);
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/*
	*Description: Gets related records from zoho module.
	*Parameters:  $module= The name of the Zoho Module for which record list want
	*			  $id= A valid Zoho_record_id 
	*             $parentModule= The name of the Zoho Module for which record id is provided
	*/
	public function getRelatedRecords($module, $id, $parentModule, $maxRecords = 400)
	{
		if (!$id) return false;
		try
		{
			$post = array();
			$post['page']= $page = 1;
			$post['per_page'] = 200;
			$zoho_url = $this->url."$parentModule/$id/$module";


			$hasMore = true;
			$records = array();
			$resultAr = array();
			while ($hasMore)
			{
				$result = $this->get_from_zoho($zoho_url);
				if(isset($result->data)) $resultAr = array_merge($resultAr, $result->data);

				$post['page']= $post['page']+1;
				if (!isset($result->data) or count($result->data) < 200 or empty($result->data) or count($resultAr) >= $maxRecords) $hasMore = false;
			}

			$output = json_encode($resultAr, true);
			$records = json_decode($output, true);
			
			return array(
				'count' => count($records) ,
				'data' => $records
			);
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/* Description: Gets fields from zoho module
	* Parameters: $type= Specify the value as 1 or 2
	1 - To retrieve all fields from the summary view
	2 - To retrieve all mandatory fields from the module
	$module= The name of the Zoho Module for which record id is provided
	*/
	public function getFields($module, $type = '')
	{
		try
		{
			$post = array();
			$records = array();
			//$post['scope'] = 'ZohoCRM.settings.fields.all';
			$post['module'] = $module;
			
			if($type != ''){
				$post['type'] = "$type";
			}
			
			$url = $this->url."settings/fields";

			$fields_string = http_build_query($post, '', "&");
			$zoho_url = $url . "?$fields_string";

			$result = $this->get_from_zoho($zoho_url);

			if(isset($result->fields)) $records = $this->getFieldResult($result->fields);

		
			return $records;
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}

		return false;
	}





	public function getFieldResult($zohoResponse)
	{
		if (!$zohoResponse) return false;

		$result = array();
		foreach($zohoResponse as $d)
		{
			$fields = array();
			$secName = $d->field_label;
			unset($vals);
			
			$tmp = array(
				'field_label' => $d->field_label ,
				'api_name' => $d->api_name ,
				'type' => $d->data_type ,
				'required' => '' ,
				'length' => isset($d->length) ? $d->length : ""  ,
				'isCustom' => $d->custom_field ,
				'isReadOnly' => $d->read_only
			);
			if ($d->data_type == 'picklist')
			{
				$listVals = $d->pick_list_values;
				foreach($listVals as $v) $vals[] = $v->actual_value;
				
				if( isset( $vals ) ){
					$tmp['values'] = $vals;
				}else{
					$tmp['values'] = '';
				}
			}

			if ($d->data_type == 'multiselectpicklist')
			{
				$listVals = $d->pick_list_values;
				foreach($listVals as $v) $vals[] = $v->actual_value;
				
				if( isset( $vals ) ){
					$tmp['values'] = $vals;
				}else{
					$tmp['values'] = '';
				}
			}
			
			$result[$secName] = $tmp;
		}

		return $result;
	}


	//Zoho CRM New Formated field get method
	public function getFieldsNew($module, $type = '')
	{
		try
		{
			$post = array();
			$records = array();
			$post['module'] = $module;
			
			if($type != ''){
				$post['type'] = "$type";
			}
			
			$url = $this->url."settings/fields";


			$fields_string = http_build_query($post, '', "&");
			$zoho_url = $url . "?$fields_string";


			$result = $this->get_from_zoho($zoho_url);


			// echo "<pre>";var_dump($result);echo "</pre>";exit();
			if(isset($result->fields)) $records = $result->fields;
			return $records;
			
		}


		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}


		return false;
	}


	public function getUsers($type = 'AllUsers')
	{
		try
		{
			$post = array();
			$output = '';
			$records = array();
			$post['type'] = $type;
			$url = $this->url."users";

			$fields_string = http_build_query($post, '', "&");
			$zoho_url = $url . "?$fields_string";

			$result = $this->get_from_zoho($zoho_url);
			
			if(isset($result->users)) $output = json_encode($result->users, true);
			$records = json_decode($output, true);

			return $records;
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}

		return false;
	}

	/*	 
	*	Description: Gets all module list
	*/
	public function getModule()
	{
		try
		{
			$post = array();
			$output = '';
			$records = array();
			$zoho_url = $this->url."settings/modules";

			$result = $this->get_from_zoho($zoho_url);
			
			if(isset($result->modules)) $output = json_encode($result->modules, true);
			$records = json_decode($output, true);

			return $records;
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}

		return false;
	}


	public function SubformFields($module='')
	{
		// try
		// {
			$post = array();
			$output = '';
			$records = array();
			$zoho_url = $this->url.$module;

			$result = $this->get_from_zoho($zoho_url);

			return $result;
		// }

		// catch(Exception $e)
		// {
		// 	$this->error = $e->getMessage();
		// }

		// return false;
	}

	/*	 
	*Description: Gets search records from pre defined column [email, phone, word as column name]
	*/
	public function getSearchRecordsByPDC($module, $searchColumn, $searchValue, $selectColumns = array() , $maxRecords = 400)
	{
		try
		{
			$post = array();
			if (count($selectColumns)) $selectColumns = implode(',', $selectColumns);
			$post['fields'] = $selectColumns;
			$post[$searchColumn] = $searchValue;
			$post['page'] = 1;
			$post['per_page'] = 200;
			$url = $this->url."$module/search";

			$hasMore = true;
			$records = array();
			$resultAr = array();
			while ($hasMore)
			{
				$fields_string = http_build_query($post, '', "&");
				$zoho_url = $url . "?$fields_string";
				
				$result = $this->get_from_zoho($zoho_url);
				if(isset($result->data)) $resultAr = array_merge($resultAr, $result->data);

				$post['page']= $post['page']+1;
				if (!isset($result->data) or count($result->data) < 200 or empty($result->data) or count($resultAr) >= $maxRecords) $hasMore = false;
			}

			$output = json_encode($resultAr, true);
			$records = json_decode($output, true);

			return array(
				'count' => count($records) ,
				'data' => $records
			);
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage(); 
		}
	}


	public function getRecordsWithIndex($module, $fromIndex = 1)
	{
		$selectColumns = array();
		$maxRecords = 400;
		$sortColumn = 'Modified_Time';
		$sortBy = 'asc';
		
		try
		{
			$post = array();
			if (count($selectColumns)) $selectColumns = implode(',', $selectColumns);
			$post['fields'] = $selectColumns;
			$post['page'] = $fromIndex;
			$post['per_page'] = 200;
			$hasMore = true;
			$post['sort_by'] = $sortColumn;
			$post['sort_order'] = $sortBy;
			$records = array();
			$resultAr = array();
			$url = $this->url."$module";
			while ($hasMore)
			{
				$fields_string = http_build_query($post, '', "&");
				$zoho_url = $url . "?$fields_string";

				$result = $this->get_from_zoho($zoho_url);
				if(isset($result->data)) $resultAr = array_merge($resultAr, $result->data);
				
				$post['page']= $post['page']+1;
				if (!isset($result->data) or count($result->data) < 200 or empty($result->data) or count($resultAr) >= $maxRecords) $hasMore = false;
			}

			$output = json_encode($resultAr, true);
			$records = json_decode($output, true);

			return array(
				'count' => count($records) ,
				'data' => $records, 
				'toIndex' => $post['page']
			);
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/*
	* Description: search records from criteria [Ex: (((Phone:equals:12345679)AND(Last_Name:equals:Jhon))OR(Email:equals:abc@abc.com))]
	*/
	public function getSearchRecords($module, $searchCondition, $selectColumns = array(), $maxRecords = 400)
	{
		try
		{
			$post = array();
			if (count($selectColumns)) $selectColumns = implode(',', $selectColumns);
			$post['fields'] = $selectColumns;
			$post['criteria'] = $searchCondition;
			$post['page'] = 1;
			$post['per_page'] = 200;
			$url = $this->url."$module/search";
			$hasMore = true;
			$records = array();
			$resultAr = array();
			while ($hasMore)
			{
				$fields_string = http_build_query($post, '', "&");
				$zoho_url = $url . "?$fields_string";

				$result = $this->get_from_zoho($zoho_url);

				if(isset($result->data)) $resultAr = array_merge($resultAr, $result->data);
				
				$post['page']= $post['page']+1;
				if (!isset($result->data) or count($result->data) < 200 or empty($result->data) or count($resultAr) >= $maxRecords) $hasMore = false;
			}
			$output = json_encode($resultAr, true);
			$records = json_decode($output, true);
			return array(
				'count' => count($records) ,
				'data' => $records
			);
			
		}

		catch(Exception $e)
		{
			$this->error = $e->getmessage();
		}
	}

	/*
	* Description: Gets Custom view records from a zoho module
	* Parameters:  $module= The name of the Zoho Module
	*              $maxRecords= The maximum number of records to be returned
	* Returns: An array(count=>NUMBER_OF_RECORDS, data=array(THE ACTUAL DATA WITH ALL FIELDS AND ITS VALUE AS KEY => VALUE PAIR))
	* Notes: It would be nice to check if getError() method returns any value after calling this function for error handling
	*/
	public function getCVRecords($module, $cvName, $selectColumns = array() , $maxRecords = 400)
	{
		try
		{
			$post = array();
			if (count($selectColumns)) $selectColumns = implode(',', $selectColumns);
			$post['fields'] = $selectColumns;
			$post['page'] = 1;
			$post['per_page'] = 200;
			$post['module'] = $module;
			$hasMore = true;
			$records = array();
			$resultAr = array();
			$url = $this->url."settings/custom_views";
			while ($hasMore)
			{
				$fields_string = http_build_query($post, '', "&");
				$zoho_url = $url . "?$fields_string";

				$result = $this->get_from_zoho($zoho_url);
				if(isset($result->custom_views)) $resultAr = array_merge($resultAr, $result->custom_views);
				
				$post['page']= $post['page']+1;
				if (!isset($result->custom_views) or count($result->custom_views) < 200 or empty($result->custom_views) or count($resultAr) >= $maxRecords) $hasMore = false;
			}
			$output = json_encode($resultAr, true);
			$records = json_decode($output, true);

			return array(
				'count' => count($records) ,
				'data' => $records
			);
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/*
	* Description: Gets records from a zoho module
	* Parameters:  $module= The name of the Zoho Module
	*              $maxRecords= The maximum number of records to be returned
	*              $recordsPerPage= The number of records in single API call to be fetched. Default is 200
	* Returns: An array(count=>NUMBER_OF_RECORDS, data=array(THE ACTUAL DATA WITH ALL FIELDS AND ITS VALUE AS KEY => VALUE PAIR))
	* Notes: It would be nice to check if getError() method returns any value after calling this function for error handling
	*/
	public function getRecords($module, $selectColumns = array() , $maxRecords = 400, $sortColumn = 'Modified_Time', $sortBy = 'asc')
	{
		try
		{
			$post = array();
			$hasMore = true;
			$records = array();

			$url = $this->url."$module";
			
			if (count($selectColumns)) $selectColumns = implode(',', $selectColumns);
			$post['fields'] = $selectColumns;
			$post['sort_order'] = 'desc';
			$post['page'] = 1;
			$post['per_page'] = 200;
			
			$resultAr = array();
			
			while ($hasMore)
			{
				$fields_string = http_build_query($post, '', "&");
				$zoho_url = $url . "?$fields_string";

				$result = $this->get_from_zoho($zoho_url);
				if(isset($result->data)) $resultAr = array_merge($resultAr, $result->data);
				
				$post['page']= $post['page']+1;
				if (!isset($result->data) or count($result->data) < 200 or empty($result->data) or count($resultAr) >= $maxRecords) $hasMore = false;
				
			}

			$output = json_encode($resultAr, true);
			$records = json_decode($output, true);
			
			return array(
				'count' => count($records) ,
				'data' => $records
			);
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/*Description: Gets records from a zoho module
	*Parameters:  $module= The name of the Zoho Module*/
	function getMyRecords($module, $selectColumns = array() , $maxRecords = 400, $sortColumn = 'Modified_Time', $sortBy = 'asc')
	{
		try
		{
			$post = array();
			if (count($selectColumns)) $selectColumns = implode(',', $selectColumns);
			$post['fields'] = $selectColumns;
			$post['page'] = 1;
			$post['per_page'] = 200;
			$hasMore = true;
			$post['sort_by'] = $sortColumn;
			$post['sort_order'] = $sortBy;
			$records = array();
			$resultAr = array();
			$url = $this->url."$module";
			while ($hasMore)
			{
				$fields_string = http_build_query($post, '', "&");
				$zoho_url = $url . "?$fields_string";

				$result = $this->get_from_zoho($zoho_url);
				if(isset($result->data)) $resultAr = array_merge($resultAr, $result->data);
				
				$post['page']= $post['page']+1;
				if (!isset($result->data) or count($result->data) < 200 or empty($result->data) or count($resultAr) >= $maxRecords) $hasMore = false;
			}
			$output = json_encode($resultAr, true);
			$records = json_decode($output, true);

			return array(
				'count' => count($records) ,
				'data' => $records
			);
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	private function get_from_zoho($url){
		
		$authtoken = $this->authtoken;
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "Authorization: ".$authtoken
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  return json_decode($response);
		}

	}



	/*
	* Description: Used for inserting the records
	*/
	public function insertRecords($data = array() , $module, $wfTrigger = '', $isApproval = 'false', $duplicateCheck = 2)
	{
		try
		{
			$post = $this->makeJson($data, $wfTrigger);
			$zoho_url = $this->url."$module";
			$result = $this->post_to_zoho($zoho_url, $post);
			
			return json_decode($result);
		}
		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	//Insert purchase order
	public function insertRecordsPO($data, $module, $wfTrigger = 'workflow'){
		try
		{
			$jsonpost = json_encode($data);
			$post = '{"data": '.$jsonpost.',"trigger": ["'.$wfTrigger.'"]}';
			
			$zoho_url = $this->url."$module";
			$result = $this->post_to_zoho($zoho_url, $post);
			
			return json_decode($result);
		}
		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/*
	*Json return in this format:
	*For one array:
	*{"data": [{"Company": "Acme Inc","Last_Name": "Donelly","First_Name": "Jennifer","Email": "jennifer@acme.com"}],"trigger": ["approval"]}
	*
	*For Multiple array
	*{"data": [{"Company": "Acme Inc","Last_Name": "Donelly","First_Name": "Jennifer","Email": "jennifer@acme.com"},{"Company": "abc Inc","Last_Name": "Test","First_Name": "Jhn","Email": "abcd@acme.com"}],"trigger": ["approval"]}

	****************$wfTrigger = 'workflow' // if want to call webhook********************
	*/
	function makeJson($data, $wfTrigger = ''){
		
		/****************$wfTrigger = 'workflow' // if want to call webhook********************/

		$str = '';
		
		foreach ($data as $key => $value) {
			$post = json_encode($value, JSON_FORCE_OBJECT);
    		$str .= $post.',';
		}
		$strtrim = rtrim($str,',');
		$post = '{"data": ['.$strtrim.'],"trigger": ["'.$wfTrigger.'"]}';

		return $post;
	}

	function post_to_zoho($zoho_url, $fields){
		
		$authtoken = $this->authtoken;

		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $zoho_url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields); 
		curl_setopt($ch, CURLOPT_POST, 1); 
		$headers = array(); 
		$headers[] = "Authorization: ".$authtoken; 
		$headers[] = "Content-Type: application/json"; 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		
		$response = curl_exec($ch); 
		$err = curl_errno($ch);
		
		curl_close ($ch);
		if ($err) {
		  return $err;
		} else {
		  return $response;
		}
	}


	public function updateRecords($id,$data = array() , $module, $wfTrigger = "workflow")
	{
		try
		{
			$post = array();
			$data[0]['id'] = $id;

			$post = $this->makeJson($data, $wfTrigger);
			$zoho_url = $this->url."$module";

			$result = $this->update_to_zoho($zoho_url, $post);
			
			return json_decode($result);
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	//Update purchase order, sales order, invoice, Quoute
	public function updateRecordsPO($data, $module, $wfTrigger = "workflow"){
		try
		{
			if(in_array($module, array('Sales_Orders', 'Purchase_Orders', 'Invoices', 'Quotes',''))){
				foreach ($data as $key => $value) {
					if(!isset($value['Product_Details']) || count($value['Product_Details']) < 1){

						$rtArr['code'] = 'MANDATORY_NOT_FOUND';
						$rtArr['details']['api_name'] = 'Product_Details';
						$rtArr['message'] = 'required field not found';
						$rtArr['status'] = 'error';

						$fArr['data'][] = $rtArr;
						return $fArr;
						exit();
					}
				}
			}

			$jsonpost = json_encode($data);
			$post = '{"data": '.$jsonpost.',"trigger": ["'.$wfTrigger.'"]}';

			$zoho_url = $this->url."$module";
			$result = $this->update_to_zoho($zoho_url, $post);

			return json_decode($result);
		}
		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	function update_to_zoho($zoho_url, $fields){
		$authtoken = $this->authtoken;

		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $zoho_url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields); 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
		$headers = array(); 
		$headers[] = "Authorization: ".$authtoken; 
		$headers[] = "Content-Type: application/json";  
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		$response = curl_exec($ch); 
		$err = curl_errno($ch);
		
		curl_close ($ch);
		if ($err) {
		  return $err;
		} else {
		  return $response;
		}

	}


	/*
	* Description: Used for inserting/updating the records [update if exists]
	* In data array, should be pass [id] for update
	*/

	public function upsertRecords($data = array() , $module, $wfTrigger = ""){
		try
		{
			$post = array();

			$post = $this->makeJson($data, $wfTrigger);
			$zoho_url = $this->url."$module/upsert";

			$result = $this->post_to_zoho($zoho_url, $post);
			
			return json_decode($result);
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}


	public function deleteRecords($id , $module)
	{
		try
		{
			$post = array();
			$post['ids'] = $id;
			$url = $this->url."$module";

			$fields_string = http_build_query($post, '', "&");
			$zoho_url = $url . "?$fields_string";


			$result = $this->delete_from_zoho($zoho_url);
			//return json_decode($result);
			return true;
			
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/*
	*Delete file
	*Description: Used for deleting the files
	*/
	//change [Add parameter => attachment_id]
	public function deleteFile($module, $id, $attachment_id)
	{
		try
		{
			$zoho_url = $this->url."$module/$id/Attachments/$attachment_id";

			$result = $this->delete_from_zoho($zoho_url);
			return $result;
		}

		catch(Exception $e)
		{
			$this->error = $e->getmessage();
		}
	}

	public function getFiles($module, $id)
	{
		try
		{
			$post['scope'] = 'ZohoCRM.modules.'.$module.'.all';
			//$url = $this->url."$module/$id/Attachments/$attachment_id";

			$url = $this->url."$module/$id/Attachments";

			$fields_string = http_build_query($post, '', "&");
			$zoho_url = $url . "?$fields_string";

			$result = $this->get_from_zoho($zoho_url);

			//var_dump($result); exit();
			
			// return true;
			return $result;
		}

		catch(Exception $e)
		{
			$this->error = $e->getmessage();
		}
	}

	/*Download file
	*Description: Used for downloading file
	*/
	//change [Add parameter => attachment_id]
	public function downloadFile($module, $id, $attachment_id)
	{
		try
		{
			$post['scope'] = 'ZohoCRM.modules.'.$module.'.all';
			$url = $this->url."$module/$id/Attachments/$attachment_id";

			$fields_string = http_build_query($post, '', "&");
			$zoho_url = $url . "?$fields_string";

			$result = $this->download_from_zoho($zoho_url);
			
			// return true;
			return $result;
		}

		catch(Exception $e)
		{
			$this->error = $e->getmessage();
		}
	}

	/*
	*Upload file
	*Description: Used for uploading the record
	*/

	public function uploadFile($module,$content, $id)
	{
		try
		{
			$post['file'] = new \CURLFile($content);
			$ffilesize = filesize($content);
			if ($ffilesize >  20000000)
			{
				die("File size must be less than 20MB");
			}			

			$zoho_url = $this->url."$module/$id/Attachments";
			
			$result = $this->upload_to_zoho($zoho_url, $post);
			$records = json_decode($result);

			return $records;
			
		}

		catch(Exception $e)
		{
			$this->error = $e->getmessage();
		}

	}

	/*
	*Get Attachments
	*Description: Used for get Attachments list
	*/
	//change [Add parameter => attachment_id]
	public function getAttachmentsList($module, $id)
	{
		try
		{
			$zoho_url = $this->url."$module/$id/Attachments";

			$result = $this->get_from_zoho($zoho_url);
			return $result;
		}

		catch(Exception $e)
		{
			$this->error = $e->getmessage();
		}
	}

	

	public function downloadPhoto($module, $id)
	{
		try
		{
			$zoho_url = $this->url."$module/$id/photo";
			$result = $this->download_from_zoho($zoho_url);
			
			return $result;
		}

		catch(Exception $e)
		{
			$this->error = $e->getmessage();
		}
	}

	public function deletePhoto($module, $id)
	{
		try
		{
			$zoho_url = $this->url."$module/$id/photo";
			$result = $this->delete_from_zoho($zoho_url);
			
			return $result;
		}

		catch(Exception $e)
		{
			$this->error = $e->getmessage();
		}
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///  	Last Update 19/11/2020 Trigger restricted deafult if need to run workflow pass parameter in workflow "false" value
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function uploadPhoto($module,$content, $id,$workflow="workflow")
	{
		try
		{
			$post = array();
			$post['file'] = new \CurlFile($content);
			$ffilesize = filesize($content);
			if ($ffilesize >  20000000)
			{
				die("File size must be less than 20MB");
			}

			$trigger = "?restrict_triggers=".$workflow;
		
			$zoho_url = $this->url."$module/$id/photo".$trigger;
			$result = $this->upload_to_zoho($zoho_url, $post);
			$records = json_decode($result, true);

			return $records;			
			
		}

		catch(Exception $e)
		{
			$this->error = $e->getmessage();
		}
		
	}


	function download_from_zoho($zoho_url){
		$authtoken = $this->authtoken;
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $zoho_url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
		$headers = array(); 
		$headers[] = "Authorization: ".$authtoken; 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		
		$response = curl_exec($ch); 
		$err = curl_errno($ch);
		
		curl_close ($ch);
		if ($err) {
		  return $err;
		} else {
		  return $response;
		}

	}

	

	private function delete_from_zoho($url){
		
		$authtoken = $this->authtoken;
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

		$headers = array();
		$headers[] = "Authorization: ".$authtoken;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch); 
		$err = curl_errno($ch);
		
		curl_close ($ch);
		if ($err) {
		  return $err;
		} else {
		  return $response;
		}


	}

	public function getLayouts($module)
	{
		try
		{
			$post = array();
			$records = array();
			//$post['scope'] = 'ZohoCRM.settings.fields.all';
			$post['module'] = $module;
			
			$url = "https://www.zohoapis.com/crm/v2/settings/layouts";

			$fields_string = http_build_query($post, '', "&");
			$zoho_url = $url . "?$fields_string";

			$result = $this->get_from_zoho($zoho_url);

			if(isset($result->layouts) && (count($result->layouts) > 0)) $records = $result->layouts;
			
			return $records;
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}

		return false;
	}

	public function getRelatedListsMetaData($module)
	{
		try
		{
			$post = array();
			$records = array();

			$url = "https://www.zohoapis.com/crm/v2/settings/related_lists?module=".$module;

			$result = $this->get_from_zoho($url);
			
			return $result;
		}

		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}

		return false;
	}


	function upload_to_zoho($zoho_url, $fields){
		
		$authtoken = $this->authtoken;

		$ch=curl_init();
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_URL,$zoho_url);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);
		$headers = array(); 
		$headers[] = "Authorization: ".$authtoken; 
		$headers[] = "Content-Type: multipart/form-data"; 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 

		$response = curl_exec($ch); 
		$err = curl_errno($ch);
		
		curl_close ($ch);
		if ($err) {
		  return $err;
		} else {
		  return $response;
		}
	}




























	
}


/*****************************************************************/
/**********************Testing of the Class***********************/
/*****************************************************************/


/*****************************************************************/
/*****Example of data array to be passed in various methods*******/
/*****************************************************************/
/*
$data=array(

array(
'Company' => 'R Square G Web',
'First Name' => 'John',
'Email' => 'ramkumar15685@gmail.com', 
'Last Name' => 'Doe'
),
array(
'Company' => 'R Square G Web',
'First Name' => 'Gaurav',
'Last Name' => 'Gupta'
)
);
*/
/*****************************************************************/
/*****Example of data array to be passed in various methods*******/
/*****************************************************************/
/*$test = new Zoho('d33dbd9c6d3e854682c3af81efe9604b', 1);
$result = $test->convertLead('531636000000061001');
$test->debug($result);*/
