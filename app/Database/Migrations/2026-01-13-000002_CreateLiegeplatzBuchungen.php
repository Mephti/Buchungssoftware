<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLiegeplatzBuchungen extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'bid' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'lid' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kid' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'von' => [
                'type' => 'DATE',
            ],
            'bis' => [
                'type' => 'DATE',
            ],
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

        $this->forge->addKey('bid', true);
        $this->forge->addKey('lid');
        $this->forge->addKey('kid');

        // Optional, aber sauber: Foreign Keys (wenn Engine/InnoDB passt)
        $this->forge->addForeignKey('lid', 'liegeplaetze', 'lid', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kid', 'kunden', 'kid', 'CASCADE', 'CASCADE');

        $this->forge->createTable('liegeplatz_buchungen');
    }

    public function down()
    {
        $this->forge->dropTable('liegeplatz_buchungen');
    }
}