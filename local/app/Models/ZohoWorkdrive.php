<?php
/**
*** @31/10/2019 by Softanis
*** @deslwarsumon0@gmail.com
*** @tutorial: Zoho WorkDrive API management class
*** @method:https://writer.zohopublic.com/writer/published/ankqibdb934759c6446bca8899b2f426428fc
**/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DOMDocument;
use Illuminate\Support\Facades\DB;

class ZohoWorkdrive 
{


	  protected $table    = 'zoho_work_drive_auth';
    protected $fillable = ['organization_id','authorized_client_name','authorized_redirect_url',
                            'client_id','client_secret','code','access_token','refresh_token','create_time'];

    protected $authToken;
    public $url       = "https://workdrive.zoho.com/api/v1/";

    public $template_folder_id = ""; //client template folder id

    public function __construct($authToken = '')
    {
        $auth_data = DB::table('zoho_work_drive_auth')->orderBy('id', 'desc')->first();          
        
        $this->authToken = $auth_data->access_token;   
        
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

            DB::table('zoho_work_drive_auth')->insert($insArr); 
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
    function getAllFilesFoldersById($folder_id, $offset = 0, $limit = 50, $type = 'all'){
        $url = $this->url."files/".$folder_id."/files";

        $post = array(
          'page' => array(
            'offset' => $offset,
            'limit' => $limit,
          ),
          'filter' => array('type' => $type)
        );
        
        $fields_string = http_build_query($post, '', "&");
        $url = $url . "?$fields_string";

        $result = $this->get_from_zoho($url);
        return $result;
    }

    
    function getFileOrFolderById($folder_id){
      $url = $this->url."files/".$folder_id;
      $result = $this->get_from_zoho($url);
      return $result;
  }

    function createFolder($folder_name, $parent_id){ //$folder_id = destination folder id
        $url = $this->url."files";
        $post['data'] = array(
          'attributes' => array( 
            "name" => $folder_name,
            "parent_id" => $parent_id,
          ),
          "type" => "files" 
        );
        $post = json_encode($post);
        $result = $this->post_to_zoho($url, $post);
        return $result;
    }

    function copyFolder($folder_id, $template_folder_id){ //$folder_id = destination folder id
        $url = $this->url."files/".$folder_id."/copy";
        $post['data'] = array(
          'attributes' => array( 
            "resource_id" => $template_folder_id
          ),
          "type" => "files" 
        );
        $post = json_encode($post);
        $result = $this->post_to_zoho($url, $post);
        return $result;
    }

    function uploadFile($post){
        $url = $this->url."upload";
        $result = $this->upload_to_zoho($url, $post);
        return $result;
    }

    function downloadFile($id){
        $url = $this->url."download/".$id;
        $result = $this->get_from_zoho($url);
        return $result;
    }


    //GET Curl
    
    function get_from_zoho($url) {
        if($this->authToken == ''){
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


    //Post Curl Here
    
    function post_to_zoho($url, $fields){
        $authtoken = $this->authToken;
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $fields,
          CURLOPT_HTTPHEADER => array(
            "Authorization: Zoho-oauthtoken ".$authtoken,
            "content-type: application/x-www-form-urlencoded"
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


    function upload_to_zoho($zoho_url, $fields){
    
      $authtoken = $this->authToken;

      $filepath=$fields['content'];

      $realPath = realpath($filepath);
      $content =curl_file_create($realPath);
      
      $fields['content'] = $content;
      
      $curl_var = curl_init();
      curl_setopt($curl_var, CURLOPT_URL, $zoho_url);
      curl_setopt($curl_var, CURLOPT_POST, 1);
      curl_setopt($curl_var, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl_var, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($curl_var, CURLOPT_POSTFIELDS, $fields);
      curl_setopt($curl_var, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $authtoken, 'cache-control: no-cache'));
      $response = curl_exec($curl_var);
      $err = curl_errno($curl_var);
      curl_close($curl_var);

      if ($err) {
        return $err;
      } else {
        return $response;
      }

    }











}