<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(["name" => "super_admin"]);
        Role::create(["name" => "guru"]);
        Role::create(["name" => "siswa"]);

        $admin = User::where("name", "admin")->first();
        $admin->assignRole("super_admin");

        $guru = User::where("name", "guru")->first();
        $guru->assignRole("guru");

        foreach (
            User::whereNotIn("name", ["admin", "guru"])->get()
            as $key => $user
        ) {
            $user->assignRole("siswa");
        }
    }
}
