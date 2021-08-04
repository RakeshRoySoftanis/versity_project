<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Master extends Model
{
    public static function check_master_token($token){
        $master_token = DB::table('master_token')->where('token', $token)->where('expires_at', '>', date('Y-m-d H:i:s'))->first();
        return isset($master_token->id) ? $master_token : false;
    }

}