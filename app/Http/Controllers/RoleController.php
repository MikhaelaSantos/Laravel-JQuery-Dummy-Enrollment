<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use DataTables; 
use Validator;

class RoleController extends Controller
{
  public function index(){
    $roles = Role::all();
    $assignedPermissions = DB::select('SELECT roles.id, roles.name, permissions.name AS permission_name  FROM roles JOIN role_has_permissions ON roles.id = role_has_permissions.role_id JOIN permissions ON role_has_permissions.permission_id = permissions.id;');
    return view('role.role', ['roles'=>$roles, 'assignedPermissions' => $assignedPermissions]);
  }

  public function create(){
    $permissions = Permission::all();
    return view('role.create', ['permissions'=>$permissions]);
  }

  public function save(Request $request){
    $data = Validator::make($request->all(), [
      'name' => 'required|unique:roles'
    ]);

    if ($data->fails()) {
      return redirect(route('role.create'))
                  ->withErrors($data)
                  ->withInput();
    }

    $newRole = Role::create($data->validated());

    $permissions = explode(',',$request->permissions);

    for($x=0; $x<count($permissions); $x++){
      $newRole->givePermissionTo($permissions[$x]);
    }

    return redirect(route('role.index'))->with('success', 'The role "'.$newRole->name.'" has been created successfully.');
  }

  public function edit(Role $role){
    $permissions = Permission::all();
    $assignedPermissions = DB::select('SELECT * FROM roles INNER JOIN role_has_permissions ON roles.id = role_has_permissions.role_id WHERE role_id = ?', [$role->id]);
    return view('role.edit', ['role'=>$role, 'permissions' => $permissions, 'assignedPermissions' => $assignedPermissions]);
  }

  public function update(Request $request, Role $role){
    $data = Validator::make($request->all(), [
      'name' => 'required'
    ]);

    if ($data->fails()) {
      return redirect(route('role.edit', ['role' => $role]))
          ->withErrors($data)
          ->withInput();
    }

    $role->update($data->validated());

    // Remove all permission to roles
    $deletePermission = DB::table('role_has_permissions')->where('role_id', '=', $role->id)->delete();

    $permissions = explode(',',$request->permissions);

    for($x=0; $x<count($permissions); $x++){
      $role->givePermissionTo($permissions[$x]);
    }

    return redirect(route('role.index'))->with('success', 'The role "'.$role->name.'" has been updated successfully.');
  }

  public function delete($role){
    $role=Role::where('id',$role)->delete();
    return redirect(route('role.index'))->with('success', 'The role has been deleted successfully.');
  }

  public function bulkDel($role){
    $roles = explode(',', $role);

    foreach($roles as $role){
      $role=Role::where('id',$role)->delete();
    }
    return redirect(route('role.index'))->with('success', 'The roles has been deleted successfully.');
  }

  public function getPer($roleID){

    $permissions = Role::find($roleID)->permissions;

    return Datatables::of($permissions)->make(true);
  }
}
