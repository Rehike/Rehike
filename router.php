<?php
require($root . '/mod/routerBase.php');

if (isset($_GET["enable_polymer"]) && $_GET["enable_polymer"] == "1") {
    include($root . "/simplefunnel.php");
    die();
}

switch ($routerUrl->path[0]) {
    case '':
        include('views/homepage.php');
        break;
    case 'watch':
        include('views/watch.php');
        break;
    case 'shorts': // redirect to watch
        ob_end_clean();
        header ('Location: /watch?v=' . $routerUrl->path[1]);
        exit();
        break;
    case 'user':
    case 'channel':
    case 'c':
        include('views/channel.php');
        break;
    case 'results':
        include('views/results.php');
        break;
    case 'forcefatal':
        $template = 'sdsadasds';
        break;
    case 'attribution':
        include('views/attribution.php');
        break;
    case 'debug':
        include('debug.php');
        break;
    case 'redirect':
        // temporary logic?
        // youtube has a redirect confirmation page in some cases
        // TODO: research
        ob_end_clean();
        $newLocation = urldecode(($_GET['q'] ?? ''));
        header('Location: ' . $newLocation);
        exit();
        break;
    case 'api':
    case 'youtubei':
    case 's':
    case '404':
        include('simplefunnel.php');
        die();
        break;
    case 'watch_fragments2_ajax':
        include('views/ajax/watch_fragments2.php');
        break;
    case 'internal': // forward to internal router
        include('internal/internalRouter.php');
        break;
    default:
        $template = '404';
        break;
}