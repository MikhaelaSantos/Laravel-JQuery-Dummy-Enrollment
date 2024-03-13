<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMaintenance
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
      $start = 'role';

      // Role List Access
      if($request->is($start.'') || $request->is($start.'/getPer*')) {
        if($user->can('read-role')) {
          return $next($request);
        }
      }

      // Create Role Access
      if($request->is($start.'/create')) {
        if($user->can('create-role')) {
          return $next($request);
        }
      }

      // Update Role Access
      if($request->is($start.'/edit*')) {
        if($user->can('update-role')) {
          return $next($request);
        }
      }

      // Delte Role Access
      if($request->is($start.'/delete*') || $request->is($start.'/bulkDel*')) {
        if($user->can('delete-role')) {
          return $next($request);
        }
      }

      return abort(401, 'Unauthorized');
    }
}
