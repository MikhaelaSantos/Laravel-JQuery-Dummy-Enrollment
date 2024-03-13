<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $permissions = [
        'create-user', 'read-user', 'update-user', 'delete-user',
        'create-subject', 'read-subject', 'update-subject', 'delete-subject',
        'create-student', 'read-student', 'update-student', 'delete-student',
        'create-role', 'read-role', 'update-role', 'delete-role',
        'create-permission', 'read-permission', 'update-permission', 'delete-permission',
        'uploadfile-student', 'export-student', 'import-student', 'getUploadedFiles-student',
        'delFile-student', 'downloadFile-student'
      ];

      foreach ($permissions as $permission) {
        Permission::create([
          'name'=> $permission,
        ]);
      }
    }
}
