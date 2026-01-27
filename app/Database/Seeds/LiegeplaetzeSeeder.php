<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LiegeplaetzeSeeder extends Seeder
{
    public function run()
    {
        // Sicherheit: DB-Verbindung
        $db = \Config\Database::connect();

        // 1) Daten vorbereiten (35 Plätze)
        $data = [];

        // Anleger A: 1-17 (oben)
        for ($i = 1; $i <= 17; $i++) {
            $data[] = [
                'anleger' => 'A',
                'nummer'  => $i,
                'status'  => 'verfuegbar',
            ];
        }

        // Anleger B: 1-18 (unten)
        for ($i = 1; $i <= 18; $i++) {
            $data[] = [
                'anleger' => 'B',
                'nummer'  => $i,
                'status'  => 'verfuegbar',
            ];
        }

        // 2) Tabelle leeren (robust: delete statt truncate falls FK/Engine zickt)
        $db->table('liegeplaetze')->emptyTable();

        // 3) Insert (Batch)
        $db->table('liegeplaetze')->insertBatch($data);
    }
}