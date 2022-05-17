<?php

namespace App\Http\Controllers;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Validator;
use DataTables;
use DB;
class PermissionManageController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
      $this->middleware('role:Super-Admin');
    }
    public function CreateRoleForm(){
    	if (request()->ajax()) {
        $total_bal=500;
        $get=Role::select('id','name')->get();
        return DataTables::of($get)
          ->addIndexColumn()
          ->addColumn('action',function($get){
          $button  ='<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="'.$get->id.'" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="'.$get->id.'"><i class="fas fa-trash-alt"></i></button>
                    </div>';
        return $button;
      })
      ->rawColumns(['action'])->make(true);
        }
    	return view('pages.permission.create_role');
    }
    public function CreateRole(Request $r){
    	$validator=Validator::make($r->all(),[
    		'name'=>'required|max:100|min:1',
    	]);
    	if ($validator->passes()) {
    		$role=Role::Create(['name'=>$r->name]);
		    	if ($role) {
		    		return response()->json(['message'=>'Role Added Success']);
		    	}
    	}
    	return response()->json([$validator->getMessageBag()]);
    }
    public function CreatePermissionForm(){
    	if (request()->ajax()) {
        $get=Permission::select('id','name')->get();
        return DataTables::of($get)
          ->addIndexColumn()
          ->addColumn('action',function($get){
          $button  ='<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="'.$get->id.'" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="'.$get->id.'"><i class="fas fa-trash-alt"></i></button>
                    </div>';
        return $button;
      })
      ->rawColumns(['action'])->make(true);
        }
       return view('pages.permission.create_permission');
    }
    public function CreatePermission(Request $r){
    	$validator=Validator::make($r->all(),[
    		'name'=>'required|max:100|min:1',
    	]);
    	if ($validator->passes()) {
    		$role=Permission::Create(['name'=>$r->name]);
		    	if ($role) {
		    		return response()->json(['message'=>'Permission Added Success']);
		    	}
    	}
    	return response()->json([$validator->getMessageBag()]);
    }
    public function roleHasPermissionForm(){
      $permission=Permission::select('id','name')->get();
      $role=Role::select('id','name')->get();
      return view('pages.permission.set_role_has_permission',compact('permission','role'));
    }
    public function setRoleHasPermission(Request $r){
      // return $r->role;
       $validator=Validator::make($r->all(),[
          'role'=>'required|array',
          'role.*'=>'required|max:50',
          'permission'=>'required|array',
          'permission'=>'required|max:100|min:1',
       ]);
       if ($validator->passes()) {
           for ($i=0; $i <count($r->role) ; $i++) {
              $role=Role::findById($r->role[$i]['id']);
              for ($i2=0; $i2 <count($r->permission) ; $i2++) { 
                 if ($r->array[$i2][$i]=='on') {
                   $permission=Permission::findById($r->permission[$i2]['id']);
                   $role->givePermissionTO($permission);
                 }elseif ($r->array[$i2][$i]=='off') {
                   $permission=Permission::findById($r->permission[$i2]['id']);
                   $permission->removeRole($role);
                 }
              }
           }
           return response()->json(['message'=>'Permission Assign Success']);
       }
       return response()->json($validator->getMessageBag());
    }
    public function getRoleHasPermission(){
       $data=DB::table('role_has_permissions')
              ->join('permissions','permissions.id','=','role_has_permissions.permission_id')
              ->select('permissions.name','role_has_permissions.role_id')
              ->get();
       return response()->json($data);
    }
    public function userWiseRoleForm(){
      if (request()->ajax()) {
        $get=DB::table('model_has_roles')
             ->join('users','model_has_roles.model_id','=','users.id')
             ->join('roles','roles.id','=','model_has_roles.role_id')
             ->select('users.id','users.name as username','roles.name as role_name')
             ->get();
        return DataTables::of($get)
          ->addIndexColumn()
          ->addColumn('action',function($get){
          $button  ='<div class="btn-group btn-group-toggle" data-toggle="buttons">
                       <button type="button" data-id="'.$get->id.'" class="btn btn-sm btn-primary rounded mr-1 edit" data-toggle="modal" data-target=""><i class="fas fa-eye"></i></button>
                       <button class="btn btn-danger btn-sm rounded delete" data-id="'.$get->id.'"><i class="fas fa-trash-alt"></i></button>
                    </div>';
        return $button;
      })
      ->rawColumns(['action'])->make(true);
        }
        $role=Role::select('id','name')->get();
        $user=User::select('id','name')->get();
        return view('pages.permission.user_wise_role',compact('role','user'));
    }
    public function userWiseRole(Request $r){
      // return $r->all();
      $validator=Validator::make($r->all(),[
        'role'=>'required|max:100|min:1',
        'user'=>'required|max:10|min:1',
      ]);
      if ($validator->passes()) {
        $role=Role::select('name')->get();
        foreach($role as $roles){
          $allRoles[]=$roles->name;
        }
        $user=User::find($r->user);

        for ($i=0; $i<count($allRoles); $i++) { 
          if ($user->hasRole($allRoles[$i])) {
            $role=Role::findByName($allRoles[$i]);
            $user->removeRole($allRoles[$i]);
          }
        }
        $user->assignRole(strval($r->role));
        return response()->json(['message'=>'Role Assign Success']);
      }
     return response()->json($validator->getMessageBag());
    }
}
