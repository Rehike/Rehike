<?php

class ProtobufHelper {
    public static function uleb128($val) {
        $bin = decbin($val);
        $pad = 7 - (strlen($bin) % 7);
        if ($pad != 7) {
            $bin = substr("0000000", 0, $pad) + bin;
        }

        $bin = "0" + preg_match("/.{7}/g", $bin);
    }
}