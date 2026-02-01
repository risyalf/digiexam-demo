<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(UserFactory $userFactory): void
    {
        User::firstorcreate([
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'password' => 'admin'
        ]);

        User::firstorcreate([
            'name' => 'guru',
            'email' => 'guru@mail.com',
            'password' => 'guru'
        ]);

        for ($i=0; $i < 20; $i++) {
            User::firstorcreate([
                ...$userFactory->definition(),
                'password' => 'siswa'
            ]);
        }
    }
}
