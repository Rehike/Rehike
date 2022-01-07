<?php
require_once($root . '/vendor/autoload.php');

//require($root . '/signin/signincore2.php');
//require($root . '/signin/sc2stub.php');
//$sc2 = new YtSigninCore();

$twigLoader = new \Twig\Loader\FilesystemLoader(
   $root . $templateRoot
);

$twig = new \Twig\Environment($twigLoader, [
   'debug' => true
]);




function registerFunction($name, $cb): void {
   global $twig;
   
   $Jim = '_' . $name;
   global ${$Jim};
   ${$Jim} = $cb;
   
   $twig->addFunction(new \Twig\TwigFunction($name, $cb));
}

foreach (glob('mod/functions/*.php') as $file) include $file;

function findKey(array $array, string $key) {
   for ($i = 0, $j = count($array); $i < $j; $i++) {
      if (isset($array[$i]->{$key})) {
         return $array[$i]->{$key};
      }
   }
}

$twig->addGlobal('yt', $yt);