<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectMaintenance
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

      $start = 'subject';
      
      // User List
      if($request->is($start.'')) {
        if ($user->can('read-subject')) {
            return $next($request);
        }
      }

      // Create User Access 
      if ($request->is($start.'/create')) {
          if ($user->can('create-subject')) {
              return $next($request);
          }
      }

      // Update User Access 
      if ($request->is($start.'/edit*')) {
        if ($user->can('update-subject')) {
            return $next($request);
        }
      }

      // Delete User Access 
      if ($request->is($start.'/delete*') || $request->is($start.'/bulkDel*')) {
        if ($user->can('delete-subject')) {
            return $next($request);
        }
      }

      
      return abort(401, 'Unauthorized');
    }
}
