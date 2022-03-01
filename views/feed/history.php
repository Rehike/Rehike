<?php
    if (isset($routerUrl->path[2])) {
        switch($routerUrl->path[2]) {
            case '':
                include('views/feed/history/history.php');
                break;
            case 'search_history':
                include('views/feed/history/search_history.php');
                break;
            case 'comment_history':
                include('views/feed/history/comment_history.php');
                break;
            default:
                $template = "404";
                break;
        }
    } else {
        include('views/feed/history/history.php');
    }