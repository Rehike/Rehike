<?php
ob_start();
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/vendor/autoload.php');

//require($root . '/signin/signincore2.php');
//require($root . '/signin/sc2stub.php');
//$sc2 = new YtSigninCore();

$visitor = null;
if (isset($_COOKIE['VISITOR_INFO1_LIVE'])) {
   $visitor = $_COOKIE['VISITOR_INFO1_LIVE'];
}

$visitor = 'QRe0LmmEJyY'; // DEBUG

$templateRoot = '/template/hitchhiker';

$yt = (object) [];
$template = '';

require($root . '/router.php');

$twigLoader = new \Twig\Loader\FilesystemLoader(
   $root . $templateRoot
);

$twig = new \Twig\Environment($twigLoader, [
   'debug' => true
]);

$getText = new \Twig\TwigFunction('getText', function ($obj) {
   if (isset($obj->runs)) {
      //return '';
      $runs = $obj->runs;
      $response = '';
      for ($i = 0, $j = count($runs); $i < $j; $i++) {
         $response .= $runs[$i]->text;
      }
      return $response;
   } else if (isset($obj->simpleText)) {
      return $obj->simpleText;
   } else {
      return '';
   }
});

$resolveBrowseIdUrl = new \Twig\TwigFunction('resolveBrowseIdUrl', function ($id) {
   if (!isset($id)) {
      return "";
   }
   $url = "";
   $idType = substr($id, 0, 2);
   $id = substr($id, 2, strlen($id));
   switch ($idType) {
      case 'UC':
         $url = '/channel/UC' . $id;
         break;
      case 'FE':
      default:
         $url = '/feed/' . $id;
         break;
   }
   return $url;
});



if (isset($_GET['spf'])) {
    header("Content-Type: application/json");
    $yt->spf = true;
    $yt->spf_url = preg_replace('/.spf='.$_GET['spf'].'/', '', $_SERVER['REQUEST_URI']);
}



$twig->addGlobal('yt', $yt);
$twig->addFunction($getText);
$twig->addFunction($resolveBrowseIdUrl);

echo $twig->render($template . '.twig', [$yt]);

ob_end_flush();