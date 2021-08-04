<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Schema;
use Session;
use DB;
use File;
use Hash;
use App\Nzoho;
use App\AdminUser;
use App\Accounts;

class MasterAdminUserController extends Controller
{
    
    protected $redirectTo = '/Masterhome';
    /**
     * Create a new controller instance.
     *
     * @return void
    */

    public function __construct()
    {

    }

    protected function redirectTo()
    {
        return $redirectTo;
    }

    public function userLists(){

        $data['active_menu'] = 'Users';
        $masterloging        = Session::get('getmaster');
        $adminUsers          = AdminUser::all();

        if (isset($masterloging) && !empty($masterloging)) {
            return view('master.Users.userlists',['data' => $data, 'users' => $adminUsers]);
        }else{
            return redirect('master-login');
        }

    }

    public function storeAdminUser( Request $request ){

        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:adminUsers',
            'phone'    => 'required',
            'password' => 'required'
        ]);

        $adminUser           = new AdminUser;
        $adminUser->name     = $request->name;
        $adminUser->email    = $request->email;
        $adminUser->password = Hash::make($request->password);
        $adminUser->phone    = $request->phone;

        $adminUser->save();

        return redirect()->back()->withSuccess('User created successfully.');
        
    }

    public function showAdminUser( Request $request, $id ){

        $masterloging = Session::get('getmaster');
        if (isset($masterloging) && !empty($masterloging)) {
            $data['active_menu'] = 'Users';
            $userData = AdminUser::findOrFail($id);
            return view('master.Users.edit',['dataForEdit' => $userData, 'data' => $data]);
        }else{
            return redirect('master-login');
        }
        
    }

    public function editAdminUser( Request $request, $id ){

        $masterloging = Session::get('getmaster');

        if (isset($masterloging) && !empty($masterloging)) {
            $request->validate([
                'name'     => 'required',
                'email'    => 'required|email|unique:adminUsers,email,'.$id,
                'phone'    => 'required'
            ]);

            $adminUser        = AdminUser::find($id);
            $adminUser->name  = $request->name;
            $adminUser->email = $request->email;
            if( !empty($request->password) ){
                $adminUser->password = Hash::make($request->password);
            }
            $adminUser->phone = $request->phone;
            $adminUser->save();
            return redirect('master-users')->withSuccess('User updated successfully.');
        }else{
            return redirect('master-login');
        }
    }

    public function deleteAdminUser( Request $request, $id ){

        $masterloging = Session::get('getmaster');

        if (isset($masterloging) && !empty($masterloging)) {
            $adminUser = AdminUser::findOrFail($id);
            $adminUser->delete();
            return redirect('master-users')->withSuccess('User deleted successfully.');
        }else{
            return redirect('master-login');
        }
    }

    public function assignAccount( $id ){

        $masterloging = Session::get('getmaster');

        if (isset($masterloging) && !empty($masterloging)) {
            $data['active_menu'] = 'Users';
            $accounts = Accounts::all();
            $userData = AdminUser::findOrFail($id);
            $assignedAccounts = json_decode($userData->assignedAccounts);
            if( is_null($assignedAccounts) ){
                $assignedAccounts = array();  
            }
            return view('master.Users.assignAccounts',['data' => $data,'accounts' => $accounts,'userData' => $userData, 'alreadyAssigned' => $assignedAccounts]);
        }else{
            return redirect('master-login');
        }

    }

    public function submitAssignAccount( Request $request, $id ){

        $masterloging        = Session::get('getmaster');

        if (isset($masterloging) && !empty($masterloging)) {
            $data['active_menu'] = 'Users';

            $assignedAccounts = json_encode($request->accounts);

            $adminUser = AdminUser::findOrFail($id);
            $adminUser->assignedAccounts = ( $assignedAccounts == "null" ) ? NULL:$assignedAccounts;
            $adminUser->save();

            return redirect('master-users')->withSuccess('Account Assigned Successfully.');
        }else{
            return redirect('master-login');
        }

    }

}