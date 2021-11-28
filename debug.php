<?php
$template = 'debug';
ob_start();

/*function //consolelog($str) {
    echo $str.'<br>';
}*/

function encryptVisitorData($visitor) {
    function toYtDateBin($unixDate) {
        $unixDate = floor($unixDate);
        if (strlen((string)$unixDate) > 10) {
            $diff = strlen((string)$unixDate) - 10;
            $unixDate = floor($unixDate / (10 ** $diff));
        }
        $bin = decbin($unixDate);
        //consolelog('[date: bin to base2] ' . $bin);
        // ugly byte padding hack
        $bin = substr("00000000000000000000000000000000", strlen($bin)) . $bin;
        //consolelog('[date: ugly byte padding hack] ' . $bin);
        $carry = substr($bin, 0, 4);
        //consolelog('[date: carry] ' . $carry);
        $bin = substr($bin, strlen($carry), strlen($bin));
        //consolelog('[date: bin - carry] ' . $bin);
        $bin = str_split($bin, 7);
        for ($i = 0, $j = count($bin); $i < $j; $i++) {
            $bin[$i] = '1' . $bin[$i];
        }
        $bin = implode('', $bin);
        $finalbin = '0000' . $carry . $bin;
        //consolelog('[date: finalbin] ' . $finalbin);
        $hex = dechex(bindec($finalbin));
        //consolelog('[date: tohex] ' . $hex);
        $hex = (strlen($hex) & 1) ? '0' . $hex : $hex;
        //consolelog('[date: pad hex] ' . $hex);
        $hex = str_split($hex, 2);
        //consolelog('[date: split hex by 2] ' . implode(',', $hex));
        $hex = array_reverse($hex);
        //consolelog('[date: reverse hex] ' . implode(',', $hex));
        $hex = implode('', $hex);
        return $hex;
    }

    function b16tob64($str) {
        $response = '';
        foreach (str_split($str, 2) as $pair) {
            $response .= chr(hexdec($pair));
        }
        return base64_encode($response);
    }
    
    $magic = '0a0b';
    $dateSeparator = '28';
    //consolelog('[magic, dateSeparator] ' . $magic . ', ' . $dateSeparator);
    $date = toYtDateBin(time());
    //consolelog('[date] ' . $date);
    $payload = bin2hex($visitor);
    //consolelog('[payload] ' . $payload);
    $hexout = strtolower($magic . $payload . $dateSeparator . $date);
    //consolelog('[hexout] ' . $hexout);

    return b16tob64($hexout);
}

if (isset($_COOKIE['VISITOR_INFO1_LIVE'])) {
    echo 'VISITOR_INFO1_LIVE cookie value: ' . $_COOKIE['VISITOR_INFO1_LIVE'];
    echo '<br>';
    echo '<br>';
    echo time();
    echo '<br>';
    echo 'Generated value: ' . encryptVisitorData($_COOKIE['VISITOR_INFO1_LIVE']);
    echo '<br>';
    echo 'Expected value: CgtRUmUwTG1tRUp5WSiN_fmKBg==';
}

//echo 'Hello world';

$yt->debug = ob_get_clean();