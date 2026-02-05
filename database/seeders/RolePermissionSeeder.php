<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (["super_admin", "guru", "siswa"] as $name) {
            Role::firstOrCreate(["name" => $name]);
        }

        $roles = Role::pluck("uuid", "name");

        $adminId = User::where("name", "admin")->value("id");
        $guruId = User::where("name", "guru")->value("id");

        $siswaIds = User::whereNotIn("name", ["admin", "guru"])->pluck("id");

        $rows = [];

        if ($adminId) {
            $rows[] = [
                "role_id" => $roles["super_admin"],
                "model_type" => User::class,
                "model_uuid" => $adminId,
            ];
        }

        if ($guruId) {
            $rows[] = [
                "role_id" => $roles["guru"],
                "model_type" => User::class,
                "model_uuid" => $guruId,
            ];
        }

        foreach ($siswaIds as $id) {
            $rows[] = [
                "role_id" => $roles["siswa"],
                "model_type" => User::class,
                "model_uuid" => $id,
            ];
        }

        DB::table("model_has_roles")->insertOrIgnore($rows);
    }
}
