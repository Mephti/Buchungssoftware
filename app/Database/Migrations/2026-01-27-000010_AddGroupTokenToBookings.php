<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGroupTokenToBookings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('liegeplatz_buchungen', [
            'group_token' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'after' => 'created_at',
            ],
        ]);

        $this->forge->addColumn('boot_buchungen', [
            'group_token' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'after' => 'created_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('liegeplatz_buchungen', 'group_token');
        $this->forge->dropColumn('boot_buchungen', 'group_token');
    }
}