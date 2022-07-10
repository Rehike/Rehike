<?php
require($root . '/modules/routerBase.php');

if (isset($_GET["enable_polymer"]) && $_GET["enable_polymer"] == "1") {
    include($root . "/simplefunnel.php");
    die();
}

\Rehike\Debugger\Debugger::expose();

switch ($routerUrl->path[0]) {
    /**
     * General page definitions
     */
        case '':
            if ($yt->config->useWebV2HomeEndpoint) {
                include('controllers/feed/what_to_watch_v2.php');
            } else {
                include('controllers/feed/what_to_watch.php');
            }
            break;
        case 'results':
            include('controllers/results.php');
            break;
        case 'feed':
            if(isset($routerUrl->path[1])) {
                switch ($routerUrl->path[1]) {
                    case 'trending':
                        include('controllers/feed/trending.php');
                        break;
                    case 'history':
                        include('controllers/feed/history.php');
                        break;
                    case 'guide_builder':
                        include('controllers/feed/guide_builder.php');
                        break;
                    default:
                        $template = 'error/404';
                        break;
                }
            } else {
                $template = 'error/404';
            }
            break;
    /**
     * AJAX definitions
     */
        case 'watch_fragments2_ajax':
            include('controllers/ajax/watch_fragments2.php');
            break;
        case "comment_service_ajax":
            include "controllers/ajax/comment_service.php";
            break;
        case "share_ajax":
            include "controllers/ajax/share.php";
            break;
        case "browse_ajax":
            include "controllers/ajax/browse.php";
            break;
        case "related_ajax":
            include "controllers/ajax/related.php";
            break;
    /**
     * Rehike special page definitions
     */
        case "rehike":
            switch ($routerUrl->path[1])
            {
                case 'settings':
                    include('controllers/rehike/settings.php');
                    break;
                case "version":
                    (include "controllers/rehike/version.php")::get($yt, $template);
                    break;
                case "static":
                    switch ($routerUrl->path[2])
                    {
                        case "logo.png":
                            header("Content-Type: image/png");
                            echo file_get_contents("static/version/logo.png");
                            exit();
                            break;
                        case "logo_small_grey.png":
                            header("Content-Type: image/png");
                            echo file_get_contents("static/version/logo_small_grey.png");
                            exit();
                            break;
                        case "branch_icon.png":
                            header("Content-Type: image/png");
                            echo file_get_contents("static/version/branch_icon.png");
                            exit();
                            break;
                    }
                    break;
            }
            break;
    /**
     * Test definitions
     */
        case 'pb':
            include('pbtest.php');
            die();
            break;
        case 'forcefatal':
            $template = 'sdsadasds';
            break;
    default:
        $template = 'error/404';
        break;
}

require "temp_cv1_end.php";