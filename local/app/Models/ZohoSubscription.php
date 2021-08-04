<?php
/**
*** @15/07/2015 by Softanis
*** @sorfuddin@gmail.com
*** @tutorial: Zoho Subscription API management class
*** @method: https://www.zoho.com/subscriptions/api/v1/#list-all-customers
**/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DOMDocument;
use Illuminate\Support\Facades\DB;

class ZohoSubscription
{
    protected $authToken;
	protected $organizationid;
	
    public $scope = 'ZohoSubscriptions/subscriptionsapi';
    public $url = 'https://subscriptions.zoho.com/api/v1/';  
    
    // Initialize the constructor, with the Zoho CRM Auth Token value.
    public function __construct()
	{
		// $auth_data = DB::table('zoho_subscriptions_auth')->orderBy('id', 'desc')->first();		   
		
		// if(isset($auth_data->access_token)){
		// 	$this->authToken = $auth_data->access_token;   
		// 	$this->organizationid = $auth_data->organization_id; 
		// }
		$auth_data = DB::table('zoho_subscriptions_auth')->orderBy('id', 'desc')->first();		   
		
		$this->authToken = $auth_data->access_token;   
		$this->organizationid = $auth_data->organization_id;   

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

			if(isset($token_dataArr->access_token)){
				$this->authToken = $token_dataArr->access_token;

				$insArr['access_token'] = $token_dataArr->access_token;
				$insArr['authorized_client_name'] = $auth_data->authorized_client_name;
				$insArr['code'] = $auth_data->code;
				$insArr['create_time'] = new \DateTime();

				DB::table('zoho_books_auth')->insert($insArr); 
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
    
    // Set input parameters for the Zoho API request
    private function setParameters($extra = array())
    {        
        $parameters['newFormat'] = $this->newFormat;
        $parameters['authtoken'] = $this->authToken;
        $parameters['scope'] = $this->scope;
        
        if(!empty($extra))
            $parameters = array_merge($parameters, $extra);
            
        $string = '';
        foreach($parameters as $key => $val) {
            $string .= "$key=".urlencode($val).'&';
        }
        
        return trim($string, '&');
    }
    
    // Convert the Object into Array format
    function object2Array_old($d)
    {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            return array_map(__FUNCTION__, $d);
        } else {
            return $d;
        }
    }
	
	
	function object2Array( $object )
    {
        if( !is_object( $object ) && !is_array( $object ) )
        {
            return $object;
        }
        if( is_object( $object ) )
        {
            $object = get_object_vars( $object );
        }
		
		return  array_map(array($this, 'object2Array'), $object);
		
       //  return array_map( $this->object2Array, $object );
    }


    /*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
		 Functions Here
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	public function test()
	{
		$t = "work";
		return $t;
	}

	function getRecordById($Id, $module)
	{
		$url = $this->url.$module.'/'.$Id.'?';
		$result = $this->get_from_zs($url);
		return $result;
	}

	function getRecords($module, $page=1){
		$url = $this->url.$module.'/?page='.$page.'&';

		return $result = $this->get_from_zs( $url);

	}




	
	
	function addCustomer($customerArray){
		
		$url = $this->url.'customers';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		return $result = $this->doRequest( $url, 'POST', $customerArray, $header );
	}
	
	function addSubscribe($customerArray){
		
		$url = $this->url.'subscriptions';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		return $result = $this->doRequest( $url, 'POST', $customerArray, $header );
	}
	
	function getSubscribe(){
		
		$url = $this->url.'events';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		return $result = $this->doRequest( $url, 'GET');
	}

	function getProducts(){
		
		$url = $this->url.'products';
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		return $result = $this->doRequest($url, 'GET');
	}

	function getProductsByID($id){
		
		$url = $this->url.'products/'.$id;
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		return $result = $this->doRequest($url, 'GET');
	}

	function getContactByID($id){

		$url = $this->url.'customers/'.$id;
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		return $result = $this->doRequest( $url, 'GET');
	}

	function getPlansByProductID($id){

		$final_items = array();
		
		$url = $this->url.'plans?filter_by=PlanStatus.ACTIVE&product_id='.$id;	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);

		return $result = $this->doRequest( $url, 'GET');

	}
	
	function getSubscriptionbyId($subscriptionID){
		
		$url = $this->url.'subscriptions/'.$subscriptionID;	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		return $result = $this->doRequest( $url, 'GET');
	}

	
	function getInvoicebyId($invoiceID){
		
		$url = $this->url.'invoices/'.$invoiceID;	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		return $result = $this->doRequest( $url, 'GET');
	}

	function getContactByZcrmAccountId($zcrm_account_id)
	{
		$url = $this->url.'customers/reference/'.$zcrm_account_id.'?reference_id_type=zcrm_account_id';
		return $result = $this->doRequest( $url, 'GET');
	}

	private function prepareParameters($extra = array())
    {
        // $parameters['newFormat'] = $this->newFormat;
        $parameters['authtoken'] = $this->authToken;
        $parameters['scope'] = $this->scope;

        if(!empty($extra))
            $parameters = array_merge($parameters, $extra);

        $string = '';
        foreach($parameters as $key => $val) {
            $string .= "$key=".urlencode($val).'&';
        }

        return trim($string, '&');
    }
	
	/*function updateCreditcardbyId($customerID, $cardID, $cardArrayy){
		
		$url = $this->url.'customers/'.$customerID.'/cards/'.$cardID;	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		var_dump($url);
		
		return $result = $this->doRequest( $url,'PUT',$cardArrayy, $header);
	}*/
	
	function updateCreditcardbyId($subscriptionid, $cardArray){
		
		$url = $this->url.'subscriptions/'.$subscriptionid.'/card';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		return $result = $this->doRequest( $url,'POST',$cardArray, $header);
	}

	function cancelNowSubscription($subscriptionid){
		
		$url = $this->url.'subscriptions/'.$subscriptionid.'/cancel?cancel_at_end=false';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		return $result = $this->doRequest( $url,'POST');
	}
	
	function cancelsubscription($subscriptionid){
		
		$url = $this->url.'subscriptions/'.$subscriptionid.'/cancel?cancel_at_end=true';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		return $result = $this->doRequest( $url,'POST');
	}
	
	function Reactivesubscription($subscriptionid){
		
		$url = $this->url.'subscriptions/'.$subscriptionid.'/reactivate';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		return $result = $this->doRequest( $url,'POST');
	}
	
	function getCardbyId($customerID, $cardID){

		
		$url = $this->url.'customers/'.$customerID.'/cards/'.$cardID;	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
			
		);
		
		return $result = $this->doRequest( $url, 'GET');
	}
	
	
	function getSubscriptionList(){

		
		$url = $this->url.'subscriptions ';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
			
		);
		
