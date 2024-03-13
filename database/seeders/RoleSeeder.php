<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $role = Role::create([
        'name'=> 'Administrator',
      ]);

      $allPermissions = Permission::all(); //Get All Permission 
      $role->permissions()->sync($allPermissions); //Storing all permision to Administrator/New Role

    }
}
