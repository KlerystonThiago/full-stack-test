<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('customers')->insert([
            [
                'name' => 'Nexa Corporate',
                'email' => 'nexa@test.com',
                'document' => '05337332430',
                'phone' => '(81) 99400-3340',
                'address' => 'Rua da Várzea 53, João Pessoa - Paraíba',
                'team_id' => 1,
                'created_at' => '2026-01-16 04:37:33',
            ],
            [
                'name' => 'Tech Solutions Ltda',
                'email' => 'contato@techsolutions.com.br',
                'document' => '12345678000190',
                'phone' => '(11) 98765-4321',
                'address' => 'Av. Paulista, 1500 - São Paulo - SP',
                'team_id' => 1,
                'created_at' => '2026-01-16 04:37:33',
            ],
            [
                'name' => 'João Silva Santos',
                'email' => 'joao.silva@email.com',
                'document' => '12345678901',
                'phone' => '(21) 97654-3210',
                'address' => 'Rua das Flores, 123 - Rio de Janeiro - RJ',
                'team_id' => 1,
                'created_at' => '2026-01-16 04:37:33',
            ],
            [
                'name' => 'Maria Oliveira Costa',
                'email' => 'maria.oliveira@gmail.com',
                'document' => '98765432109',
                'phone' => '(85) 99876-5432',
                'address' => 'Av. Beira Mar, 456 - Fortaleza - CE',
                'team_id' => 1,
                'created_at' => '2026-01-16 04:37:33',
            ],
            [
                'name' => 'GameStore Digital',
                'email' => 'vendas@gamestore.com.br',
                'document' => '23456789000123',
                'phone' => '(11) 3456-7890',
                'address' => 'Rua dos Gamers, 789 - São Paulo - SP',
                'team_id' => 1,
                'created_at' => '2026-01-16 04:37:33',
            ],
            [
                'name' => 'Pedro Henrique Almeida',
                'email' => 'pedro.almeida@hotmail.com',
                'document' => '45678912345',
                'phone' => '(31) 99123-4567',
                'address' => 'Rua Amazonas, 321 - Belo Horizonte - MG',
                'team_id' => 1,
                'created_at' => '2026-01-16 04:37:33',
            ],
            [
                'name' => 'Ana Carolina Ferreira',
                'email' => 'ana.ferreira@outlook.com',
                'document' => '78912345678',
                'phone' => '(41) 98234-5678',
                'address' => 'Av. Marechal Deodoro, 654 - Curitiba - PR',
                'team_id' => 1,
                'created_at' => '2026-01-16 04:37:33',
            ],
            [
                'name' => 'Mega Hardware Distribuidora',
                'email' => 'comercial@megahardware.com.br',
                'document' => '34567890000145',
                'phone' => '(48) 3234-5678',
                'address' => 'Rod. SC-401, 1200 - Florianópolis - SC',
                'team_id' => 1,
                'created_at' => '2026-01-16 04:37:33',
            ],
            [
                'name' => 'Carlos Eduardo Souza',
                'email' => 'carlos.souza@yahoo.com.br',
                'document' => '32165498732',
                'phone' => '(71) 99345-6789',
                'address' => 'Rua Chile, 88 - Salvador - BA',
                'team_id' => 1,
                'created_at' => '2026-01-16 04:37:33',
            ],
            [
                'name' => 'Juliana Martins Rocha',
                'email' => 'ju.martins@gmail.com',
                'document' => '65432187965',
                'phone' => '(61) 98456-7890',
                'address' => 'SQN 210, Bloco F - Brasília - DF',
                'team_id' => 1,
                'created_at' => '2026-01-16 04:37:33',
            ],
            [
                'name' => 'InfoTech Sistemas Ltda',
                'email' => 'sistemas@infotech.com.br',
                'document' => '45678901000167',
                'phone' => '(51) 3567-8901',
                'address' => 'Av. Ipiranga, 2300 - Porto Alegre - RS',
                'team_id' => 1,
                'created_at' => '2026-01-16 04:37:33',
            ],
            [
                'name' => 'Rafael Santos Lima',
                'email' => 'rafael.lima@proton.me',
                'document' => '85274196385',
                'phone' => '(92) 99567-8901',
                'address' => 'Av. Eduardo Ribeiro, 950 - Manaus - AM',
                'team_id' => 1,
                'created_at' => '2026-01-16 04:37:33',
            ],
        ]);

    }
}
