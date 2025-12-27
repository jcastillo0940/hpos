<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlanCuenta;

class PlanCuentasPanamaSeeder extends Seeder
{
    public function run(): void
    {
        $empresaId = 1; // Cambiar según empresa

        $cuentas = [
            // ACTIVOS
            ['codigo' => '1', 'nombre' => 'ACTIVO', 'tipo' => 'activo', 'naturaleza' => 'debito', 'nivel' => 1, 'acepta_movimiento' => false, 'es_sistema' => true],
            ['codigo' => '1.1', 'nombre' => 'ACTIVO CORRIENTE', 'tipo' => 'activo', 'naturaleza' => 'debito', 'nivel' => 2, 'acepta_movimiento' => false, 'padre' => '1', 'es_sistema' => true],
            
            // Caja y Bancos
            ['codigo' => '1.1.01', 'nombre' => 'EFECTIVO Y EQUIVALENTES', 'tipo' => 'activo', 'naturaleza' => 'debito', 'nivel' => 3, 'acepta_movimiento' => false, 'padre' => '1.1', 'es_sistema' => true],
            ['codigo' => '1.1.01.01', 'nombre' => 'Caja General', 'tipo' => 'activo', 'naturaleza' => 'debito', 'nivel' => 4, 'acepta_movimiento' => true, 'padre' => '1.1.01', 'es_sistema' => true, 'cuenta_sistema' => 'caja'],
            ['codigo' => '1.1.01.02', 'nombre' => 'Caja Repartidor', 'tipo' => 'activo', 'naturaleza' => 'debito', 'nivel' => 4, 'acepta_movimiento' => true, 'padre' => '1.1.01', 'es_sistema' => true, 'cuenta_sistema' => 'caja_repartidor'],
            ['codigo' => '1.1.01.03', 'nombre' => 'Bancos Cuenta Corriente', 'tipo' => 'activo', 'naturaleza' => 'debito', 'nivel' => 4, 'acepta_movimiento' => true, 'padre' => '1.1.01', 'es_sistema' => true, 'cuenta_sistema' => 'banco'],
            ['codigo' => '1.1.01.04', 'nombre' => 'Bancos Cuenta Ahorro', 'tipo' => 'activo', 'naturaleza' => 'debito', 'nivel' => 4, 'acepta_movimiento' => true, 'padre' => '1.1.01', 'es_sistema' => true],
            
            // Cuentas por Cobrar
            ['codigo' => '1.1.02', 'nombre' => 'CUENTAS POR COBRAR', 'tipo' => 'activo', 'naturaleza' => 'debito', 'nivel' => 3, 'acepta_movimiento' => false, 'padre' => '1.1', 'es_sistema' => true],
            ['codigo' => '1.1.02.01', 'nombre' => 'Clientes', 'tipo' => 'activo', 'naturaleza' => 'debito', 'nivel' => 4, 'acepta_movimiento' => true, 'requiere_tercero' => true, 'padre' => '1.1.02', 'es_sistema' => true, 'cuenta_sistema' => 'cxc'],
            ['codigo' => '1.1.02.02', 'nombre' => 'Provisión Cuentas Incobrables', 'tipo' => 'activo', 'naturaleza' => 'credito', 'nivel' => 4, 'acepta_movimiento' => true, 'padre' => '1.1.02', 'es_sistema' => true],
            
            // Inventarios
            ['codigo' => '1.1.04', 'nombre' => 'INVENTARIOS', 'tipo' => 'activo', 'naturaleza' => 'debito', 'nivel' => 3, 'acepta_movimiento' => false, 'padre' => '1.1', 'es_sistema' => true],
            ['codigo' => '1.1.04.01', 'nombre' => 'Inventario de Mercancías', 'tipo' => 'activo', 'naturaleza' => 'debito', 'nivel' => 4, 'acepta_movimiento' => true, 'padre' => '1.1.04', 'es_sistema' => true, 'cuenta_sistema' => 'inventario'],
            
            // PASIVOS
            ['codigo' => '2', 'nombre' => 'PASIVO', 'tipo' => 'pasivo', 'naturaleza' => 'credito', 'nivel' => 1, 'acepta_movimiento' => false, 'es_sistema' => true],
            ['codigo' => '2.1', 'nombre' => 'PASIVO CORRIENTE', 'tipo' => 'pasivo', 'naturaleza' => 'credito', 'nivel' => 2, 'acepta_movimiento' => false, 'padre' => '2', 'es_sistema' => true],
            
            // Cuentas por Pagar
            ['codigo' => '2.1.01', 'nombre' => 'CUENTAS POR PAGAR', 'tipo' => 'pasivo', 'naturaleza' => 'credito', 'nivel' => 3, 'acepta_movimiento' => false, 'padre' => '2.1', 'es_sistema' => true],
            ['codigo' => '2.1.01.01', 'nombre' => 'Proveedores', 'tipo' => 'pasivo', 'naturaleza' => 'credito', 'nivel' => 4, 'acepta_movimiento' => true, 'requiere_tercero' => true, 'padre' => '2.1.01', 'es_sistema' => true, 'cuenta_sistema' => 'cxp'],
            
            // ITBMS
            ['codigo' => '2.1.03', 'nombre' => 'IMPUESTOS POR PAGAR', 'tipo' => 'pasivo', 'naturaleza' => 'credito', 'nivel' => 3, 'acepta_movimiento' => false, 'padre' => '2.1', 'es_sistema' => true],
            ['codigo' => '2.1.03.01', 'nombre' => 'ITBMS por Pagar', 'tipo' => 'pasivo', 'naturaleza' => 'credito', 'nivel' => 4, 'acepta_movimiento' => true, 'padre' => '2.1.03', 'es_sistema' => true, 'cuenta_sistema' => 'itbms_pagar'],
            ['codigo' => '2.1.03.02', 'nombre' => 'ITBMS Acreditable', 'tipo' => 'pasivo', 'naturaleza' => 'debito', 'nivel' => 4, 'acepta_movimiento' => true, 'padre' => '2.1.03', 'es_sistema' => true, 'cuenta_sistema' => 'itbms_acreditable'],
            
            // PATRIMONIO
            ['codigo' => '3', 'nombre' => 'PATRIMONIO', 'tipo' => 'patrimonio', 'naturaleza' => 'credito', 'nivel' => 1, 'acepta_movimiento' => false, 'es_sistema' => true],
            ['codigo' => '3.1', 'nombre' => 'CAPITAL', 'tipo' => 'patrimonio', 'naturaleza' => 'credito', 'nivel' => 2, 'acepta_movimiento' => false, 'padre' => '3', 'es_sistema' => true],
            ['codigo' => '3.1.01', 'nombre' => 'Capital Social', 'tipo' => 'patrimonio', 'naturaleza' => 'credito', 'nivel' => 3, 'acepta_movimiento' => true, 'padre' => '3.1', 'es_sistema' => true],
            ['codigo' => '3.2', 'nombre' => 'RESULTADOS', 'tipo' => 'patrimonio', 'naturaleza' => 'credito', 'nivel' => 2, 'acepta_movimiento' => false, 'padre' => '3', 'es_sistema' => true],
            ['codigo' => '3.2.01', 'nombre' => 'Utilidades Acumuladas', 'tipo' => 'patrimonio', 'naturaleza' => 'credito', 'nivel' => 3, 'acepta_movimiento' => true, 'padre' => '3.2', 'es_sistema' => true],
            ['codigo' => '3.2.02', 'nombre' => 'Utilidad del Ejercicio', 'tipo' => 'patrimonio', 'naturaleza' => 'credito', 'nivel' => 3, 'acepta_movimiento' => true, 'padre' => '3.2', 'es_sistema' => true],
            
            // INGRESOS
            ['codigo' => '4', 'nombre' => 'INGRESOS', 'tipo' => 'ingreso', 'naturaleza' => 'credito', 'nivel' => 1, 'acepta_movimiento' => false, 'es_sistema' => true],
            ['codigo' => '4.1', 'nombre' => 'INGRESOS OPERACIONALES', 'tipo' => 'ingreso', 'naturaleza' => 'credito', 'nivel' => 2, 'acepta_movimiento' => false, 'padre' => '4', 'es_sistema' => true],
            ['codigo' => '4.1.01', 'nombre' => 'Ventas', 'tipo' => 'ingreso', 'naturaleza' => 'credito', 'nivel' => 3, 'acepta_movimiento' => true, 'padre' => '4.1', 'es_sistema' => true, 'cuenta_sistema' => 'ventas'],
            ['codigo' => '4.1.02', 'nombre' => 'Devoluciones en Ventas', 'tipo' => 'ingreso', 'naturaleza' => 'debito', 'nivel' => 3, 'acepta_movimiento' => true, 'padre' => '4.1', 'es_sistema' => true, 'cuenta_sistema' => 'devolucion_ventas'],
            
            // COSTOS
            ['codigo' => '5', 'nombre' => 'COSTOS', 'tipo' => 'costo', 'naturaleza' => 'debito', 'nivel' => 1, 'acepta_movimiento' => false, 'es_sistema' => true],
            ['codigo' => '5.1', 'nombre' => 'COSTO DE VENTAS', 'tipo' => 'costo', 'naturaleza' => 'debito', 'nivel' => 2, 'acepta_movimiento' => false, 'padre' => '5', 'es_sistema' => true],
            ['codigo' => '5.1.01', 'nombre' => 'Costo de Ventas', 'tipo' => 'costo', 'naturaleza' => 'debito', 'nivel' => 3, 'acepta_movimiento' => true, 'padre' => '5.1', 'es_sistema' => true, 'cuenta_sistema' => 'costo_ventas'],
            
            // GASTOS
            ['codigo' => '6', 'nombre' => 'GASTOS', 'tipo' => 'gasto', 'naturaleza' => 'debito', 'nivel' => 1, 'acepta_movimiento' => false, 'es_sistema' => true],
            ['codigo' => '6.1', 'nombre' => 'GASTOS OPERACIONALES', 'tipo' => 'gasto', 'naturaleza' => 'debito', 'nivel' => 2, 'acepta_movimiento' => false, 'padre' => '6', 'es_sistema' => true],
            ['codigo' => '6.1.01', 'nombre' => 'Salarios y Prestaciones', 'tipo' => 'gasto', 'naturaleza' => 'debito', 'nivel' => 3, 'acepta_movimiento' => true, 'padre' => '6.1', 'es_sistema' => true],
            ['codigo' => '6.1.02', 'nombre' => 'Alquileres', 'tipo' => 'gasto', 'naturaleza' => 'debito', 'nivel' => 3, 'acepta_movimiento' => true, 'padre' => '6.1', 'es_sistema' => true],
            ['codigo' => '6.1.03', 'nombre' => 'Servicios Públicos', 'tipo' => 'gasto', 'naturaleza' => 'debito', 'nivel' => 3, 'acepta_movimiento' => true, 'padre' => '6.1', 'es_sistema' => true],
            ['codigo' => '6.1.04', 'nombre' => 'Combustible', 'tipo' => 'gasto', 'naturaleza' => 'debito', 'nivel' => 3, 'acepta_movimiento' => true, 'padre' => '6.1', 'es_sistema' => true],
            ['codigo' => '6.1.05', 'nombre' => 'Pérdida por Mermas y Vencidos', 'tipo' => 'gasto', 'naturaleza' => 'debito', 'nivel' => 3, 'acepta_movimiento' => true, 'padre' => '6.1', 'es_sistema' => true, 'cuenta_sistema' => 'gasto_mermas'],
        ];

        $cuentasCreadas = [];

        foreach ($cuentas as $cuenta) {
            $padre = null;
            if (isset($cuenta['padre'])) {
                $padre = $cuentasCreadas[$cuenta['padre']] ?? null;
            }

            $cuentaCreada = PlanCuenta::create([
                'empresa_id' => $empresaId,
                'codigo' => $cuenta['codigo'],
                'nombre' => $cuenta['nombre'],
                'tipo' => $cuenta['tipo'],
                'naturaleza' => $cuenta['naturaleza'],
                'nivel' => $cuenta['nivel'],
                'acepta_movimiento' => $cuenta['acepta_movimiento'],
                'requiere_tercero' => $cuenta['requiere_tercero'] ?? false,
                'es_sistema' => $cuenta['es_sistema'] ?? false,
                'cuenta_sistema' => $cuenta['cuenta_sistema'] ?? null,
                'cuenta_padre_id' => $padre,
            ]);

            $cuentasCreadas[$cuenta['codigo']] = $cuentaCreada->id;
        }
    }
}