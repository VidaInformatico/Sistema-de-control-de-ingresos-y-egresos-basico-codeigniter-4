<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCajasTable extends Migration
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
            'monto_inicial' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'fecha_apertura' => [
                'type' => 'DATETIME'
            ],
            'fecha_cierre' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'ingresos' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],
            'egresos' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],
            'estado' => [
                'type' => 'INT',
                'constraint' => '5',
                'default' => 1,
            ],
            'id_usuario' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_usuario', 'usuarios', 'id');
        $this->forge->createTable('cajas');
    }

    public function down()
    {
        $this->forge->dropTable('cajas');
    }
}
