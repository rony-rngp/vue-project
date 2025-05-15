<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Organization;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //$this->call(AdminSeeder::class);

        Organization::updateOrCreate(
            ['name' => 'Organization 1'],
        );

        //create super admin
        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'user_type' => 'super_admin',
                'phone' => '01792702312',
                'password' => bcrypt('11111111'),
                'organization_id' => null, // selected from UI
                'created_by' => null, // super_admin
            ]
        );

        //super admin create admin
        $super_admin = User::find(1);

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'user_type' => 'admin',
                'phone' => '01000000000',
                'password' => bcrypt('11111111'),
                'organization_id' => 1, // selected from UI
                'created_by' => $super_admin->id, // super_admin
            ]
        );

        //admin create user
        $admin = User::find(2);
        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User',
                'user_type' => 'user',
                'phone' => '0192828282',
                'password' => bcrypt('11111111'),
                'organization_id' => $admin->organization_id, // selected from UI
                'created_by' => $admin->id, // super_admin
            ]
        );
    }
}
