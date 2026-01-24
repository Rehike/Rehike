<?php
namespace Rehike\Controller;

use Rehike\ControllerV2\Router;

// Passed through the Rehike server. These simply request the YouTube server
// directly.
Router::funnel([
    "/api/*",
    "~/youtubei/v1/player", // exclude player
    "/youtubei/v1/player/heartbeat",
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
    "/channel_image_upload*",
    "/features",
    "/testtube",
    "/t/terms",
    "/iframe_api",
    "/signin_prompt",
    "/post/*",
    "/feeds/*",
    "/img/*",
    "/attribution_link*",
	"/yt*", // this seems to be the username of a channel but YouTube reserved it for footer links
	"/about/"
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
    "/" => FeedPageController::class,
    "/feed/**" => FeedPageController::class,
    "/watch" => WatchPageController::class,
    "/user/**" => ChannelPageController::class,
    "/channel/**" => ChannelPageController::class,
    "/c/**" => ChannelPageController::class,
    "/live_chat" => special\GetLiveChatController::class,
    "/live_chat_replay" => special\GetLiveChatController::class,
    "/feed_ajax" => ajax\FeedAjaxController::class,
    "/results" => ResultsPageController::class,
    "/playlist" => PlaylistPageController::class,
    "/oops" => OopsPageController::class,
    "/related_ajax" => ajax\RelatedAjaxController::class,
    "/browse_ajax" => ajax\BrowseFragmentsController::class,
    "/addto_ajax" => ajax\AddtoFragmentsController::class,
    "/rehike/version" => rehike\RehikeVersionPageController::class,
    "/rehike/static/**" => rehike\StaticRouter::class,
    "/share_ajax" => ajax\ShareFragmentsAjax::class,
    "/picker_ajax" => ajax\PickerFragmentsAjax::class,
    "/attribution" => AttributionPageController::class,
    "/profile" => ProfileRedirectEndpointController::class,
    "/channel_switcher" => ChannelSwitcherPageController::class,
    "/rehike/config" => rehike\RehikeConfigPageController::class,
    "/rehike/config/**" => rehike\RehikeConfigPageController::class,
    "/rehike/ajax/git_ajax" => rehike\ajax\RehikeVersionGitAjaxController::class,
    "/rehike/server_info" => rehike\RehikeServerInfoPageController::class,
    "/rehike/extensions" => rehike\RehikeExtensionsPageController::class,
	"/html5" => Html5PageController::class,
	"/annotations_invideo" => ajax\AnnotationsInvideoController::class,
	"/get_video_metadata" => ajax\GetVideoMetadataController::class,
    "/player_204" => function() { exit(); },
    "default" => ChannelPageController::class
]);

Router::post([
    "/youtubei/v1/player" => special\InnertubePlayerProxyController::class,
    "/feed_ajax" => ajax\FeedAjaxController::class,
    "/browse_ajax" => ajax\BrowseFragmentsController::class,
    "/watch_fragments2_ajax" => ajax\WatchFragments2AjaxController::class,
    "/related_ajax" => ajax\RelatedAjaxController::class,
    "/playlist_video_ajax" => ajax\PlaylistVideoAjaxController::class,
    "/playlist_ajax" => ajax\PlaylistAjaxController::class,
    "/picker_ajax" => ajax\PickerFragmentsAjax::class,
    "/subscription_ajax" => ajax\SubscriptionAjaxController::class,
    "/service_ajax" => ajax\ServiceAjaxController::class,
    "/comment_service_ajax" => ajax\CommentServiceAjaxController::class,
    "/addto_ajax" => ajax\AddtoFragmentsController::class,
    "/live_events_reminders_ajax" => ajax\LiveEventsRemindersAjaxController::class,
    "/delegate_account_ajax" => ajax\DelegateAccountFragmentsController::class,
    "/rehike/update_config" => rehike\ajax\RehikeUpdateConfigRouter::class,
	"/annotations_invideo" => ajax\AnnotationsInvideoController::class,
	"/get_video_metadata" => ajax\GetVideoMetadataController::class,
    "/player_204" => function() { exit(); },
    
    // The default route is the channel controller. This is so we can handle
    // username shorthand URLs (i.e. /PewDiePie -> /user/PewDiePie)
    // Channel controller is responsible for showing the 404 page if lookup
    // fails.
    "default" => ChannelPageController::class
]);
