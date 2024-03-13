<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentMaintenance
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
      $preStud = 'student';
      $preEnroll = 'enrollment';

      // Student List Access
      if($request->is($preStud.'') || $request->is($preEnroll.'') || $request->is($preEnroll.'/getSub')) {
        if($user->can('read-student')) {
          return $next($request);
        }
      }

      // Read Students in Subject List
      if($request->is($preEnroll.'*') || $request->is($preStud.'/fileList*')) {
        if($user->can('read-subject')) {
          return $next($request);
        }
      }

      // Create Student Acces
      if($request->is($preStud.'/create') || $request->is($preEnroll.'/create*') || $request->is($preEnroll.'/enroll')) {
        if($user->can('create-student')) {
          return $next($request);
        }
      }

      // Update Student Acces
      if($request->is($preStud.'/edit*') || $request->is($preEnroll.'/edit*')) {
        if($user->can('update-student')) {
          return $next($request);
        }
      }

      // DeleteStudent Acces
      if($request->is($preStud.'/delete*') || $request->is($preStud.'/bulkDel*')) {
        if($user->can('delete-student')) {
          return $next($request);
        }
      }

      // Export Student Acces
      if($request->is($preStud.'/export')) {
        if($user->can('export-student')) {
          return $next($request);
        }
      }

      // Import Student Access
      if($request->is($preStud.'/import') || $request->is($preStud.'/saveSessionData') || $request->is($preStud.'/deleteSessions')) {
        if($user->can('import-student')) {
          return $next($request);
        }
      }

    // Student File List Maintenance
      // Upload File Student Access
      if($request->is($preStud.'/uploadFile*')) {
        if($user->can('uploadfile-student')) {
          return $next($request);
        }
      }
      
      // Get Uploaded Files
      if($request->is($preStud.'/getUploadedFiles*')) {
        if($user->can('getUploadedFiles-student')) {
          return $next($request);
        }
      }

      // Delete Uploaded File
      if($request->is($preStud.'/delFile*') || $request->is($preStud.'/bulkFileDel*')) {
        if($user->can('delFile-student')) {
          return $next($request);
        }
      }

      // Download File
      if($request->is($preStud.'/downloadFile*')) {
        if($user->can('downloadFile-student')) {
          return $next($request);
        }
      }

      return abort(401, 'Unauthorized');
    }
}
