<?php
require($root . '/mod/routerBase.php');

switch ($routerUrl->path[0]) {
    case '':
        include('views/homepage.php');
        break;
    case 'watch':
        include('views/watch.php');
        break;
    case 'shorts': // redirect to watch
        ob_end_clean();
        ob_start();
        header ('Location: /watch?v=' . $routerUrl->path[1]);
        ob_end_flush();
        exit();
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
    default:
        $template = '404';
        break;
}