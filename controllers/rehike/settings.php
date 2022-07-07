<?php
    if (isset($routerUrl->path[1])) {
        switch($routerUrl->path[1]) {
            case 'general':
                include('controllers/rehike/settings/general.php');
                break;
            case '':
            default:
                header("Location: /settings/general");
                break;
        }
    } else {
        header("Location: /settings/general");
    }