<?php
require($root . '/modules/routerBase.php');

if (isset($_GET["enable_polymer"]) && $_GET["enable_polymer"] == "1") {
    include($root . "/simplefunnel.php");
    die();
}

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
        case 'watch':
            include('controllers/watch.php');
            break;
        case 'user':
        case 'channel':
        case 'c':
            include('controllers/channel.php');
            break;
        case 'results':
            include('controllers/results.php');
            break;
        case 'attribution':
            include('controllers/attribution.php');
            break;
        case 'feed':
            if(isset($routerUrl->path[1])) {
                switch ($routerUrl->path[1]) {
                    case 'what_to_watch':
                        header('Location: /');
                        break;
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
     * "Business" definitions (typically API/static resources)
     */
        case 'api':
        case 'youtubei':
        case 's':
        case 'embed':
        case 'yts':
            include('simplefunnel.php');
            die();
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
    /**
     * Redirect definitions
     */
        case 'shorts': // redirect to watch
            ob_end_clean();
            header ('Location: /watch?v=' . $routerUrl->path[1]);
            exit();
            break;
        case 'hashtag':
            if (isset($routerUrl->path[1])) {
                header("Location: /results?search_query=" . $routerUrl->path[1]);
            } else {
                $template = 'error/404';
            }
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
    default:
        $template = 'error/404';
        break;
}

require "temp_cv1_end.php";