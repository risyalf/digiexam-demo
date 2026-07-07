<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission as ModelsPermission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
        ]);

        foreach (["guru", "siswa"] as $name) {
            Role::firstOrCreate(["name" => $name]);
        }

        $roles = Role::pluck("uuid", "name");

        $admin = User::where("email", "admin@mail.com")->first();

        $rows = [];

        if ($admin) {
            $rows[] = [
                "role_id" => $roles["super_admin"],
                "model_type" => User::class,
                "model_uuid" => $admin->id,
            ];
        }

        DB::table("model_has_roles")->insertOrIgnore($rows);

        $permissions = Permission::all();

        $superAdmin->syncPermissions($permissions);
        $admin->assignRole($superAdmin);
    }
}
