<?php
namespace QuickPb;

class Compiler {
    public static function compile($obj)  {

    }
    
    public static function compileFields($obj) {
        $fields = (object) [];
        foreach ($obj as $key => $_val) {
            $type = null;
            $nest = null;
            switch (gettype( $obj->{$key} )) {
                case 'object':
                    $type = WIRETYPE_LENGTH;
                    $nest = self::compileFields( $obj->{$key} );
                    break;
                case 'string':
                    $type = WIRETYPE_LENGTH;
                    break;
                case 'integer':
                    $type = WIRETYPE_VARINT;
                    break;
                case 'double': // PHP gettype returns double for all floats
                    switch ($_val) {
                        case ($_val * 1e7 | 0) / 1e7:
                            $type = WIRETYPE_FLOAT;
                            break;
                        default:
                            $type = WIRETYPE_DOUBLE;
                            break;
                    }
                    break;
            }
            $fields->{$key} = [$key, $type, $nest];
        }
        return $fields;
    }

    public static function compileKvPair($key, $val, $fields) {

    }

    public static function compileKey() {

    }

    public static function getKey($fieldNumber, $wireType) {
        $shift = $fieldNumber << 3;
        return $shift | $wireType;
    }
}