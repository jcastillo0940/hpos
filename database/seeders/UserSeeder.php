<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear empresa de ejemplo
        $empresa = Empresa::create([
            'ruc' => '12345678-1-123456',
            'dv' => '01',
            'razon_social' => 'Distribuidora Demo S.A.',
            'nombre_comercial' => 'Distribuidora Demo',
            'email' => 'info@distribuidorademo.com',
            'telefono' => '507-6000-0000',
            'direccion' => 'Calle Principal, Ciudad de Panamá',
            'provincia' => 'Panamá',
            'distrito' => 'Panamá',
            'corregimiento' => 'San Felipe',
            'usa_multibodega' => true,
            'usa_vencimientos' => true,
            'metodo_costeo' => 'promedio',
            'facturacion_electronica' => true,
            'activa' => true,
        ]);

        // Crear usuario administrador
        $admin = User::create([
            'empresa_id' => $empresa->id,
            'codigo_empleado' => 'ADM001',
            'name' => 'Administrador',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'telefono' => '507-6000-0001',
            'activo' => true,
        ]);
        
        $admin->assignRole('Administrador Total');

        // Crear vendedor de ejemplo
        $vendedor = User::create([
            'empresa_id' => $empresa->id,
            'codigo_empleado' => 'VEN001',
            'name' => 'Juan Pérez',
            'email' => 'vendedor@demo.com',
            'password' => Hash::make('password'),
            'telefono' => '507-6000-0002',
            'activo' => true,
        ]);
        
        $vendedor->assignRole('Vendedor');

        // Crear repartidor de ejemplo
        $repartidor = User::create([
            'empresa_id' => $empresa->id,
            'codigo_empleado' => 'REP001',
            'name' => 'Carlos Rodríguez',
            'email' => 'repartidor@demo.com',
            'password' => Hash::make('password'),
            'telefono' => '507-6000-0003',
            'activo' => true,
        ]);
        
        $repartidor->assignRole('Repartidor');
    }
}