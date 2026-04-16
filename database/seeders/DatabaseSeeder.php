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

        // 1. Roles first
        $this->call(RoleSeeder::class);

        // 2. Admin user (patients need created_by FK)
        $adminRoleId = Role::where('name', 'Admin')->value('id');
        User::updateOrCreate(
            ['email' => 'nus.ahamed@gmail.com'],
            [
                'name'     => 'Nusair',
                'role_id'  => $adminRoleId,
                'password' => Hash::make('12344321'),
            ]
        );

        // 3. Everything else
        $this->call([
            TestCategorySeeder::class,
            TestSeeder::class,
            TestGroupSeeder::class,
            PatientSeeder::class,
        ]);
    }
}
