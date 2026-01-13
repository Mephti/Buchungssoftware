<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LiegeplaetzeSeeder extends Seeder
{
    public function run()
    {
        $data = [];

        // Anleger A: Plätze 1-12
        for ($i = 1; $i <= 12; $i++) {
            $data[] = [
                'anleger' => 'A',
                'nummer'  => $i,
                'status'  => 'verfuegbar',
            ];
        }

        // Anleger B: Plätze 1-8, ein paar mit anderem Status
        for ($i = 1; $i <= 8; $i++) {
            $status = 'verfuegbar';
            if ($i === 2) $status = 'gesperrt';
            if ($i === 4) $status = 'vermietet';
            if ($i === 6) $status = 'belegt';

            $data[] = [
                'anleger' => 'B',
                'nummer'  => $i,
                'status'  => $status,
            ];
        }

        $this->db->table('liegeplaetze')->insertBatch($data);
    }
}
