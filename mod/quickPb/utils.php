<?php

namespace QuickPb;

class Utils {
    public static function pad(string $input, int $amount = 2): string {
        while ($input % $amount != 0) $input = '0' . $input;
        return $input;
    }
    public static function swapEndianness(string $input, int $byteLength = 2): string {
        $input = str_split($input, $byteLength);
        $input = array_reverse($input);
        $input = implode($input);
        return $input;
    }
    public static function q_uleb128(int $a): string {
        // limited by php int limitations, but chad move overall
        if ($a <= 127) return dechex($a);
        else if ($a <= 16383) {
            
        }
    }
}

class Uleb128 {
    public static function encode(int $a): string {
        if ($a <= 127) return dechex($a);
        
        $a = decbin($a);
        $a = str_split( Utils::pad($a, 7), 7 );
        $a[0] = '0' . $a[0];
        for ($i = 1, $j = count($a); $i < $j; $i++) {
            $a[$i] = '1' . $a[$i];
        }
        $a = implode($a);
        $a = Utils::swapEndianness($a, 7);
        $a = strtolower(bin2hex($a));
        return $a;
    }
}