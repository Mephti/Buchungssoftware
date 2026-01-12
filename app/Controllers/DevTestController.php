<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class DevTestController extends Controller
{
    public function dbTest()
    {
        $db = Database::connect();

        // Test: Liste der Tabellen anzeigen
        $tables = $db->listTables();

        return $this->response->setJSON([
            'ok' => true,
            'tables' => $tables,
        ]);
    }

    public function kundenTest()
    {
        $model = new \App\Models\KundeModel();

        return $this->response->setJSON([
            'ok' => true,
            'kunden' => $model->findNewest(5),
        ]);
    }

}