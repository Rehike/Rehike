<?php
require($root . '/mod/routerBase.php');

if (isset($_GET["enable_polymer"]) && $_GET["enable_polymer"] == "1") {
    include($root . "/simplefunnel.php");
    die();
}

switch ($routerUrl->path[0]) {
    case '':
        if ($yt->experiment->webV2Home) {
            include('views/feed/what_to_watch_v2.php');
        } else {
            include('views/feed/what_to_watch.php');
        }
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
    case 'embed':
    case '404':
    case 'yts':
        include('simplefunnel.php');
        die();
        break;
    case 'watch_fragments2_ajax':
        include('views/ajax/watch_fragments2.php');
        break;
    case "comment_service_ajax":
        include "views/ajax/comment_service.php";
        break;
    case "share_ajax":
        include "views/ajax/share.php";
        break;
    case "browse_ajax":
        include "views/ajax/browse.php";
        break;
    case "related_ajax":
        include "views/ajax/related.php";
        break;
    case 'internal': // forward to internal router
        include('internal/internalRouter.php');
        break;
    case 'settings':
        include('views/rehike/settings.php');
        break;
    case 'feed':
        if(isset($routerUrl->path[1])) {
            switch ($routerUrl->path[1]) {
                case 'what_to_watch':
                    header('Location: /');
                    break;
                case 'trending':
                    include('views/feed/trending.php');
                    break;
                case 'history':
                    include('views/feed/history.php');
                    break;
                case 'guide_builder':
                    include('views/feed/guide_builder.php');
                    break;
                default:
                    $template = 'error/404';
                    break;
            }
        } else {
            $template = 'error/404';
        }
        break;
    case 'pb':
        include('pbtest.php');
        die();
        break;
    case 'hashtag':
        if (isset($routerUrl->path[1])) {
            header("Location: /results?search_query=" . $routerUrl->path[1]);
        } else {
            $template = 'error/404';
        }
        break;
    default:
        $template = 'error/404';
        break;
}