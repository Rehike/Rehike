<?php
ob_start();
$root = $_SERVER['DOCUMENT_ROOT'];
set_include_path($root);

// TODO (kirasicecreamm): Clean this up.
if (isset($_COOKIE['VISITOR_INFO1_LIVE'])) {
    $visitor = $_COOKIE['VISITOR_INFO1_LIVE'];
} else {
    $visitor = '';
    setcookie("VISITOR_INFO1_LIVE", $visitor);
}

// Ungodly global variable that connects everything together.
// Do not touch, this needs to be carefully moved for clean up.
// Think of it like nuclear waste :P
// love taniko
$yt = (object) [];

include "resourceConstants.php";
include "fatalHandler.php";
include "boot.php";

// Load configuration
$rehikeConfig = Rehike\RehikeConfigManager::loadConfig();
$yt->config = $rehikeConfig;

Rehike\Debugger\Debugger::init($yt);

// Post boot events
Rehike\ContextManager::$visitorData = $visitor;

// * Set signin state
Rehike\Signin\AuthManager::use($yt);

// Load version information from the version service
// and push it to the global yt variable
// This can probably be improved in the future
\Rehike\Version\VersionController::init();
$yt->rehikeVersion = (object)\Rehike\Version\VersionController::$versionInfo;
$yt->rehikeVersion->semanticVersion = \Rehike\Version\VersionController::getVersion();

// Include the router for Controller v2 pages.
if (!@$_GET["disable_cv2"] || in_array($_GET["disable_cv2"], ["false", "0"])) // debug
    require "router_v2.php";

/*
 * TEMPORARY BEHAVIOUR: Controller v1 router.
 * 
 * This should be removed once CV1 is fully deprecated. Until then, it's
 * okay to stay.
 * 
 * This will only be included if no matches were found in the CV2 router, 
 * and as such, no default case is included in there. Upon CV1 deprecation, 
 * unknown page behaviour should also be migrated to the default case of the
 * CV2 router.
 * 
 * Since many pages currently rely on this, it is not okay to remove.
 * 
 * love taniko
 */
if (!Rehike\ControllerV2\Core::$cv2HasBeenUsed)
    require "router.php";