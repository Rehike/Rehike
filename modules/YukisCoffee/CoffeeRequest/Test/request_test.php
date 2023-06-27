<?php
require "autoloader.php";
// Anonymous promises

use YukisCoffee\CoffeeRequest\CoffeeRequest;

CoffeeRequest::request("http://127.0.0.3/")->then(function ($r) {
    echo $r::class;
    echo "\n\n";
    var_dump($r);
});

CoffeeRequest::run();