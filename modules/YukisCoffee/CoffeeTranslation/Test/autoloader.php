<?php
spl_autoload_register(function($className) {
    $className = str_replace("\\", "/", $className);

    include explode("CoffeeTranslation/", str_replace("\\", "/", __DIR__))[0] . 
        str_replace("YukisCoffee", "", $className) .
        ".php"
    ;
});