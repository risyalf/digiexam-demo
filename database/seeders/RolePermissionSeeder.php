<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'siswa']);

        $admin = User::where('name', 'admin')->first();

        $admin->assignRole('admin');

        foreach (User::where('name', '!=', 'admin')->get() as $key => $user) {
            $user->assignRole('siswa');
        }

        
    }
}
