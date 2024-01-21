<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConfiguracionTable extends Migration
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
            'identidad' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'unique' => true,
            ],
            'nombre_comercial' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'razon_social' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'telefono' => [
                'type' => 'VARCHAR',
                'constraint' => '50'
            ],
            'correo' => [
                'type' => 'VARCHAR',
                'constraint' => '150'
            ],
            'direccion' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'mensaje' => [
                'type' => 'TEXT'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('configuracion');
    }

    public function down()
    {
        $this->forge->dropTable('configuracion');
    }
}
