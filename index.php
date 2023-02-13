<?php
ob_start();
$root = $_SERVER['DOCUMENT_ROOT'];
set_include_path($root);

// Correct the working directory if the currently used one differs from the
// true root.
if (
    (DIRECTORY_SEPARATOR == "\\" 
        ? strtr($_SERVER["DOCUMENT_ROOT"], "\\", "/")
        : $_SERVER["DOCUMENT_ROOT"])
    != (DIRECTORY_SEPARATOR == "\\" 
        ? ($corrected = explode("/index.php", strtr(__FILE__, "\\", "/"))[0])
        : ($corrected = explode("/index.php", __FILE__)[0]))
)
{
    $_SERVER["DOCUMENT_ROOT"] = $corrected;
    $root = $corrected;
    chdir($corrected);
    set_include_path($corrected);
}

// Ungodly global variable that connects everything together.
// Do not touch, this needs to be carefully moved for clean up.
// Think of it like nuclear waste :P
// love taniko
$yt = (object) [];

$ytConstants = include "resource_constants.php";
include "fatalHandler.php";
include "boot.php";

// TODO (kirasicecreamm): Clean this up.
if (isset($_COOKIE['VISITOR_INFO1_LIVE'])) {
    $visitor = $_COOKIE['VISITOR_INFO1_LIVE'];
} else {
    $coffee = new \YukisCoffee\CoffeeRequest\CoffeeRequest;
    $response = $coffee->request("https://www.youtube.com/");
    preg_match("/ytcfg\.set\(({.*?})\);/", $response, $matches);
    $ytcfg = json_decode(@$matches[1]);
    $visitor = $ytcfg->INNERTUBE_CONTEXT->client->visitorData ?? "";
    $visitor = \Rehike\Util\Base64Url::decode($visitor);
    $visitor = substr($visitor, 2);
    $visitor = explode("(", $visitor)[0];
    setcookie("VISITOR_INFO1_LIVE", $visitor);
}

// Load configuration
$rehikeConfig = Rehike\RehikeConfigManager::loadConfig();
$yt->config = $rehikeConfig;

Rehike\Debugger\Debugger::init($yt);

// Post boot events
Rehike\ContextManager::$visitorData = $visitor;
$yt->visitor = Rehike\InnertubeContext::genVisitorData(Rehike\ContextManager::$visitorData);

// * Set signin state
Rehike\Signin\AuthManager::use($yt);

// Load version information from the version service
// and push it to the global yt variable
// This can probably be improved in the future
\Rehike\Version\VersionController::init();
$yt->rehikeVersion = (object)\Rehike\Version\VersionController::$versionInfo;
$yt->rehikeVersion->semanticVersion = \Rehike\Version\VersionController::getVersion();

// Include the router for Controller v2 pages.
require "router.php";

Rehike\Debugger\Debugger::shutdown();