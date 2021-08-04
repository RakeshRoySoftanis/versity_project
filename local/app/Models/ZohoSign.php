<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DOMDocument;
use Illuminate\Support\Facades\DB;

class ZohoSign extends Model{
	
	private $authtoken;
	private $url = "https://sign.zoho.com/api/v1/";

	public function __construct($authtoken = '')
	{
		$auth_data = DB::table('zoho_sign_auth')->orderBy('id', 'desc')->first();		
  
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

			if(isset($token_dataArr->access_token)) $this->authtoken = 'Zoho-oauthtoken '.$token_dataArr->access_token;

			$insArr['access_token'] = $token_dataArr->access_token;
			$insArr['authorized_client_name'] = $auth_data->authorized_client_name;
			$insArr['code'] = $auth_data->code;
			$insArr['organization_id'] = $auth_data->organization_id;
			$insArr['authorized_redirect_url'] = $auth_data->authorized_redirect_url;
			$insArr['create_time'] = new \DateTime();

			DB::table('zoho_sign_auth')->insert($insArr); 
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


	function getRecordById($Id, $module)
	{
		$url = $this->url.$module.'/'.$Id;
		$result = $this->get_from_zsg($url);
		return $result;
	}

	function getRecords($module, $start_index = 1, $row_count = 100, $search_columns = array(), $sort_column = "created_time", $sort_order = "DESC"){
		$post = array();
		$post['page_context'] = array(
			"row_count" => $row_count,
			"start_index" => $start_index,
			//"search_columns" => $search_columns,
			"sort_column" => $sort_column,
			"sort_order" => $sort_order
		);

		$params = json_encode($post);

		$url = $this->url.$module.'?data='.urlencode($params);

		return $result = $this->get_from_zsg( $url);

	}

	


	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
		GET Curl
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	function get_from_zsg($url) {
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
				"Host: sign.zoho.com",
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





}

?>