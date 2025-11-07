<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRefusedClients extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'client_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'record_date' => [
                'type' => 'DATE',
            ],
            'record_id' => [
                'type' => 'INT',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'state'=> [
                'type'       => 'CHAR',
                'constraint' => '2',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('refused_clients');
    }

    public function down()
    {
        $this->forge->dropTable('refused_clients');
    }
}
