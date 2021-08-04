<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hash;
use App\Models\Accounts;
use App\Models\Contacts;
use Session;

class AdminUser extends Authenticatable
{
	use SoftDeletes;

    protected $table = 'admin_users';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
        
    protected $guarded = ['id','deleted_at'];

    public static function getUserAuth( $email, $password ){

        $email = trim($email);
        $user = self::where('email',$email)
            ->whereNull('deleted_at')
            ->first();

        if( is_null($user) ){
            return false;
        }else if (!Hash::check($password, $user->password)) {
            return false;
        }

        return $user;

    }

    public static function getAssociatedAccount(){
        $asscoAccounts = self::getUser();
        if( !is_null($asscoAccounts->assignedAccounts) ){
            $accounts = Accounts::whereIn('module_id',json_decode($asscoAccounts->assignedAccounts))
                                    ->get();
            return $accounts;
        }
        return null;
    }

    public static function getUser(){

        $masterloging = Session::get('getmasteradmin');

        $user = self::where('id',$masterloging['id'])
            ->first();

        return $user;

    }

    public static function getContactsByAccount( $module_id ){
        $contacts = Contacts::where("Account_Name_ID",$module_id)
                            ->get();
        return $contacts;
    }
}