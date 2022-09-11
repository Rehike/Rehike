<?php
require "modules/Rehike/Constants.php";

// Include the Composer and Rehike autoloaders, respectively.
require "vendor/autoload.php";
require "modules/rehikeAutoloader.php";

// Does not properly autoload (this should be fixed)
include "modules/YukisCoffee/GetPropertyAtPath.php";

use Rehike\ControllerV2\Core as ControllerV2;
use Rehike\TemplateManager;

TemplateManager::registerGlobalState($yt);

// Pass resource constants to the templater
TemplateManager::addGlobal('ytConstants', $ytConstants);
TemplateManager::addGlobal('PIXEL', $ytConstants->pixelGif);

// Load general i18n files
use Rehike\i18n;
i18n::setDefaultLanguage("en");
i18n::newNamespace("main/regex")->registerFromFolder("i18n/regex");
i18n::newNamespace("main/misc")->registerFromFolder("i18n/misc");
i18n::newNamespace("main/guide")->registerFromFolder("i18n/guide");

// Controller V2 init
ControllerV2::registerStateVariable($yt);
ControllerV2::setRedirectHandler(require "modules/spfRedirectHandler.php");

// Player init
use \Rehike\Player\PlayerCore;
PlayerCore::configure([
   "cacheMaxTime" => 18000, // 5 hours in seconds
   "cacheDestDir" => "cache",
   "cacheDestName" => "player_cache" // .json
]);

// Parse user preferences as stored by the YouTube application.
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

// Aubrey added this to include timestamp in ytGlobalJsConfig.twig,
// should be moved
$yt -> version = json_decode(file_get_contents($root . "/.version"));

// Import all template functions
foreach (glob('modules/template_functions/*.php') as $file) include $file;

// should be moved
TemplateManager::addFunction('http_response_code', function($code) {
   http_response_code($code);
});
// should be moved
TemplateManager::addFilter("base64_encode", function($a){
   return base64_encode($a);
});