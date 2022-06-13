<?php
require_once($root . '/vendor/autoload.php');

include "modules/YukisCoffee/CoffeeException.php";
include "modules/YukisCoffee/GetPropertyAtPath.php";

function YcRehikeAutoloader($class)
{
   $class = str_replace("\\", "/", $class);

   if (file_exists("modules/{$class}.php")) {
      require "modules/{$class}.php";
   }
   else if ("Rehike/Model/" == substr($class, 0, 13))
   {
      $file = substr($class, 13, strlen($class));

      require "models/${file}.php";
   }
   else if ("Rehike/Controller" == substr($class, 0, 17))
   {
      $file = substr($class, 17, strlen($class));

      require "controllers/${file}.php";
   }
}
spl_autoload_register('YcRehikeAutoloader');

// Controller V2 init

use Rehike\ControllerV2\Core as ControllerV2;

ControllerV2::registerStateVariable($yt);
ControllerV2::registerTemplateVariable($template);


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

// temporary? move twig when?
\Rehike\TemplateFunctions::$boundTwigInstance = &$twig;

foreach (glob('modules/template_functions/*.php') as $file) include $file;

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