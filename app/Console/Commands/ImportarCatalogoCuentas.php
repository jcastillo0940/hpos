<?php

namespace App\Console\Commands;

use App\Models\PlanCuenta;
use App\Models\Empresa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportarCatalogoCuentas extends Command
{
    protected $signature = 'cuentas:importar-catalogo {--limpiar : Eliminar cuentas existentes antes de importar}';
    protected $description = 'Importa el catálogo completo de cuentas contables';

    public function handle()
    {
        $this->info('?? Importando catálogo de cuentas contables...');
        $this->newLine();
        
        // Leer JSON
        $jsonPath = base_path('catalogo_cuentas.json');
        
        if (!file_exists($jsonPath)) {
            $this->error('? No se encontró el archivo catalogo_cuentas.json');
            $this->warn('   Coloca el archivo en la raíz del proyecto');
            return 1;
        }
        
        $cuentasData = json_decode(file_get_contents($jsonPath), true);
        
        if (!$cuentasData) {
            $this->error('? Error al leer el archivo JSON');
            return 1;
        }
        
        // Obtener empresa
        $empresa = Empresa::first();
        
        if (!$empresa) {
            $this->error('? No se encontró ninguna empresa');
            return 1;
        }
        
        // Limpiar si se solicitó
        if ($this->option('limpiar')) {
            if ($this->confirm('??  ¿Estás seguro de eliminar TODAS las cuentas existentes?', false)) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                PlanCuenta::where('empresa_id', $empresa->id)->delete();
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                $this->info('? Cuentas eliminadas');
            } else {
                $this->warn('Importación cancelada');
                return 0;
            }
        }
        
        $this->info("Importando para {$empresa->nombre}...");
        $bar = $this->output->createProgressBar(count($cuentasData));
        $bar->start();
        
        DB::transaction(function () use ($cuentasData, $empresa, $bar) {
            $cuentasCreadas = [];
            $contadores = [1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1];
            
            foreach ($cuentasData as $index => $data) {
                $nivel = $data['nivel'];
                $codigo = null;
                $padre_id = null;
                
                // Generar código jerárquico según nivel
                if ($nivel == 1) {
                    // Nivel 1: 1, 2, 3, 4, 5, 6, 7
                    $codigo = (string) $contadores[$nivel];
                    $contadores[$nivel]++;
                } else {
                    // Buscar padre (cuenta del nivel anterior más cercana)
                    $padreIndex = $this->buscarPadre($cuentasData, $index);
                    
                    if ($padreIndex !== null && isset($cuentasCreadas[$padreIndex])) {
                        $cuentaPadre = $cuentasCreadas[$padreIndex];
                        $padre_id = $cuentaPadre['id'];
                        $codigoPadre = $cuentaPadre['codigo'];
                        
                        // Contar hermanos
                        $numeroHermano = $this->contarHermanos($cuentasData, $cuentasCreadas, $index, $nivel, $padreIndex);
                        
                        // Generar código hijo
                        if ($nivel == 2) {
                            $codigo = $codigoPadre . '.' . $numeroHermano;
                        } elseif ($nivel == 3) {
                            $codigo = $codigoPadre . '.' . str_pad($numeroHermano, 2, '0', STR_PAD_LEFT);
                        } else {
                            $codigo = $codigoPadre . '.' . str_pad($numeroHermano, 2, '0', STR_PAD_LEFT);
                        }
                    }
                }
                
                if (!$codigo) {
                    $this->warn("\n??  No se pudo generar código para: {$data['nombre']}");
                    $bar->advance();
                    continue;
                }
                
                // Crear cuenta
                try {
                    $cuenta = PlanCuenta::create([
                        'empresa_id' => $empresa->id,
                        'codigo' => $codigo,
                        'nombre' => $data['nombre'],
                        'descripcion' => $data['descripcion'],
                        'tipo' => $data['tipo'],
                        'naturaleza' => $data['naturaleza'],
                        'nivel' => $nivel,
                        'cuenta_padre_id' => $padre_id,
                        'cuenta_sistema' => $data['cuenta_sistema'],
                        'acepta_movimiento' => $data['acepta_movimiento'],
                        'requiere_tercero' => false,
                        'requiere_centro_costo' => false,
                        'es_sistema' => $data['cuenta_sistema'] ? true : false,
                        'activa' => true,
                    ]);
                    
                    $cuentasCreadas[$index] = [
                        'id' => $cuenta->id,
                        'codigo' => $codigo,
                        'nivel' => $nivel,
                        'nombre' => $data['nombre']
                    ];
                } catch (\Exception $e) {
                    $this->warn("\n??  Error creando cuenta '{$data['nombre']}' (Código: {$codigo}): " . $e->getMessage());
                }
                
                $bar->advance();
            }
        });
        
        $bar->finish();
        $this->newLine(2);
        
        // Mostrar resumen
        $total = PlanCuenta::where('empresa_id', $empresa->id)->count();
        $conSistema = PlanCuenta::where('empresa_id', $empresa->id)->whereNotNull('cuenta_sistema')->count();
        
        $this->info("? Importación completada exitosamente");
        $this->table(
            ['Concepto', 'Cantidad'],
            [
                ['Total de cuentas', $total],
                ['Cuentas del sistema', $conSistema],
                ['Nivel 1', PlanCuenta::where('empresa_id', $empresa->id)->where('nivel', 1)->count()],
                ['Nivel 2', PlanCuenta::where('empresa_id', $empresa->id)->where('nivel', 2)->count()],
                ['Nivel 3', PlanCuenta::where('empresa_id', $empresa->id)->where('nivel', 3)->count()],
                ['Nivel 4+', PlanCuenta::where('empresa_id', $empresa->id)->where('nivel', '>=', 4)->count()],
            ]
        );
        
        // Mostrar cuentas del sistema
        $this->newLine();
        $this->info('=== CUENTAS DEL SISTEMA ===');
        $cuentasSistema = PlanCuenta::where('empresa_id', $empresa->id)
            ->whereNotNull('cuenta_sistema')
            ->orderBy('codigo')
            ->get(['codigo', 'nombre', 'cuenta_sistema']);
        
        foreach ($cuentasSistema as $c) {
            $this->line("  {$c->codigo} - {$c->nombre} ? {$c->cuenta_sistema}");
        }
        
        return 0;
    }
    
    private function buscarPadre($cuentas, $index)
    {
        $nivelActual = $cuentas[$index]['nivel'];
        $nivelPadre = $nivelActual - 1;
        
        // Buscar hacia atrás la cuenta del nivel inmediato anterior
        for ($i = $index - 1; $i >= 0; $i--) {
            if ($cuentas[$i]['nivel'] == $nivelPadre) {
                return $i;
            }
        }
        
        return null;
    }
    
    private function contarHermanos($cuentas, $cuentasCreadas, $index, $nivel, $padreIndex)
    {
        $contador = 1;
        
        // Contar cuántos hermanos del mismo nivel y mismo padre hay antes de este
        for ($i = $index - 1; $i >= 0; $i--) {
            if ($cuentas[$i]['nivel'] == $nivel) {
                // Verificar si tienen el mismo padre
                $padreDei = $this->buscarPadre($cuentas, $i);
                if ($padreDei === $padreIndex) {
                    $contador++;
                }
            } elseif ($cuentas[$i]['nivel'] < $nivel) {
                // Ya llegamos al padre, no hay más hermanos
                break;
            }
        }
        
        return $contador;
    }
}