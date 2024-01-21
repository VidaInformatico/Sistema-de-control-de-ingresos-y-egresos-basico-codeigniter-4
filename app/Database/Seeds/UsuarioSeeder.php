<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'usuario' => 'admin',
                'correo' => 'admin@gmail.com',
                'nombre' => 'Tu nombre',
                'apellido' => 'Tu apellido',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'rol' => 'admin',
            ],
        ];

        // Inserta los datos en la tabla
        $this->db->table('usuarios')->insertBatch($data);
    }
}

