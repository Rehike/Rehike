<?php
require($root . '/lib/routerBase.php');

switch ($routerUrl->path[0]) {
    case '':
        include($root . '/views/homepage.php');
        break;
    case 'watch':
        include($root . '/views/watch.php');
        break;
    case 'debug':
        include($root . '/debug.php');
        break;
    default:
        $template = '404';
    break;
}