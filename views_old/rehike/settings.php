<?php
    if (isset($routerUrl->path[1])) {
        switch($routerUrl->path[1]) {
            case 'general':
            case '':
            default:
                include('views/rehike/settings/general.php');
                break;
        }
    } else {
        
    }