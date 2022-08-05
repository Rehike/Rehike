<?php
/**
 * ! DO NOT ADD ANYTHING HERE !
 * 
 * Instead, add any pages you want to add in router_v2.php
 */

require($root . '/modules/routerBase.php');

if (isset($_GET["enable_polymer"]) && $_GET["enable_polymer"] == "1") {
    include($root . "/simplefunnel.php");
    die();
}

\Rehike\Debugger\Debugger::expose();

switch ($routerUrl->path[0]) {
    /**
     * AJAX definitions
     */
        case 'watch_fragments2_ajax':
            include('controllers/ajax/watch_fragments2.php');
            break;
        case "comment_service_ajax":
            include "controllers/ajax/comment_service_old.php";
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
    default:
        http_response_code(404);
        $template = 'error/404';
        break;
}

require "temp_cv1_end.php";