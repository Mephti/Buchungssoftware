<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBoote extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'boid' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
            ],
            'typ' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => true,
            ],
            'plaetze' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 2,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['verfuegbar', 'gesperrt', 'wartung', 'unterwegs'],
                'default'    => 'verfuegbar',
            ],
        ]);

        $this->forge->addKey('boid', true);
        $this->forge->createTable('boote');
    }

    public function down()
    {
        $this->forge->dropTable('boote');
    }
}