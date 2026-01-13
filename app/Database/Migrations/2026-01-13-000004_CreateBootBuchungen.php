<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBootBuchungen extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'bbid' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'boid' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kid' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'von' => ['type' => 'DATE'],
            'bis' => ['type' => 'DATE'],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktiv', 'storniert'],
                'default'    => 'aktiv',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('bbid', true);
        $this->forge->addKey('boid');
        $this->forge->addKey('kid');

        $this->forge->addForeignKey('boid', 'boote', 'boid', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kid', 'kunden', 'kid', 'CASCADE', 'CASCADE');

        $this->forge->createTable('boot_buchungen');
    }

    public function down()
    {
        $this->forge->dropTable('boot_buchungen');
    }
}