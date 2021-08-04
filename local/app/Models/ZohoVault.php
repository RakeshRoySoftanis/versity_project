<?php
/**
*** @20/08/2019 by Softanis
*** @delwarsumon0@gmail.com
*** @tutorial: Zoho Vault API Doc
*** @method:https://www.zoho.com/vault/api/
*** @Read important notes bottom of the class
**/


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DOMDocument;
use Illuminate\Support\Facades\DB;

class ZohoVault extends Model{
	
	private $authtoken;
    private $url = 'https://vault.zoho.com/api/rest/json/v1/';  

	public function __construct($authtoken = '')
	{
		$auth_data = DB::table('zoho_vault_auth')->orderBy('id', 'desc')->first();		
  
		$this->authtoken = 'Zoho-oauthtoken '.$auth_data->access_token;   

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

			if(isset($token_dataArr->access_token)){ 
				$this->authtoken = 'Zoho-oauthtoken '.$token_dataArr->access_token;

				$insArr['access_token'] = $token_dataArr->access_token;
				$insArr['authorized_client_name'] = $auth_data->authorized_client_name;
				$insArr['code'] = $auth_data->code;
				$insArr['organization_id'] = $auth_data->organization_id;
				$insArr['authorized_redirect_url'] = $auth_data->authorized_redirect_url;
				$insArr['create_time'] = new \DateTime();

				DB::table('zoho_vault_auth')->insert($insArr); 
			}
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

	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
	||	GET Functions
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	function getRecordById($Id, $module)
	{
		$url = $this->url.$module.'/'.$Id.'?';
		$result = $this->get_from_zoho($url);
		return $result;
	}

	function getRecords($module, $page=0){
		
		$url = $this->url.$module.'?isAsc=true&pageNum='.$page.'&rowPerPage=50';

		return $result = $this->get_from_zoho( $url);

	}

	function getSearchRecords($module, $page=0, $param = array()){
		
		if(empty($param)){
			$url = $this->url.$module.'?isAsc=true&pageNum='.$page.'&rowPerPage=50';
		}else{
			$param['isAsc'] = "true";
			$param['pageNum'] = $page;
			$param['rowPerPage'] = 50;
			$fields_string = http_build_query($param, '', "&");

			$url = $this->url.$module."?$fields_string";
		}
		return $result = $this->get_from_zoho( $url);

	}



	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
		GET Curl
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	function get_from_zoho($url){
		if($this->authtoken == ''){
			return false; 
		}
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


		$headers = array();
		$headers[] = 'Authorization: '.$this->authtoken;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		$err = curl_error($ch);

		curl_close($ch);

		if ($err) {
		  return $err;
		} else {
		  return $response;
		}

	}

	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
	||	POST Functions
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	public function insertRecord($data = array() , $module)
	{
		try
		{
			$post = "INPUT_DATA= ".json_encode($data);
			$zoho_url = $this->url."$module";
			$result = $this->post_to_zoho($zoho_url, $post);
			return $result;
		}
		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}


	public function updateRecord($id,$data = array() , $module)
	{
		try
		{
			$post = "INPUT_DATA= ".json_encode($data);
			$zoho_url = $this->url."$module/$id";
			$result = $this->update_to_zoho($zoho_url, $post);
			return $result;
		}
		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
	||	DELETE Functions
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	public function deleteRecord($id , $module)
	{
		try
		{
			$zoho_url = $this->url."$module/$id";
			$result = $this->delete_from_zoho($zoho_url);
			return $result;
		}
		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}

	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
		POST Curl
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	
	function post_to_zoho($zoho_url, $fields){
		
		if($this->authtoken == ''){
			return false; 
		}
		
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $zoho_url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields); 
		curl_setopt($ch, CURLOPT_POST, 1); 
		$headers = array(); 
		$headers[] = "Authorization: ".$this->authtoken; 
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


	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
		Update Curl
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	function update_to_zoho($zoho_url, $fields){
		if($this->authtoken == ''){
			return false; 
		}
		
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $zoho_url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields); 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
		$headers = array(); 
		$headers[] = "Authorization: ".$this->authtoken; 
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


	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
		DELETE Curl
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	function delete_from_zoho($url){
		
		if($this->authtoken == ''){
			return false; 
		}
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

		$headers = array();
		$headers[] = "Authorization: ".$this->authtoken;
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

	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
	||	LOGIN Functions
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	function loginRequest($operation){
		$url = "https://vault.zoho.com/api/json/login?OPERATION_NAME=".$operation;
		return $result = $this->login_request_to_zoho( $url);

	}


	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
		LOGIN Curl
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	function login_request_to_zoho($url){
		
		if($this->authtoken == ''){
			return false; 
		}

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
		    "Accept: */*",
		    "Accept-Encoding: gzip, deflate",
		    "Authorization: ".$this->authtoken,
		    "Cache-Control: no-cache",
		    "Connection: keep-alive",
		    "Cookie: 3882114f56=374c69881851202617aa9c9a1d9650d7; zvcsr=a5932918-88b5-4fab-ae05-d91adef25d57; JSESSIONID=402AB9EFBFDB9973D06AB1D82FD6D692",
		    "Host: vault.zoho.com",
		    "Postman-Token: f97492fe-d647-42aa-8427-fcd865411e24,a27c4832-9066-4f52-bf7c-df306081676c",
		    "User-Agent: PostmanRuntime/7.19.0",
		    "cache-control: no-cache"
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



	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
	||	Secret Type Functions
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	public function getSecretTypes()
	{
		try
		{
			$result = $this->curl_for_secret_types();
			return $result;
		}
		catch(Exception $e)
		{
			$this->error = $e->getMessage();
		}
	}


	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
		Secret Type Curl
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	function curl_for_secret_types()
	{	
		if($this->authtoken == ''){
			return false; 
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://vault.zoho.com/api/json/secrets",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "OPERATION_NAME=GET_SECRET_TYPES_EXT",
			CURLOPT_HTTPHEADER => array(
			    "Authorization: ".$this->authtoken,
			    "Content-Type: application/x-www-form-urlencoded"
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
	|	Important Notes
	|	----------------------
	*/

	/*
	*	Module Name [secrets, chambers]
	*	For Create Secret [Required Fields => secretname, classification, secrettypeid, secretdata{username, password}]
		--------------------------
		$data = array(
            "secretname" => "Dev Secret 3",
            "isshared" => "NO",
            "description" => "description about Dev secret 3",
            "classification" => "E",
            "secrettypeid" => "85308000000000015",
            "chamberid" => "85308000000014051", //optional, if you want to associate new create secret with this chamber
            "secretdata" => array(  
                "password" => "devuser3",
                "username" => "devuser1233"
            ),
        );
        $zData = $ZohoVault->insertRecords($data, "secrets");

	*	For Update Secret [Required Fields => id, secretname, classification, secretdata{username, password}]
		--------------------------
		$up_data = array(
            "secretname" => "Dev Secret UP 2",
            "isshared" => "Yes",
            "description" => "This is description about Dev secret 22",
            "tags" => "tags1, tags12",
            "classification" => "E",
            // "secrettypeid" => "85308000000000015",
            "secretdata" => array(  
                "password" => "devuser",
                "username" => "devuser123"
            ),
        );
        $zData = $ZohoVault->updateRecords('85308000000015001', $up_data, "secrets");

	*	For create Chamber [Required Fields => chambername]
		-------------------
		$data = array(
            "chambername" => "Dev Chamber 2",
            "chamberdesc" => "This is description about Dev chamber 2",
            "chambersecrets" => array("85308000000013011"), //optional, If you want to associate secrets(you can give multiple secretid ) with this chamber
        );
		$zData = $ZohoVault->insertRecords($data, "chambers");
	
	*	For update Chamber [Required Fields => chambername]
		-------------------
		$up_data = array(
            "chambername" => "Dev Chamber 2",
            "chamberdesc" => "This is description about Dev chamber 2",
            "chambersecrets" => array("85308000000013011", "85308000000016003"), //optional, update of associate secrets - If you want to associate new secrets then previous associate secrets will be deleted. So if you want new secrets with pervious then you need to put previous secrets ids and new secret ids
        );
        $zData = $ZohoVault->updateRecord('85308000000016010', $up_data, "chambers");
		
	*/



}

?>