<?php
/**
*** @12/09/2018 by Softanis
*** @sorfuddin@gmail.com
*** @tutorial: Zoho Books API management class
*** @method:https://www.zoho.com/books/api/v3/invoices/#create-an-invoice
**/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DOMDocument;
use Illuminate\Support\Facades\DB;

class ZohoDesk extends Model
{

	protected $table    = 'zoho_desk_auth';
    protected $fillable = ['zoho_account_id','organization_id','authorized_client_name','authorized_redirect_url',
                            'client_id','client_secret','code','access_token','refresh_token','create_time'];

    protected $authToken;
    protected $organization_id;
    public $url       = "https://desk.zoho.com/api/v1/";
    public $newFormat = 1;

    public function __construct($authToken = '', $newFormat = 1)
    {
        $auth_data = DB::table('zoho_desk_auth')->orderBy('id', 'desc')->first();          
        
        $this->authToken = $auth_data->access_token;   
        $this->newFormat = $newFormat;
        $this->organization_id = $auth_data->organization_id;

        $db_time_with_increase_time = date('Y-m-d H:i:s', strtotime("+59 minutes", strtotime($auth_data->create_time)));
        $dtA = new \DateTime($db_time_with_increase_time);
        $dtB = new \DateTime();
        
        if ( $dtA < $dtB ) {
            $insArr['organization_id'] = $auth_data->organization_id;
            $insArr['authorized_redirect_url'] = $auth_data->authorized_redirect_url;

            $insArr['refresh_token'] = $data['refresh_token'] = $auth_data->refresh_token;
            $insArr['client_id'] = $data['client_id'] = $auth_data->client_id;
            $insArr['client_secret'] = $data['client_secret'] =$auth_data->client_secret;
            $data['grant_type'] = 'refresh_token';

            $token_data = $this->refresh_authtoken($data);
            $token_dataArr = json_decode($token_data);

            if(isset($token_dataArr->access_token)) $this->authToken = $token_dataArr->access_token;

            $insArr['access_token'] = $token_dataArr->access_token;
            $insArr['authorized_client_name'] = $auth_data->authorized_client_name;
            $insArr['code'] = $auth_data->code;
            $insArr['create_time'] = new \DateTime();

            DB::table('zoho_desk_auth')->insert($insArr); 
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


    //GET Functions Here

    function getRecordById($Id, $module, $organization_id = NULL,$url='')
    {
        if($url==''){
            $url = $this->url.$module.'/'.$Id;
        }else{
            $url = $url.$module.'/'.$Id;
        }

        $result = $this->get_from_desk($url);
       
        return $result;
    }


    function getRecords($module, $page=0,$url=''){
        
        if($url == ''){
            $url = $this->url.$module.'?from='.$page;
        }else{
            $url = $url.$module.'?from='.$page;
        }

        $header = array(
            "Content-Type: application/json;charset=UTF-8"
        );

        $result = $this->get_from_desk($url);
        $result = json_decode( $result, true );
        $result['organization_id'] = $this->organization_id;
        return $result;
    }

    //GET Curl
    
    function get_from_desk($url) {
        if($this->authToken == '' || $this->organization_id== ''){
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
          CURLOPT_POSTFIELDS => "",
          CURLOPT_HTTPHEADER => array(
            "Authorization: Zoho-oauthtoken ".$this->authToken,
            "orgId: ".$this->organization_id
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

    function get_organization_info(){

        if($this->authToken == ''){
            return false; 
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://desk.zoho.com/api/v1/organizations",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_POSTFIELDS => "",
          CURLOPT_HTTPHEADER => array(
            "Authorization: Zoho-oauthtoken ".$this->authToken
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

    public function updateRecord($data, $module, $id)
    {
        $url = $this->url.$module.'/'.$id;
        $result = $this->post_to_desk($url, json_encode($data), "PATCH");
        $result = json_decode( $result, true );
        $result['organization_id'] = $this->organization_id;
        return $result;
    }

    public function createRecord($data, $module)
    {
        $url = $this->url.$module;
        $result = $this->post_to_desk($url, json_encode($data));
        return $result;
    }


    //Post Curl Here
    
    function post_to_desk($url, $fields, $method = "POST"){
        $authtoken = $this->authToken;
        $organization_id = $this->organization_id;

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => $method,
          // CURLOPT_POSTFIELDS => http_build_query($fields),
          CURLOPT_POSTFIELDS => $fields,
          CURLOPT_HTTPHEADER => array(
            "Authorization: Zoho-oauthtoken ".$authtoken,
            "orgId: ".$organization_id
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        $info = curl_getinfo($curl); 

        curl_close($curl);

        if ($err) {
          return $err;
        } else {
          return $response;
        }

    }




}