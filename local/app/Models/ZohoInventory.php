<?php
/**
*** @22/04/2020 by Softanis
*** @sumon.softanis@gmail.com
*** @tutorial: Zoho Inventory API management class
*** @method:https://www.zoho.com/inventory/api/v1/#introduction
**/
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DOMDocument;
use DB;
class ZohoInventory 
{
	protected $authToken;
	protected $organization_id;
    public $scope = 'ZohoInventory/inventoryapi';
    public $url = 'https://inventory.zoho.com/api/v1/';

    public function __construct()
    {
     	$auth_data = DB::table('zoho_inventory_auth')->orderBy('id', 'desc')->first();		   
		
		$this->authToken = $auth_data->access_token;   
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

			if(isset($token_dataArr->access_token)){
				$this->authToken = $token_dataArr->access_token;

				$insArr['access_token'] = $token_dataArr->access_token;
				$insArr['authorized_client_name'] = $auth_data->authorized_client_name;
				$insArr['code'] = $auth_data->code;
				$insArr['create_time'] = new \DateTime();

				DB::table('zoho_inventory_auth')->insert($insArr); 
			}
		}
    }

    /*
	* Description: CURL Request to regenerate access token
	* Parameters:  $data = array of post data
	* Returns: An Object(Access Token, Time etc.)
	*/
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
		$url = $this->url.$module.'/'.$Id.'?organization_id='.$this->organization_id.'&';
		$result = $this->get_from_inventory($url);
		return $result;
	}

	function getRecords($module, $page=1){
		$url = $this->url.$module.'/?organization_id='.$this->organization_id.'&page='.$page;
		return $result = $this->get_from_inventory($url);
	}



	/*------------------------------------------------------------------------------
	--------------------------------------------------------------------------------
		GET Curl
	--------------------------------------------------------------------------------
	--------------------------------------------------------------------------------*/
	function get_from_inventory($url) {
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









	function createContactInv($cntArray='')
	{
		$data = array(
    	  'JSONString' => json_encode($cntArray)
    	);
		$url = $this->url.'contacts';
        $result = $this->doRequest($url,'POST',$cond=array(),$data);
        return $result;
	}

	function insertToInventory($cntArray='',$module='')
	{
		$data = array(
    		'JSONString' => json_encode($cntArray)
    	);

		$url = $this->url.$module;
        $result = $this->doRequest($url,'POST',$cond=array(),$data);
        return $result;
	}

	// Get List
	function getDataById($invtid='',$module='')
	{
		$url = $this->url.$module.'/'.$invtid;

        $result = $this->doRequest($url,'GET','','');
        $result = json_decode($result);

        return $result;
	}

	// Price list
	function getPricelist($getpricelistid=''){
		$url = $this->url.'pricebooks/';
		$result = $this->doRequest($url,'GET','','');
		$pricelist = json_decode($result);
		$return_result = (isset($pricelist->pricebooks)) ? $pricelist->pricebooks : array();
		return $return_result;
	}	

	function getPricelistDetails($getpricelistid=''){
		$url = $this->url.'pricebooks/'.$getpricelistid;
		$result = $this->doRequest($url,'GET','','');
		$pricelist = json_decode($result);
		$return_result = (isset($pricelist->pricebook->pricebook_items)) ? $pricelist->pricebook->pricebook_items : array();
		return $return_result;
	}

	//Get all adujtment 
	public function getAllAdj($value='')
	{
		$url = $this->url.'inventoryadjustments/';
		$result = $this->doRequest($url,'GET','','');
		$adjustment = json_decode($result);
		$return_result = (isset($adjustment->inventory_adjustments)) ? $adjustment->inventory_adjustments : array();
		return $return_result;
	}

	// Get adjustment by id
	function getInvAdjustmentByID($InvAdjustID=''){
		$url = $this->url.'inventoryadjustments/'.$InvAdjustID;
		$result = $this->doRequest($url,'GET','','');
		$adjustment = json_decode($result);
		$return_result = (isset($adjustment->inventory_adjustment)) ? $adjustment->inventory_adjustment : array();
		return $return_result;
	}

	//Warehouse list
	public function getWareHouselist($value='')
	{
		$url = $this->url.'settings/warehouses/';
		$result = $this->doRequest($url,'GET','','');
		$warehouselist = json_decode($result);
		$return_result = (isset($warehouselist->warehouses)) ? $warehouselist->warehouses : array();
		return $return_result;
	}

	public function getProductList()
	{
		$url = $this->url.'items/';
		$result = $this->doRequest($url,'GET','','');
		$productlist = json_decode($result);
		$return_result = (isset($productlist->items)) ? $productlist->items : array();
		return $return_result;
	}	

	public function getWarehouseStockReports()
	{
		$url = $this->url.'reports/warehouse/';
		$result = $this->doRequest($url,'GET','','');
		$productstock = json_decode($result);
		$return_result = (isset($productstock->warehouse_stock_info)) ? $productstock->warehouse_stock_info : array();
		return $return_result;
	}	

	public function getBatchReport()
	{
		$getdata = array(
			// 'ignore_empty_batches' => 'false', 
			// 'page' => '1', 
			'to_date' 	=> date('Y-m-d'),
			'from_date' => '2018-01-01',
			// 'filter_by' => 'TransactionDate.ThisYear', 
			'filter_by' => 'TransactionDate.CustomDate', 
		);

		$url = $this->url.'reports/batchdetails';
		$result = $this->doRequest($url,'GET',$getdata);
		$batchdetails = json_decode($result);
		$return_result = (isset($batchdetails->batch_details)) ? $batchdetails->batch_details : array();
		return $return_result;
	}

	public function getItemImage($item_id)
	{
		$url = $this->url.'items/'.$item_id.'/image';
		return $result = $this->doRequest($url,'GET');
	}

	function doRequest($url, $method='', $cond = array(),$data = '') {

		$cdata = $cond;

		if($this->authToken == '' || $this->organization_id == ''){
			return false; 
		}

		$params_auth = array(
			'authtoken' => $this->authToken,
			'organization_id' => $this->organization_id
		 );

		if($method == 'POST'){
			$cdata = $data;
			$params = http_build_query(array_merge($params_auth,$cdata));
		}else{
			$params = http_build_query(array_merge($params_auth));
		}

		if (!empty($cdata)) {
		    $params = http_build_query(array_merge($params_auth,$cdata));
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url.'?'.$params,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => $method,
		  CURLOPT_POSTFIELDS => $data,
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "postman-token: d6751468-3402-6923-2fcb-de6f13c32578"
		  ),
		));

		$response = curl_exec($curl);

		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  return $response;
		}
	}
}