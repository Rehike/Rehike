<?php
use Rehike\ControllerV2\Router;

// Passed through the Rehike server. These simply request the YouTube server
// directly.
Router::funnel([
    "/api/*",
    "~/youtubei/v1/player*", // exclude player
    "/youtubei/*", // all youtubei/ except player, which is proxied elsewhere
    "/s/*",
    "/embed/*",
    "/yts/*",
    "/favicon.ico",
    "/subscribe_embed",
    "/login",
    "/logout",
    "/signin",
    "/upload",
    "/t/*",
    "/howyoutubeworks/*",
    "/create_channel",
    "/new",
    "/supported_browsers",
    "/getAccountSwitcherEndpoint",
    "/channel_image_upload/*",
    "/account",
    "/account_notifications",
    "/account_playback",
    "/account_privacy",
    "/account_sharing",
    "/account_billing",
    "/account_advanced",
    "/account_transfer_channel",
    "/features",
    "/testtube",
    "/t/terms",
    "/iframe_api",
    "/signin_prompt",
    "/post/*",
    "/feeds/*",
    "/img/*",
    "/attribution_link*"
]);

Router::redirect([
    "/watch/(*)" => "/watch?v=$1",
    "/shorts/(*)" => function($request) {
        if (isset($request->path[1]))
            return "/watch?v=" . $request->path[1];
        else
            return "/watch";
    },
    "/live/(*)" => "/watch?v=$1",
    "/hashtag/(*)" => "/results?search_query=$1",
    "/feed/what_to_watch/**" => "/",
    "/source/(*)" => function($request) {
        if (isset($request->path[1]))
            return "/attribution?v=" . $request->path[1];
        else
            return "/attribution";
    },
    "/redirect(/|?)*" => function($request) {
        if (isset($request->params->q))
            return urldecode($request->params->q);
    },
    "/feed/library" => "/profile",
    "/feed/you" => "/profile",
    "/subscription_manager" => "/feed/channels",
    "/rehike/settings" => "/rehike/config",
    "/subscription_center?(*)" => function($request) {
        if ($user = @$request->params->add_user)
            return "/user/$user?sub_confirmation=1";
        else if ($user = @$request->params->add_user_id)
            return "/channel/$user?sub_confirmation=1";
    }
]);

Router::get([
    "/" => "feed",
    "/feed/**" => "feed",
    "/watch" => "watch",
    "/user/**" => "channel",
    "/channel/**" => "channel",
    "/c/**" => "channel",
    "/live_chat" => "special/get_live_chat",
    "/live_chat_replay" => "special/get_live_chat",
    "/feed_ajax" => "ajax/feed",
    "/results" => "results",
    "/playlist" => "playlist",
    "/oops" => "oops",
    "/related_ajax" => "ajax/related",
    "/browse_ajax" => "ajax/browse",
    "/addto_ajax" => "ajax/addto",
    "/rehike/version" => "rehike/version",
    "/rehike/static/**" => "rehike/static_router",
    "/share_ajax" => "ajax/share",
    "/attribution" => "attribution",
    "/profile" => "profile",
    "/channel_switcher" => "channel_switcher",
    "/rehike/config" => "rehike/config",
    "/rehike/config/**" => "rehike/config",
    "/rehike/ajax/git_ajax" => "rehike/ajax/git_ajax",
    "/rehike/server_info" => "rehike/server_info",
    "default" => "channel"
]);

Router::post([
    "/youtubei/v1/player" => "special/innertube_player_proxy",
    "/feed_ajax" => "ajax/feed",
    "/browse_ajax" => "ajax/browse",
    "/watch_fragments2_ajax" => "ajax/watch_fragments2",
    "/related_ajax" => "ajax/related",
    "/playlist_video_ajax" => "ajax/playlist_video",
    "/playlist_ajax" => "ajax/playlist",
    "/subscription_ajax" => "ajax/subscription",
    "/service_ajax" => "ajax/service",
    "/comment_service_ajax" => "ajax/comment_service",
    "/addto_ajax" => "ajax/addto",
    "/live_events_reminders_ajax" => "ajax/live_events_reminders",
    "/delegate_account_ajax" => "ajax/delegate_account",
    "/rehike/update_config" => "rehike/update_config",
    "default" => "channel"
]);
