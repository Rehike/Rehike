<?php
require_once($root . '/vendor/autoload.php');

include "modules/YukisCoffee/CoffeeException.php";
include "modules/YukisCoffee/GetPropertyAtPath.php";

require('modules/playerCore.php');
$_playerCore = PlayerCore::main();
$yt->playerCore = $_playerCore;
$yt->playerBasepos = $_playerCore->basepos;

if (isset($_COOKIE["PREF"])) {
   $PREF = explode("&", $_COOKIE["PREF"]);
   $yt->PREF = (object) [];
   for ($i = 0; $i < count($PREF); $i++) {
      $option = explode("=", $PREF[$i]);
      $title = $option[0];
      $yt->PREF->$title = $option[1];
   }
} else {
   $yt->PREF = (object) [
      "f5" => "20030"
   ];
}

$yt -> version = json_decode(file_get_contents($root . "/.version"));

$twigLoader = new \Twig\Loader\FilesystemLoader(
   $root . $templateRoot
);

$twig = new \Twig\Environment($twigLoader, [
   'debug' => true
]);

$twig -> addFilter (
   new \Twig\TwigFilter("base64_encode", function($a){return base64_encode($a);})
);

function YcRehikeAutoloader($class)
{
   $class = str_replace("\\", "/", $class);

    if (file_exists("mod/{$class}.php")) {
        require "modules/{$class}.php";
    }
}
spl_autoload_register('YcRehikeAutoloader');


/**
 * TODO (kirasicecreamm): Clean up Template Functions system to have
 * a more clear name. This will probably done on new-mvc branch.
 */
function registerFunction($name, $cb): void {
   global $twig;
   
   $Jim = '_' . $name;
   global ${$Jim};
   ${$Jim} = $cb;
   
   $twig->addFunction(new \Twig\TwigFunction($name, $cb));
}

foreach (glob('modules/functions/*.php') as $file) include $file;

function findKey($array, string $key) {
   for ($i = 0, $j = count($array); $i < $j; $i++) {
      if (isset($array[$i]->{$key})) {
         return $array[$i]->{$key};
      }
   }
}

$response = null;

$twig->addGlobal('yt', $yt);
$twig->addGlobal('response', $response);
$twig->addFunction(new \Twig\TwigFunction('http_response_code', function($code) {
   http_response_code($code);
}));