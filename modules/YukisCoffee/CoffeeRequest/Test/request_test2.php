<?php
require "autoloader.php";
// Anonymous promises

use YukisCoffee\CoffeeRequest\CoffeeRequest;
use YukisCoffee\CoffeeRequest\Promise;


function request(): Promise/*<Response>*/
{
    return CoffeeRequest::request("http://127.0.0.3/");
}

function promiseTest(): Promise/*<void>*/
{
    return new Promise(function($r): Generator/*<void>*/ {
        $endTime = time() + 2;

        while (time() < $endTime)
        {
            yield;
        }

        $r("test");
    });
}

Promise::all([request(), request(), promiseTest()])->then(function($responses){
    var_dump($responses);
});

CoffeeRequest::run();