<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Validator;

class PermissionController extends Controller
{
  
  public function index(){
    $permissions = Permission::all();
    return view('permission.permission', ['permissions'=>$permissions]);
  }

  public function create(){
    return view('permission.create');
  }

  public function save(Request $request){
    $data = Validator::make($request->all(), [
      'name' => 'required|unique:permissions'
    ]);

    if ($data->fails()) {
      return redirect(route('permission.create'))
                  ->withErrors($data)
                  ->withInput();
    }

    $newPermission = Permission::create($data->validated());

    // Setting the New Permission to the Administrator Role
    $role = Role::find(1);
    $role->givePermissionTo($newPermission);
    
    return redirect(route('permission.index'))->with('success', 'The permission "'.$newPermission->name.'" has been created successfully.');
  }

  public function edit(Permission $permission){
    return view('permission.edit', ['permission'=>$permission]);
  }

  public function update(Request $request, Permission $permission){
    $data = Validator::make($request->all(), [
      'name' => 'required|unique:permissions'
    ]);

    if ($data->fails()) {
      return redirect(route('permission.edit', ['permission'=>$permission]))
                  ->withErrors($data)
                  ->withInput();
    }

    $permission->update($data->validated());

    return redirect(route('permission.index'))->with('success', 'The permission "'.$permission->name.'" has been created successfully.');
  }

  public function delete($permission){
    $permission=Permission::where('id',$permission)->delete();
    return redirect(route('permission.index'))->with('success', 'The permission has been deleted successfully.');
  }

  public function bulkDel($permission){
    $permissions = explode(',', $permission);

    foreach($permissions as $permission){
      $permission=Permission::where('id',$permission)->delete();
    }
    return redirect(route('permission.index'))->with('success', 'The permissions has been deleted successfully.');
  }
}
