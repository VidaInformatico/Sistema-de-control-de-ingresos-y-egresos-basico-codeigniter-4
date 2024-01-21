<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMovimientosTable extends Migration
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
            'monto' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'descripcion' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'estado' => [
                'type' => 'INT',
                'constraint' => '5',
                'default' => 1,
            ],
            'imagen' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'fecha' => [
                'type' => 'DATETIME'
            ],
            'movimiento' => [
                'type' => 'INT',
                'constraint' => 5,
            ],
            'id_caja' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'id_comprobante' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'id_usuario' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_caja', 'cajas', 'id');
        $this->forge->addForeignKey('id_comprobante', 'comprobantes', 'id');
        $this->forge->addForeignKey('id_usuario', 'usuarios', 'id');
        $this->forge->createTable('movimientos');
    }

    public function down()
    {
        $this->forge->dropTable('movimientos');
    }
}
