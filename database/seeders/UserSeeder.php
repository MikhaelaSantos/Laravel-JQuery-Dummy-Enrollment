<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $user = User::create([
        'name' => 'Administrator',
        'email' => 'admin@admin.com',
        'password' => bcrypt('admin123'),
      ]);

      $role = Role::findByName('Administrator');
      $user->assignRole($role);
    }
}
