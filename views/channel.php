<?php
if (isset($routerUrl->path[2])) {
    switch($routerUrl->path[2]) {
        case "":
        case "featured":
            include($root . "/views/channel/featured.php");
            break;
        case "videos":
            include($root . "/views/channel/videos.php");
            break;
        case "playlists":
            include($root . "/views/channel/playlists.php");
            break;
        case "community":
            include($root . "/views/channel/community.php");
            break;
        case "channels":
            include($root . "/views/channel/channels.php");
            break;
        case "about":
            include($root . "/views/channel/about.php");
            break;
        case "search":
            include($root . "/views/channel/search.php");
            break;
        default:
            header("Location: /");
            break;
    }
} else {
    include($root . "/views/channel/featured.php");
}
