<?php
namespace Rehike\Controller;

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
    "/" => \Rehike\Controller\FeedPageController::class,
    "/feed/**" => \Rehike\Controller\FeedPageController::class,
    "/watch" => \Rehike\Controller\WatchPageController::class,
    "/user/**" => \Rehike\Controller\ChannelPageController::class,
    "/channel/**" => \Rehike\Controller\ChannelPageController::class,
    "/c/**" => \Rehike\Controller\ChannelPageController::class,
    "/live_chat" => \Rehike\Controller\special\GetLiveChatController::class,
    "/live_chat_replay" => \Rehike\Controller\special\GetLiveChatController::class,
    "/feed_ajax" => \Rehike\Controller\ajax\FeedAjaxController::class,
    "/results" => \Rehike\Controller\ResultsPageController::class,
    "/playlist" => \Rehike\Controller\PlaylistPageController::class,
    "/oops" => \Rehike\Controller\OopsPageController::class,
    "/related_ajax" => \Rehike\Controller\ajax\RelatedAjaxController::class,
    "/browse_ajax" => \Rehike\Controller\ajax\BrowseFragmentsController::class,
    "/addto_ajax" => \Rehike\Controller\ajax\AddtoFragmentsController::class,
    "/rehike/version" => \Rehike\Controller\rehike\RehikeVersionPageController::class,
    "/rehike/static/**" => \Rehike\Controller\rehike\StaticRouter::class,
    "/share_ajax" => \Rehike\Controller\ajax\ShareFragmentsAjax::class,
    "/picker_ajax" => \Rehike\Controller\ajax\PickerFragmentsAjax::class,
    "/attribution" => \Rehike\Controller\AttributionPageController::class,
    "/profile" => \Rehike\Controller\ProfileRedirectEndpointController::class,
    "/channel_switcher" => \Rehike\Controller\ChannelSwitcherPageController::class,
    "/rehike/config" => \Rehike\Controller\rehike\RehikeConfigPageController::class,
    "/rehike/config/**" => \Rehike\Controller\rehike\RehikeConfigPageController::class,
    "/rehike/ajax/git_ajax" => \Rehike\Controller\rehike\ajax\RehikeVersionGitAjaxController::class,
    "/rehike/server_info" => \Rehike\Controller\rehike\RehikeServerInfoPageController::class,
    "/rehike/extensions" => \Rehike\Controller\rehike\RehikeExtensionsPageController::class,
	"/html5" => \Rehike\Controller\Html5PageController::class,
	"/annotations_invideo" => \Rehike\Controller\ajax\AnnotationsInvideoController::class,
	"/get_video_metadata" => \Rehike\Controller\ajax\GetVideoMetadataController::class,
    "/player_204" => function() { exit(); },
    "default" => \Rehike\Controller\ChannelPageController::class
]);

Router::post([
    "/youtubei/v1/player" => \Rehike\Controller\special\InnertubePlayerProxyController::class,
    "/feed_ajax" => \Rehike\Controller\ajax\FeedAjaxController::class,
    "/browse_ajax" => \Rehike\Controller\ajax\BrowseFragmentsController::class,
    "/watch_fragments2_ajax" => \Rehike\Controller\ajax\WatchFragments2AjaxController::class,
    "/related_ajax" => \Rehike\Controller\ajax\RelatedAjaxController::class,
    "/playlist_video_ajax" => \Rehike\Controller\ajax\PlaylistVideoAjaxController::class,
    "/playlist_ajax" => \Rehike\Controller\ajax\PlaylistAjaxController::class,
    "/picker_ajax" => \Rehike\Controller\ajax\PickerFragmentsAjax::class,
    "/subscription_ajax" => \Rehike\Controller\ajax\SubscriptionAjaxController::class,
    "/service_ajax" => \Rehike\Controller\ajax\ServiceAjaxController::class,
    "/comment_service_ajax" => \Rehike\Controller\ajax\CommentServiceAjaxController::class,
    "/addto_ajax" => \Rehike\Controller\ajax\AddtoFragmentsController::class,
    "/live_events_reminders_ajax" => \Rehike\Controller\ajax\LiveEventsRemindersAjaxController::class,
    "/delegate_account_ajax" => \Rehike\Controller\ajax\DelegateAccountFragmentsController::class,
    "/rehike/update_config" => \Rehike\Controller\rehike\ajax\RehikeUpdateConfigRouter::class,
	"/annotations_invideo" => \Rehike\Controller\ajax\AnnotationsInvideoController::class,
	"/get_video_metadata" => \Rehike\Controller\ajax\GetVideoMetadataController::class,
    "/player_204" => function() { exit(); },
    
    // The default route is the channel controller. This is so we can handle
    // username shorthand URLs (i.e. /PewDiePie -> /user/PewDiePie)
    // Channel controller is responsible for showing the 404 page if lookup
    // fails.
    "default" => \Rehike\Controller\ChannelPageController::class
]);
