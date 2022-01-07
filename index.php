<?php
ob_start();
$root = $_SERVER['DOCUMENT_ROOT'];
set_include_path($root);

$visitor = null;
if (isset($_COOKIE['VISITOR_INFO1_LIVE'])) {
   $visitor = $_COOKIE['VISITOR_INFO1_LIVE'];
}

$visitor = 'QRe0LmmEJyY'; // DEBUG

$templateRoot = '/template/hitchhiker';

$yt = (object) [];
$template = '';

include ('boot.php');
include ('defaultExperiments.php');
include ('resourceConstants.php');

// differentiates pages
require ('router.php');

// initialises twig
include ('fatalHandler.php');

// lazy spf check
if (isset($_GET['spf'])) {
    $yt->spf = true;
    $yt->spf_url = preg_replace('/.spf='.$_GET['spf'].'/', '', $_SERVER['REQUEST_URI']);
}

$yt->spfEnabled = false; // DEBUG
echo $twig->render($template . '.twig', [$yt]);

ob_end_flush();