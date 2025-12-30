<?php

namespace App\Helpers;

class NumberToWords
{
    private static $unidades = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'
    ];
    
    private static $decenas = [
        '', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta',
        'sesenta', 'setenta', 'ochenta', 'noventa'
    ];
    
    private static $especiales = [
        10 => 'diez', 11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce',
        15 => 'quince', 16 => 'dieciséis', 17 => 'diecisiete', 18 => 'dieciocho', 19 => 'diecinueve',
        20 => 'veinte', 21 => 'veintiuno', 22 => 'veintidós', 23 => 'veintitrés', 24 => 'veinticuatro',
        25 => 'veinticinco', 26 => 'veintiséis', 27 => 'veintisiete', 28 => 'veintiocho', 29 => 'veintinueve'
    ];
    
    private static $centenas = [
        '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos',
        'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
    ];
    
    public static function convert($number)
    {
        $number = floor($number);
        
        if ($number == 0) {
            return 'cero';
        }
        
        if ($number < 0) {
            return 'menos ' . self::convert(abs($number));
        }
        
        if ($number < 10) {
            return self::$unidades[$number];
        }
        
        if ($number < 30) {
            return self::$especiales[$number] ?? self::$decenas[floor($number / 10)] . ' y ' . self::$unidades[$number % 10];
        }
        
        if ($number < 100) {
            $resto = $number % 10;
            return self::$decenas[floor($number / 10)] . ($resto > 0 ? ' y ' . self::$unidades[$resto] : '');
        }
        
        if ($number == 100) {
            return 'cien';
        }
        
        if ($number < 1000) {
            $centena = floor($number / 100);
            $resto = $number % 100;
            return self::$centenas[$centena] . ($resto > 0 ? ' ' . self::convert($resto) : '');
        }
        
        if ($number < 1000000) {
            $miles = floor($number / 1000);
            $resto = $number % 1000;
            $milesText = $miles == 1 ? 'mil' : self::convert($miles) . ' mil';
            return $milesText . ($resto > 0 ? ' ' . self::convert($resto) : '');
        }
        
        return 'número demasiado grande';
    }
}