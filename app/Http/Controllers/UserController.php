<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DataTables; 
use Illuminate\Support\Facades\DB;
use Validator;


class UserController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $users = User::all();
    $allRoles = Role::all();
    return view('user.user', ['users' => $users, 'allRoles' => $allRoles]);
  }

  public function create()
  {
    $roles = Role::all();
    return view('user.create', ['roles' => $roles]);
  }

  public function Save(Request $request)
  {
    $data = Validator::make($request->all(), [
      'name' => 'required:',
      'email' => 'required|unique:users',
      'password' => 'required|min:8|confirmed',
    ]);

    if ($data->fails()) {
      return redirect(route('user.create'))
                  ->withErrors($data)
                  ->withInput();
    }

    $validatedData = $data->validated();
    $validatedData['password'] = Hash::make($validatedData['password']);

    $newUser = User::create($validatedData);

    if ($request->roles != null) {
      $roles = explode(',', $request->roles);
      foreach ($roles as $role) {
        $newUser->assignRole($role);
      }
    }
    return redirect(route('user.index'))->with('success', '"' . $newUser->name . '" account has been created successfully.');
  }

  public function edit(User $user)
  {
    $roles = Role::all();
    return view('user.edit', ['user' => $user, 'roles' => $roles]);
  }

  public function update(Request $request, User $user)
  {

    $data = Validator::make($request->all(), [
      'name' => 'required:',
      'email' => 'required',
      'password' => 'nullable|min:8|confirmed',
    ]);

    if ($data->fails()) {
      return redirect(route('user.edit', ['user' => $user]))
                  ->withErrors($data)
                  ->withInput();
    }

    $validatedData = $data->validated();

    if ($validatedData['password'] != null) {
      $validatedData['password'] = Hash::make($validatedData['password']);
      $user->update($validatedData);
    } else {
      $user->update([
        'name' => $validatedData['name'],
        'email' => $validatedData['email']
      ]);
    }

    if ($request->roles != null) {
      $user->syncRoles([]);
      $roles = explode(',', $request->roles);
      foreach ($roles as $role) {
        $user->assignRole($role);
      }
    }

    return redirect(route('user.index'))->with('success', '"' . $user->name . '" account has been updated successfully.');
  }

  public function delete($user)
  {
    $user = User::where('id', $user)->delete();
    return redirect(route('user.index'))->with('success', 'The user account has been deleted successfully.');
  }

  public function bulkDel($user)
  {
    $users = explode(',', $user);

    foreach ($users as $user) {
      $user = User::where('id', $user)->delete();
    }
    return redirect(route('user.index'))->with('success', 'The user accounts has been deleted successfully.');
  }

  public function bulkRole(Request $request)
  {
    $users = explode(',', $request->users);

    foreach ($users as $userId) {
      $user = User::find($userId);
      $user->syncRoles([]);
      $user->assignRole($request->role);
      if ($user) {
        $user->syncRoles([]);
        if ($request->roles != null) {
          $roles = explode(',', $request->roles);
          foreach ($roles as $role) {
            $user->assignRole($role);
          }
        }
        // $user->assignRole($request->role);
      }
    }

    return redirect(route('user.index'))->with('success', 'The user accounts has been assigned successfully.');
  }

  public function getRole($userId, $searchBox = null)
  {
    $user = User::find($userId);
    $userRoles['data'] = $user->roles;

    $roleSearch['data'] = [];

    foreach ($userRoles['data'] as $roleCont){
      $roleContent = [
        'id'=> $roleCont->id,
        'name'=> $roleCont->name
      ];

      $permissions = $roleCont->permissions;
      $perFiltered= [];
      foreach( $permissions as $permission ) {
        if( $searchBox != null ) {
          if (str_contains($permission->name, $searchBox) ) {
              array_push($perFiltered, $permission->name);
          }
        }else{
          array_push($perFiltered, $permission->name);
        }
      }
      $roleContent['permissions'] = $perFiltered;

      if(str_contains($roleCont->name, $searchBox) || !empty($perFiltered)){
        array_push($roleSearch['data'], $roleContent);
      }
    }

    // for rowgroups and defcolumns
    $rolePermission = DB::select('SELECT roles.id, roles.name AS role, permissions.name AS permission FROM ((roles INNER JOIN role_has_permissions ON roles.id = role_has_permissions.role_id) INNER JOIN permissions ON role_has_permissions.permission_id = permissions.id);');
    $allInnerJoin = DB::select('SELECT users.id, users.name AS user_name, roles.name AS role_name, permissions.name AS permission_name FROM users JOIN model_has_roles ON users.id = model_has_roles.model_id JOIN roles ON model_has_roles.role_id = roles.id JOIN role_has_permissions ON roles.id = role_has_permissions.role_id JOIN permissions ON role_has_permissions.permission_id = permissions.id WHERE users.id = ?;', [$userId]);


    return response()->json(
      ['userRoles' => $userRoles, 'roleSearch' => $roleSearch, 'searchBox' => $searchBox, 'rolePermission' => $rolePermission, 'allInnerJoin' => $allInnerJoin]
    );
  }

  public function getUserRolesPer($userId)
  {
    // Get all Roles and Permissions of the User SQL
    $allInnerJoin = DB::select('SELECT users.id, users.name AS user_name, roles.name AS role_name, permissions.name AS permission_name FROM users JOIN model_has_roles ON users.id = model_has_roles.model_id JOIN roles ON model_has_roles.role_id = roles.id JOIN role_has_permissions ON roles.id = role_has_permissions.role_id JOIN permissions ON role_has_permissions.permission_id = permissions.id WHERE users.id = ?;', [$userId]);

      return DataTables::of($allInnerJoin)
            ->make(true);
  }

  public function getSelectedRolesPer($roles)
  {
    $allSelectedRoles = [];
    $rolesIDs = explode(',', $roles);
    
    foreach ($rolesIDs as $roleId) {
      // Get all Roles and Permissions of the User SQL
      $roleJoinPer = DB::select('SELECT roles.id, roles.name AS role_name, permissions.name AS permission_name FROM roles JOIN role_has_permissions ON roles.id = role_has_permissions.role_id JOIN permissions ON role_has_permissions.permission_id = permissions.id WHERE roles.id = ?;', [$roleId]);
      foreach ($roleJoinPer as $role) {
        array_push($allSelectedRoles, $role);
      }
    }

      return DataTables::of($allSelectedRoles)
            ->make(true);

  }


}
