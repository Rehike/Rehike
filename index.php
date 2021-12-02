<?php
ob_start();
$root = $_SERVER['DOCUMENT_ROOT'];

$visitor = null;
if (isset($_COOKIE['VISITOR_INFO1_LIVE'])) {
   $visitor = $_COOKIE['VISITOR_INFO1_LIVE'];
}

$visitor = 'QRe0LmmEJyY'; // DEBUG

$templateRoot = '/template/hitchhiker';

$yt = (object) [];
$template = '';

// differentiates pages
require($root . '/router.php');

// initialises twig
include($root . '/boot.php');

if (isset($_GET['spf'])) {
    $yt->spf = true;
    $yt->spf_url = preg_replace('/.spf='.$_GET['spf'].'/', '', $_SERVER['REQUEST_URI']);
}

echo $twig->render($template . '.twig', [$yt]);

ob_end_flush();