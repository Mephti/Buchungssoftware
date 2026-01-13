<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLiegeplaetze extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'lid' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'anleger' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'nummer' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['verfügbar', 'gesperrt', 'vermietet', 'belegt'],
                'default'    => 'verfügbar',
            ],
        ]);

        $this->forge->addKey('lid', true);
        $this->forge->addUniqueKey(['anleger', 'nummer']); // eindeutige Platznummer pro Anleger
        $this->forge->createTable('liegeplaetze');
    }

    public function down()
    {
        $this->forge->dropTable('liegeplaetze');
    }
}