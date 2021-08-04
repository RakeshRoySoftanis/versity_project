<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
// use App\Classes\Zoho;

class Contacts extends Model
{
    //
    protected $table = 'zc_contacts';
    protected $guarded = ['id'];

    public function getContact(){
        $contact_id = Auth::user()->contact_id;
        return $this->where('module_id',$contact_id)->first();
    }

    public function getAccountName(){
        $contact_id = Auth::user()->contact_id;
        $contact = $this->where('module_id',$contact_id)->first();
        return $contact->Account_Name;
    }

    public function getLoggedClientInfo(){
    	$return = $this->where('module_id',Auth::user()->contact_id)->first();
        // var_dump($return);
    	return $return;
    }

    public function updateContact($post){
    	try {
            $contact_id = Auth::user()->contact_id;
            $data = [];
            $ignoreArr = ['_token'];
            foreach ($_POST as $key => $value) {
                if ( !in_array($key,$ignoreArr) ){
                    $data[$key] = $value;
                }
            }
            $zoho = New Zoho;
            $updateZoho = $zoho->updateRecord('Contacts',$contact_id,$data);
            // var_dump($data);
            $updateContact = $this->updateOrCreate(['module_id' => $contact_id],$data);
            $result = $updateContact;
        } catch (\Illuminate\Database\QueryException $e){
            $result = $e;
        }
        // var_dump($result);
        return $result;
    }

}
