<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos por módulo
        $permissions = [
            // Ventas
            'ver_ordenes_entrega',
            'crear_ordenes_entrega',
            'editar_ordenes_entrega',
            'anular_ordenes_entrega',
            'convertir_ordenes_entrega',
            
            'ver_facturas',
            'crear_facturas',
            'anular_facturas',
            
            'ver_notas_credito',
            'crear_notas_credito',
            
            // Compras
            'ver_ordenes_compra',
            'crear_ordenes_compra',
            'aprobar_ordenes_compra',
            
            'ver_facturas_compra',
            'crear_facturas_compra',
            
            // Inventario
            'ver_inventario',
            'ajustar_inventario',
            'transferir_inventario',
            
            // Reparto
            'ver_rutas',
            'ejecutar_rutas',
            'liquidar_rutas',
            
            // Cobranza
            'ver_cobranza',
            'registrar_cobros',
            'aplicar_pagos',
            
            // Reportes
            'ver_reportes_ventas',
            'ver_reportes_financieros',
            'ver_reportes_inventario',
            'ver_reportes_contables',
            
            // Contabilidad
            'ver_contabilidad',
            'crear_asientos',
            'cerrar_periodos',
            
            // Administración
            'administrar_empresa',
            'administrar_usuarios',
            'administrar_catalogos',
            'ver_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        
        // 1. Administrador Total
        $admin = Role::create(['name' => 'Administrador Total']);
        $admin->givePermissionTo(Permission::all());

        // 2. Gerente
        $gerente = Role::create(['name' => 'Gerente']);
        $gerente->givePermissionTo([
            'ver_reportes_ventas',
            'ver_reportes_financieros',
            'ver_reportes_inventario',
            'ver_reportes_contables',
            'ver_facturas',
            'ver_ordenes_compra',
            'aprobar_ordenes_compra',
            'ver_inventario',
        ]);

        // 3. Supervisor
        $supervisor = Role::create(['name' => 'Supervisor']);
        $supervisor->givePermissionTo([
            'ver_ordenes_entrega',
            'convertir_ordenes_entrega',
            'ver_facturas',
            'crear_facturas',
            'ver_notas_credito',
            'crear_notas_credito',
            'ver_rutas',
            'liquidar_rutas',
            'ver_reportes_ventas',
            'ver_inventario',
        ]);

        // 4. Vendedor
        $vendedor = Role::create(['name' => 'Vendedor']);
        $vendedor->givePermissionTo([
            'ver_ordenes_entrega',
            'crear_ordenes_entrega',
            'editar_ordenes_entrega',
            'ver_facturas', // Solo sus propias facturas
        ]);

        // 5. Repartidor
        $repartidor = Role::create(['name' => 'Repartidor']);
        $repartidor->givePermissionTo([
            'ver_rutas',
            'ejecutar_rutas',
            'registrar_cobros',
        ]);

        // 6. Contador
        $contador = Role::create(['name' => 'Contador']);
        $contador->givePermissionTo([
            'ver_contabilidad',
            'crear_asientos',
            'ver_reportes_contables',
            'ver_reportes_financieros',
            'cerrar_periodos',
        ]);
    }
}