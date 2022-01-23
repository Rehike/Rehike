<?php

namespace CacheUtils;

class StdClass {
    // Alternative to encoding a JSON object;
    // theoretically this achieves better performance, as
    // it takes advantage of native PHP behaviour.

    const PHP_MAGIC = '<?php'; // php magic required for execution
    const RETURN_KEYWORD = 'return';
    const OBJ_TYPECAST = '(object)';
    const ARR_OPEN = '[';
    const ARR_CLOSE = ']';
    const ASSOCARR_MAP = '=>';
    const ARR_SERPARTOR = ',';
    const SEMICOLON = ';';

    public static function encodeMapping(string $key, $value): string {
        $_val = is_object($value) ? encode($value, true): $value; // encode nests

        return $key . self::ASSOCARR_MAP . $_val;
    }

    public static function encode(object $obj, bool $isNest = false): string {
        $out = $isNest ? '' : self::PHP_MAGIC . ' ' . self::RETURN_KEYWORD . ' ';

        $out .= self::OBJ_TYPECAST . self::ARR_OPEN;

        $objLen = count( (array)$obj ); // array cast our object to count properties
        $i = 0;
        foreach ($obj as $key => $value) {
            $out .= self::encodeMapping($key, $value);
            if ($i < $objLen) {
                $out .= self::ARR_SERPARTOR;
            }
            $i++;
        }

        $out .= self::ARR_CLOSE . self::SEMICOLON;

        return $out;
    }
}