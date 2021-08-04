<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    //
    protected $table = 'zc_accounts';
    protected $guarded = ['id'];

    public function getAccount($contact_id){
    	// return $this->where()
    }

}
