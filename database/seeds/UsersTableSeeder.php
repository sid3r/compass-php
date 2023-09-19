<?php

use Illuminate\Database\Seeder;
use App\Laravue\Acl;
use App\Laravue\Models\User;
use App\Laravue\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // admin
        $admin = User::create([
          'name' => 'Admin',
          'email' => 'admin@compass.dev',
          'password' => Hash::make('compass'),
        ]);
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@compass.dev',
            'password' => Hash::make('compass'),
        ]);
        $adminRole = Role::findByName(\App\Laravue\Acl::ROLE_ADMIN);
        $managerRole = Role::findByName(\App\Laravue\Acl::ROLE_MANAGER);
        $admin->syncRoles($adminRole);
        $manager->syncRoles($managerRole);

        // random user role
        $userList = [
            "Yacine A",
            "Kamel H",
            "Idir B",
            "Massinissa S",
            "Laila I",
        ];

        foreach ($userList as $fullName) {
            $name = str_replace(' ', '.', $fullName);
            $roleName = \App\Laravue\Faker::randomInArray([
                Acl::ROLE_MANAGER,
                Acl::ROLE_VENDOR,
            ]);
            $user = \App\Laravue\Models\User::create([
                'name' => $fullName,
                'email' => strtolower($name) . '@compass.dev',
                'password' => \Illuminate\Support\Facades\Hash::make('compass'),
            ]);

            $role = Role::findByName($roleName);
            $user->syncRoles($role);
        }
    }
}
