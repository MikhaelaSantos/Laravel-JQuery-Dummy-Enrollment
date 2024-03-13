<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMaintenance
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
      $start = 'permission';

      // Permimssion List Access
      if($request->is($start.'')) {
        if($user->can('read-permission')) {
          return $next($request);
        }
      }

      // Create Permimssion Access
      if($request->is($start.'/create')) {
        if($user->can('create-permission')) {
          return $next($request);
        }
      }

      // Update Permimssion Access
      if($request->is($start.'/edit*')) {
        if($user->can('update-permission')) {
          return $next($request);
        }
      }

      // Delete Permimssion Access
      if($request->is($start.'/delete*') || $request->is($start.'/bulkDel*')) {
        if($user->can('delete-permission')) {
          return $next($request);
        }
      }

      return abort(401, 'Unauthorized');
    }
}