		return $result = $this->doRequest( $url, 'GET');
	}

	function updateCustomerById($customer_id, $customerArray){
		
		$url = $this->url.'customers/'.$customer_id;	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
			
		);
		
		return $result = $this->doRequest( $url, 'PUT', $customerArray);
	
	}
	
	function updateByHostedPage($hostedArray){
		
		$url = $this->url.'hostedpages/updatecard';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
			
		);
		
		return $result = $this->doRequest( $url, 'POST', $hostedArray);
	
	}
	
	
	function CreateSubscriptionByHostedPage($hostedArray){
		
		$url = $this->url.'hostedpages/newsubscription';
		return $result = $this->doRequest( $url, 'POST', $hostedArray);
	
	}

	function UpdateSubscriptionByHostedPage($hostedArray){
		
		$url = $this->url.'hostedpages/updatesubscription';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
			
		);
		
		return $result = $this->doRequest( $url, 'POST', $hostedArray);
	
	}
	
	
	
	function getHostedPage(){
	$url = $this->url.'hostedpages/2-b41daab8520c119ed8490075cae83431db02f59d003d38dd9c790243566fc024842ccf646307cc7dadd4a75026e7441d';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
			
		);
		
		return $result = $this->doRequest( $url, 'GET');
	
	}


	/*------------------------------------------------------------------------------
		Contact 
	--------------------------------------------------------------------------------*/
	function createContactByCRMContact($crm_contact){

		$contact_array = array(
			'display_name'=>$crm_contact->Full_Name,
            'first_name'=> $crm_contact->First_Name,
			'last_name'=> $crm_contact->Last_Name,
			'email'=> $crm_contact->Email					
        );

		$jsonpost = json_encode($contact_array);
		$url = $this->url.'customers';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
			
		);
		
		return $result = $this->doRequest( $url, 'POST', $jsonpost);
	}


	function createContact($contact_array){
		$jsonpost = json_encode($contact_array);
		$url = $this->url.'customers';	
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
			
		);
		
		return $result = $this->doRequest( $url, 'POST', $jsonpost);

	}

	

	//---------------------------------------------------------------------------------
	
	
	
	function doRequest($url, $method='', $vars='', $additionalHeader = '') {
		
		if($this->authToken == '' || $this->organizationid == ''){
			return false; 
		}
		
		$header = array(
			"X-com-zoho-subscriptions-organizationid: $this->organizationid",
			"Authorization: Zoho-authtoken $this->authToken"
		);
	
		
		if($additionalHeader != ''){
			$header = array_merge($additionalHeader, $header);
		}
		
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		
	    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    
		if ($method == 'POST') {
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	    }

	    if($method == 'PUT'){
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		}
            
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	    $data = curl_exec($ch);


	  	
	    if ($data) {
	      
	            return $data;
	      
	    } else {
	        return curl_error($ch);
	    }
	    curl_close($ch);
	}
	
	
	
	function getOrganizationId($url='', $method='', $vars='') {
		
						 
		$url = $this->url.'organizations';		
		
		$header = array(
			"Authorization: Zoho-authtoken $this->authToken"
		);
		
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		
	    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    
		if ($method == 'POST') {
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	    }
            
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	    $data = curl_exec($ch);
	  	
	    if ($data) {
	        if ($this->callback)
	        {
	            $callback = $this->callback;
	            $this->callback = false;
	            return call_user_func($callback, $data);
	        } else {
	            return $data;
	        }
	    } else {
	        return curl_error($ch);
	    }
		
	    curl_close($ch);
	}
	
	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
		GET Curl
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	function get_from_zs($url) {
		if($this->authToken == '' || $this->organizationid == ''){
			return false; 
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url."Content-Type=application/json;charset=UTF-8",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_POSTFIELDS => "",
			CURLOPT_HTTPHEADER => array(
				// "Authorization: Zoho-authtoken ".$this->authToken,
				"Authorization: Zoho-oauthtoken ".$this->authToken,
				"X-com-zoho-subscriptions-organizationid: ".$this->organizationid,
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





	/*
	*-------------------------------------------------
	*/
	
    // Get the modules records list, upto 200 maximum records per request
    // module = Accounts, Contacts, Vendors, Invoices, Products, etc ...
   
}
