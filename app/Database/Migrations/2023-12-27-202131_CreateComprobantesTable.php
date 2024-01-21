<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateComprobantesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'codigo' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('comprobantes');
    }

    public function down()
    {
        $this->forge->dropTable('comprobantes');
    }
}
