<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BooteSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'Seerose 1', 'typ' => 'Ruderboot', 'plaetze' => 2, 'status' => 'verfuegbar'],
            ['name' => 'Seerose 2', 'typ' => 'Tretboot', 'plaetze' => 4, 'status' => 'verfuegbar'],
            ['name' => 'Hecht',     'typ' => 'Motorboot', 'plaetze' => 5, 'status' => 'gesperrt'],
            ['name' => 'Barsch',    'typ' => 'Ruderboot', 'plaetze' => 2, 'status' => 'wartung'],
            ['name' => 'Karpfen',   'typ' => 'Tretboot',  'plaetze' => 4, 'status' => 'verfuegbar'],
        ];

        $this->db->table('boote')->insertBatch($data);
    }
}