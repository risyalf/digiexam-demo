<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstorcreate([
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'password' => 'admin'
        ]);

        User::firstorcreate([
            'name' => 'siswa',
            'email' => 'siswa@mail.com',
            'password' => 'siswa'
        ]);
    }
}
