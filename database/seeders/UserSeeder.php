<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Team;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Guilherme Caraciolo',
                'email' => 'dev@test.com',
                'role_id' => 1,
                'password' => Hash::make('123'),
            ],
            [
                'name' => 'Kleryston Thiago',
                'email' => 'admin@mail.com',
                'role_id' => 2,
                'password' => Hash::make('123'),
            ],
            [
                'name' => 'Iohana Gama',
                'email' => 'iohana@test.com',
                'role_id' => 3,
                'password' => Hash::make('123'),
            ],
            [
                'name' => 'Paulo Peixoto',
                'email' => 'paulo@test.com',
                'role_id' => 3,
                'password' => Hash::make('123'),
            ],
            [
                'name' => 'Micaela Aguiar',
                'email' => 'micaela@test.com',
                'role_id' => 3,
                'password' => Hash::make('123'),
            ],
            [
                'name' => 'Anderson Túlio',
                'email' => 'anderson@test.com',
                'role_id' => 3,
                'password' => Hash::make('123'),
            ],
            [
                'name' => 'Vitória Correia',
                'email' => 'vitoria@test.com',
                'role_id' => 3,
                'password' => Hash::make('123'),
            ],
            [
                'name' => 'Steffany Vital',
                'email' => 'steffany@test.com',
                'role_id' => 3,
                'password' => Hash::make('123'),
            ],
        ]);

        Team::create([
            'user_id' => 2,
            'name' => 'Developers',
            'personal_team' => true,
        ]);
    }
}
