<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    { 
        $user = Auth::user();

        $start = 'user';
        
        // User List
        if($request->is($start.'') || $request->is($start.'/getUserRolesPer*')){
          if ($user->can('read-user')) {
              return $next($request);
          }
        }

        // Create User Access 
        if ($request->is($start.'/create') || $request->is($start.'/getSelectedRolesPer*')) {
            if ($user->can('create-user')) {
                return $next($request);
            }
        }

        // Update User Access 
        if ($request->is($start.'/edit*') || $request->is($start.'/bulkRole') || $request->is($start.'/getSelectedRolesPer*')) {
          if ($user->can('update-user')) {
              return $next($request);
          }
        }

        // Delete User Access 
        if ($request->is($start.'/delete*') || $request->is($start.'/bulkDel*')) {
          if ($user->can('delete-user')) {
              return $next($request);
          }
        }

        
        return abort(401, 'Unauthorized');
        

    }
}
