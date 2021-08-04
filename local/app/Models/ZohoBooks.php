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

class ZohoBooks 
{
   
	protected $authToken;
	protected $organization_id;

    public $scope = 'ZohoBooks/booksapi';
    public $url = 'https://books.zoho.com/api/v3/';
	public $newFormat = 1;

    public function __construct($authToken = '', $newFormat = 1)
	{
		$auth_data = DB::table('zoho_books_auth')->orderBy('id', 'desc')->first();		   
		
		$this->authToken = $auth_data->access_token;   
		$this->organization_id = $auth_data->organization_id;   
		$this->newFormat = $newFormat;

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

			DB::table('zoho_books_auth')->insert($insArr); 
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
		GET Functions Here
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	public function test()
	{
		$t = "";
		return $t;
	}

	function getRecordById($Id, $module)
	{
		$url = $this->url.$module.'/'.$Id.'?organization_id='.$this->organization_id.'&';
		$result = $this->get_from_books($url);
		return $result;
	}

	function getRecords($module, $page=1){
		
		$url = $this->url.$module.'/?organization_id='.$this->organization_id.'&page='.$page;
		// echo $url;
		$header = array(
			"Content-Type: application/json;charset=UTF-8"
		);
		
		return $result = $this->get_from_books($url);
	}


	function getInvoiceByinvId($Id)
	{
		$url = $this->url.'invoices/'.$Id.'?organization_id='.$this->organization_id.'&';
		$result = $this->get_from_books($url);

		$inv_object = json_decode($result);
		
		$return_result = (isset($inv_object->invoice)) ? $inv_object->invoice : array();

		return $return_result;
		
	}

	function getRecurringInvoiceByinvId($Id){
		$url = $this->url.'recurringinvoices/'.$Id.'?organization_id='.$this->organization_id.'&';
		$result = $this->get_from_books($url);

		$inv_object = json_decode($result);

		$return_result = (isset($inv_object->recurring_invoice)) ? $inv_object->recurring_invoice : array();

		return $return_result;
		// return $inv_object->recurring_invoice;
		
	}

	public function getContactById($contact_id){
		$url = $this->url.'contacts/'.$contact_id.'?organization_id=674675567';
		$result = $this->get_from_books( $url);
		$contact_object = json_decode($result);
		
		$return_result = (isset($contact_object->contact)) ? $contact_object->contact : array();
		return $return_result;

	}

	public function getContactByZcrmContactId($zcrm_contact_id)
	{
		$params = $this->prepareParameters(array(
			'zcrm_contact_id'=>$zcrm_contact_id
		));

		$url = $this->url.'contacts?'.$params;
		$result = $this->get_from_books( $url);
		$contact_object = json_decode($result);
		$contacts = $contact_object->contacts;
		$contact = (count($contacts)>0) ? $contacts[0] : false;

		return $contact;
	}

	function getContactByZcrmAccountId($zcrm_account_id)
	{
		$params = $this->prepareParameters(array(
			'zcrm_account_id'=>$zcrm_account_id
		));

		$url = $this->url.'contacts?'.$params;
		$result = $this->get_from_books( $url);
		$contact_object = json_decode($result);
		$contacts = $contact_object->contacts;
		$contact = (count($contacts)>0) ? $contacts[0] : false;

		return $contact;
	}


	function getInvoiceByRecurringInvoiceId($recurring_invoice_id)
	{
		$params = $this->prepareParameters(array(
			'recurring_invoice_id'=>$recurring_invoice_id
		));

		$url = $this->url.'invoices?organization_id='.$this->organization_id.'&'.$params;
		$result = $this->get_from_books( $url);
		
		$inv_object = json_decode($result);
		// echo "<pre>";
  //       print_r($inv_object);
  //       exit();

		$return_result = (isset($inv_object->invoices)) ? $inv_object->invoices : array();

		return $return_result;
		// return $inv_object->invoices;
	}


	function getItemIdByCrmProductId($crm_product_id)
	{
		$params = $this->prepareParameters(array(
			'zcrm_product_id'=> $crm_product_id
		));

		$url = $this->url.'items?organization_id='.$this->organization_id.'&'.$params;
        $result = $this->get_from_books($url);
        $item_object = json_decode($result);
        $items = $item_object->items;
        $item = (count($items)>0) ? $items[0] : false;

        return $item;
	}


	function getVendorByCrmVendorName($crm_vendor_name)
	{
		$params = $this->prepareParameters(array(
			'contact_name'=>$crm_vendor_name
		));

		$url = $this->url.'contacts?organization_id='.$this->organization_id.'&'.$params;
		$result = $this->get_from_books( $url );
		$contact_object = json_decode($result);

		$contacts = $contact_object->contacts;
		$contact = (count($contacts)>0) ? $contacts[0] : false;

		return $contact;
	}


	


	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
		GET Curl
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	function get_from_books($url) {
		if($this->authToken == '' || $this->organization_id == ''){
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
		  CURLOPT_CUSTOMREQUEST => 'GET',
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



	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
		POST Functions Here
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	

	/*------------------------------------------------------------------------------
		Contact create
	--------------------------------------------------------------------------------*/
	function createContactByCRMContact($crm_contact){

		$contact_array = array(
            'contact_name'=>$crm_contact->Full_Name,
            'contact_type'=>'customer',
            'contact_persons'=> array(
            						array(

		            					'first_name'=> $crm_contact->First_Name,
		            					'last_name'=> $crm_contact->Last_Name,
		            					'email'=> $crm_contact->Email,
            						)
            					),
            );

		$jsonpost = json_encode($contact_array);
		$post['JSONString'] = $jsonpost;	
        
		$url = $this->url.'contacts?organization_id='.$this->organization_id;
		$result = $this->post_to_books( $url, $post);
		//echo "<pre>"; print_r(json_decode($result)); echo "</pre>";exit();
		return json_decode($result);
	}


	function createContact($contact_array){

		$jsonpost = json_encode($contact_array);
		$post['JSONString'] = $jsonpost;	
        
		$url = $this->url.'contacts?organization_id='.$this->organization_id;
		$result = $this->post_to_books( $url, $post);
		//echo "<pre>"; print_r(json_decode($result)); echo "</pre>";exit();
		return json_decode($result);
	}



	/*------------------------------------------------------------------------------
		Invoice create
	--------------------------------------------------------------------------------*/

	function createInvoiceInZbook($items,$post_data){
		
		$jsonpost = $this->prepareInvoiceData($items,$post_data);
		$post['JSONString'] = $jsonpost;	
        
		$url = $this->url.'invoices?organization_id='.$this->organization_id;
		$result = $this->post_to_books( $url, $post);
		//echo "<pre>"; print_r(json_decode($result)); echo "</pre>";exit();
		return json_decode($result);
	}

	function prepareInvoiceData($items,$post_data){
		$zcrm_contact_id = $post_data['crm_acc_id'];
		$book_contact = $this->getContactByZcrmAccountId($zcrm_contact_id);
				 
		$contact_id = ($book_contact) ? $book_contact->contact_id : false;
		//$contact_id = '1553548000000066011';

		$line_items = $this->prepareLineItemsForInvoice($items);        
        $due_date = date('Y-m-d');

        $book_invoice_data_array = array(
            'customer_id'=>$contact_id,
            'date'=>$due_date,
            'due_date' => $due_date,
            'is_discount_before_tax'=>true,
            'discount_type'=>'item_level',
            'is_inclusive_tax'=>false,
            // 'salesperson_name'=>'Test',
            'line_items'=>$line_items,
            'allow_partial_payments'=>true,
            'terms'=>'Terms and conditions apply.',
        );

		// echo "<pre>"; print_r($book_invoice_data_array); echo "</pre>";exit();
        $book_invoice_data_json = json_encode($book_invoice_data_array);

		return $book_invoice_data_json;
	}
	

	/*------------------------------------------------------------------------------
		Recurring Invoice create
	--------------------------------------------------------------------------------*/
	
	function createRecurringInvoiceInZbook($items,$post_data){
		
		$jsonpost = $this->prepareRecurrenceInvoiceData($items,$post_data);
		$post['JSONString'] = $jsonpost;	
        
		$url = $this->url.'recurringinvoices?organization_id='.$this->organization_id;
		$result = $this->post_to_books( $url, $post);
		// echo "<pre>"; print_r(json_decode($result)); echo "</pre>";exit();
		return json_decode($result);
	}

	function prepareRecurrenceInvoiceData($items,$post_data){

    	$zcrm_contact_id = $post_data['crm_con_id'];
		$book_contact = $this->getContactByZcrmContactId($zcrm_contact_id);
		$contact_id = ($book_contact) ? $book_contact->contact_id : false;

		// $line_items = $this->prepareLineItemsForInvoice($items); 

		$line_items[0] = array(
	                'item_id'=> '1465789000000246005',
	                'quantity'=>1,
	                'rate'=> $post_data['amount']
	            );

        $start_date = $post_data['start_date'];

        $recurrence_frequency = '';
        $repeat_every = '';
        if($post_data['frequency'] == 'Quarterly'){
			$recurrence_frequency = 'months';
        	$repeat_every = 4;
        }elseif ($post_data['frequency'] == 'Monthly') {
        	$recurrence_frequency = 'months';
        	$repeat_every = 1;
        }elseif ($post_data['frequency'] == 'Bi Weekly') {
        	$recurrence_frequency = 'weeks';
        	$repeat_every = 2;
        }elseif ($post_data['frequency'] == 'Weekly') {
        	$recurrence_frequency = 'weeks';
        	$repeat_every = 1;
        }


        $book_invoice_data_array = array(
        	'recurrence_name' => 'test recurrence - '.date('Y-m-d H:i:s'),
            'customer_id'=>$contact_id,
            "recurrence_frequency"=> $recurrence_frequency,
            "repeat_every"=> $repeat_every,
            // 'payment_terms' => 0,
            // 'payment_terms_label' => 'Due on Receipt',
            'start_date'=>$start_date,
            'line_items'=>$line_items,
        );

        $book_invoice_data_json = json_encode($book_invoice_data_array);

		// echo "<pre>"; print_r($book_invoice_data_array); echo "</pre>";exit();

		return $book_invoice_data_json;
    }

    function stopRecurringInvoiceInZbook($recurring_invoice_id){
		$post['JSONString'] = json_encode(array('recurring_invoice_id' => $recurring_invoice_id));

		$url = $this->url.'recurringinvoices/'.$recurring_invoice_id.'/status/stop?organization_id='.$this->organization_id.'&';
		$result = $this->post_to_books($url, $post);
		$result = json_decode($result);

		return $result;
    }

    /*------------------------------------------------------------------------------
		Create Payment of invoice
	--------------------------------------------------------------------------------*/
    function createPayment($post_data){

		$post['JSONString'] = json_encode($post_data);


		$url = $this->url.'customerpayments?organization_id='.$this->organization_id.'&';
		$result = $this->post_to_books($url, $post);
		$result = json_decode($result);

		return $result;



	}



	/*------------------------------------------------------------------------------
		Post Curl Here
	--------------------------------------------------------------------------------*/
	function post_to_books($url, $fields){
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
		  CURLOPT_POSTFIELDS => http_build_query($fields),
		  CURLOPT_HTTPHEADER => array(
		    "Authorization: Zoho-oauthtoken ".$authtoken,
		    "content-type: application/x-www-form-urlencoded"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		$info = curl_getinfo($curl); 
		//echo '<pre>';print_r($info);echo '</pre>'; 

		curl_close($curl);

		if ($err) {
		  return $err;
		} else {
		  return $response;
		}

	}

	/*------------------------------------------------------------------------------
		Common Functions Here
	--------------------------------------------------------------------------------*/

	function prepareLineItemsForInvoice($crm_products)
	{
		$line_items = array();
		foreach ($crm_products as $product)
		{
	
			$item = $this->getItemIdByCrmProductId($product['product']['id']);
			
			if($item){
	            $line_item = array(
	                'item_id'=> $item->item_id,
	                'quantity'=>$product['quantity'],
	                'rate'=>$product['list_price']
	            );
	          
            }

            $line_items[] = $line_item;
        }
	
        return $line_items;
	}


	// Set input parameters for the Zoho API request
    private function prepareParameters($extra = array())
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



    



   
}
