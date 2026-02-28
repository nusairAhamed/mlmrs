<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

         $this->call(RoleSeeder::class);

             $adminRoleId = Role::where('name', 'Admin')->value('id');


        User::updateOrCreate(
        ['email' => 'admin@mlmrs.test'],
        [
            'name' => 'Admin',
            'role_id' => $adminRoleId,
            'password' => Hash::make('Password@123'),
        ]
    );
    }
}
