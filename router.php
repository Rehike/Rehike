<?php
use Rehike\ControllerV2\Router;

if (isset($_GET["enable_polymer"]) && $_GET["enable_polymer"] == "1") {
    include("simplefunnel.php");
    die();
}

Router::funnel([
    "/api/*",
    "/youtubei/*",
    "/s/*",
    "/embed/*",
    "/yts/*",
    "/favicon.ico",
    "/subscribe_embed",
    "/login",
    "/signin",
    "/upload"
]);

Router::redirect([
    "/watch/(*)" => "/watch?v=$1",
    "/shorts/(*)" => "/watch?v=$1",
    "/hashtag/(*)" => "/results?search_query=$1",
    "/feed/what_to_watch/**" => "/",
    // TODO: Redirect confirmation page?
    "/redirect(/|?)*" => function($request) {
        if (isset($request->params->q))
            return urldecode($request->params->q);
    }
]);

Router::get([
    "/" => "feed/what_to_watch",
    "/feed/trending" => "feed/trending",
    "/feed/history**" => "feed/history",
    "/feed/guide_builder" => "feed/guide_builder",
    "/debug_browse" => "debug_browse",
    "/watch" => "watch",
    "/user/**" => "channel",
    "/channel/**" => "channel",
    "/c/**" => "channel",
    "/live_chat" => "live_chat", //"special/get_live_chat",
    "/feed_ajax" => "ajax/feed",
    "/results" => "results",
    "/playlist" => "playlist",
    "/oops" => "oops",
    "/forcefatal" => "forcefatal",
    "/all_comments" => "all_comments",
    "/related_ajax" => "ajax/related",
    "/browse_ajax" => "ajax/browse",
    "/rehike/version" => "rehike/version",
    "/rehike/static/**" => "rehike/static_router",
    "default" => "404"
]);

Router::post([
    "/feed_ajax" => "ajax/feed",
    "/browse_ajax" => "ajax/browse",
    "/watch_fragments2_ajax" => "ajax/watch_fragments2",
    "/related_ajax" => "ajax/related",
    "/playlist_video_ajax" => "ajax/playlist_video",
    "/subscription_ajax" => "ajax/subscription",
    "/service_ajax" => "ajax/service",
    "/comment_service_ajax" => "ajax/comment_service",
    "/heart_ajax" => "ajax/heart"
]);