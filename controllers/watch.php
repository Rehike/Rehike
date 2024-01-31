<?php
use Rehike\Controller\core\NirvanaController;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Com\Youtube\Innertube\Request\NextRequestParams;
use Com\Youtube\Innertube\Request\NextRequestParams\UnknownThing;

use Rehike\Network;
use Rehike\Async\Promise;

use Rehike\Util\Base64Url;
use Rehike\ConfigManager\Config;
use Rehike\Util\WatchUtils;
use Rehike\Util\ExtractUtils;

use Rehike\Model\Watch\WatchModel;
use YukisCoffee\CoffeeRequest\Exception\GeneralException;

/**
 * Controller for the watch page.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends NirvanaController {
    public string $template = 'watch';
    
    // Watch should only load the guide after everything else is done.
    protected bool $delayLoadGuide = true;

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $this->useJsModule("www/watch");

        // invalid request redirect
        if (!isset($_GET['v']))
        {
            header('Location: /');
            die();
        }

        /*
         * Set theater mode state.
         */
        if ("1" == @$_COOKIE['wide']) 
        {
            $yt->theaterMode = $_COOKIE['wide'];
        } 
        else 
        {
            $yt->theaterMode = "0";
            $_COOKIE['wide'] = "0";
        }

        // begin request
        $yt->videoId = $request->params->v;
        $yt->playlistId = $request->params->list ?? null;

        // What the fuck.
        $yt->playlistIndex = (string) ((int) ($request->params->index ?? '1'));

        // ?!?!?!?!?!?!?!?!?!?!?!?!?!?!?!?!?!?!?!?!?!?
        if (0 == $yt->playlistIndex) $yt->playlistIndex = 1;

        // Used by InnerTube in some cases for player-specific parameters.
        $yt->playerParams = $request->params->pp ?? null;

        // Common parameters to be used for both the next API and player API.
        $sharedRequestParams = [
            'videoId' => $yt->videoId
        ];

        // Content restriction
        if (isset($_GET["has_verified"]) && ($_GET["has_verified"] == "1" || $_GET["has_verified"] == true) or false === Config::getConfigProp("experiments.encryptedStreamsDO_NOT_USE_UNLESS_YOU_KNOW_WHAT_YOU_ARE_DOING"))
        {
            $sharedRequestParams += ["racyCheckOk" => true];
            $sharedRequestParams += ["contentCheckOk" => true];
        }

        // Defines parameters to be sent only to the next (watch data) API.
        // Required for LC link implementation.
        $nextOnlyParams = [];

        $lc = $request->params->lc ?? $request->params->google_comment_id ?? null;

        /*
         * Generate LC (linked comment) param.
         * 
         * This is handled by InnerTube as a next parameter, which is base64-
         * encoded as with similar params. As such, it needs to be encoded like
         * any other protobuf/base64 parameter (ugly).
         * 
         * LC itself simply modifies the comment continuation that's provided
         * to link to a specific comment.
         */
        if (isset($lc))
        {
            $param = new NextRequestParams();
            
            // I don't know if this is needed, but I want to include it
            // anyways.
            $param->setUnknownThing(new UnknownThing(["a" => 0]));

            $param->setLinkedCommentId($lc);

            $nextOnlyParams += [
                "params" => Base64Url::encode($param->serializeToString())
            ];
        }

        if (!is_null($yt->playlistId))
        {
            $sharedRequestParams['playlistId'] = $yt->playlistId;
            $sharedRequestParams['playlistIndex'] = $yt->playlistIndex;
        }

        // TODO (kirasicecreamm): Clean up this algo, make better
        if (isset($request->params->t))
        {
            preg_match_all("/\d{1,6}/", $request->params->t, $times);
            $times = $times[0];
            if (count($times) == 1)
            {
                // before you whine "waaahh use case" I CAN'T IT BREAKS IT FOR NO FUCKING REASON, if you wanna make this better, go ahead
                $startTime = (int) $times[0];
            } 
            else if (count($times) == 2)
            {
                $startTime = ((int) $times[0] * 60) + (int) $times[0];
            } 
            else if (count($times) == 3)
            {
                $startTime = ((int) $times[0] * 3600) + ((int) $times[1] * 60) + (int) $times[2];
            } 
            else
            {
                $startTime = 0;
            }
        }

        \Rehike\Profiler::start("watch_requests");
        // Makes the main watch request.
        $nextRequest = Network::innertubeRequest(
            "next",
            $sharedRequestParams + $nextOnlyParams
        );

        // Unlike Polymer, Hitchhiker had all of the player data already
        // available in the initial response. So an additional player request
        // is used.
        if (false === Config::getConfigProp("experiments.encryptedStreamsDO_NOT_USE_UNLESS_YOU_KNOW_WHAT_YOU_ARE_DOING")){
            $playerRequest = Network::innertubeRequest(
                "player",
                [
                    "playbackContext" => [
                        'contentPlaybackContext' => (object) [
                            'autoCaptionsDefaultOn' => false,
                            'autonavState' => 'STATE_OFF',
                            'html5Preference' => 'HTML5_PREF_WANTS',
                            'lactMilliseconds' => '13407',
                            'mdxContext' => (object) [],
                            'playerHeightPixels' => 1080,
                            'playerWidthPixels' => 1920,
                            'signatureTimestamp' => $yt->playerConfig->signatureTimestamp
                        ]   
                    ],
                    "startTimeSecs" => $startTime ?? 0,
                    "params" => 'CgIQBg=='
                ] + $sharedRequestParams,
                    clientName: "ANDROID",
                    clientVersion: "16.02.00"
            );
            $storyboardRequest = Network::innertubeRequest(
                "player",
                [
                    "playbackContext" => [
                        'contentPlaybackContext' => (object) [
                            'autoCaptionsDefaultOn' => false,
                            'autonavState' => 'STATE_OFF',
                            'html5Preference' => 'HTML5_PREF_WANTS',
                            'lactMilliseconds' => '13407',
                            'mdxContext' => (object) [],
                            'playerHeightPixels' => 1080,
                            'playerWidthPixels' => 1920,
                            'signatureTimestamp' => $yt->playerConfig->signatureTimestamp
                        ]
                    ],
                    "startTimeSecs" => $startTime ?? 0
                ] + $sharedRequestParams,
                clientName: "XBOXONEGUIDE",
                clientVersion: "1.0"
            );
        }
        else{
            $playerRequest = Network::innertubeRequest(
                "player",
                [
                    "playbackContext" => [
                        'contentPlaybackContext' => (object) [
                            'autoCaptionsDefaultOn' => false,
                            'autonavState' => 'STATE_OFF',
                            'html5Preference' => 'HTML5_PREF_WANTS',
                            'lactMilliseconds' => '13407',
                            'mdxContext' => (object) [],
                            'playerHeightPixels' => 1080,
                            'playerWidthPixels' => 1920,
                            'signatureTimestamp' => $yt->playerConfig->signatureTimestamp
                        ]   
                    ],
                    "startTimeSecs" => $startTime ?? 0,
                    "params" => $yt->playerParams
                ] + $sharedRequestParams
            );
            $storyboardRequest = new Promise(fn($r) => $r());
        }

        /**
         * Determine whether or not to use the Return YouTube Dislike
         * API to return dislikes. Retrieved from application config.
         */
        if (true === Config::getConfigProp("appearance.useRyd"))
        {
            $rydUrl = "https://returnyoutubedislikeapi.com/votes?videoId=" . $yt->videoId;

            $rydRequest = Network::urlRequest($rydUrl);
        }
        else
        {
            // If RYD is disabled, then send a void Promise that instantly
            // resolves itself.
            $rydRequest = new Promise(fn($r) => $r());
        }

        Promise::all([
            "next"       => $nextRequest,
            "player"     => $playerRequest,
            "ryd"        => $rydRequest,
            "storyboard" => $storyboardRequest
        ])->then(function ($responses) use ($yt) {
            \Rehike\Profiler::end("watch_requests");
            $nextResponse = $responses["next"]->getJson();
            $playerResponse = $responses["player"]->getJson();
            if (false === Config::getConfigProp("experiments.encryptedStreamsDO_NOT_USE_UNLESS_YOU_KNOW_WHAT_YOU_ARE_DOING")){
                $storyboardResponse = $responses["storyboard"]->getJson();
                $playerResponse->storyboards = $storyboardResponse->storyboards;
            }
            try
            {
                $rydResponse = $responses["ryd"]?->getJson() ?? (object)[];
            }
            catch (GeneralException $e)
            {
                $rydResponse = (object) [];
            }
			
			$renderer = (object) [];
			
			$renderer->invideoUrl = "//www.youtube.com/annotations_invideo?video_id=".$yt->videoId;
			$renderer->loadPolicy = "ALWAYS";
			$renderer->allowInPlaceSwitch = false;
			
			$playerResponse->annotations = array((object) []);
			$playerResponse->annotations[0]->playerAnnotationsUrlsRenderer = $renderer;

            if (Config::getConfigProp("appearance.enableAdblock"))
            {
                // This may not be needed any longer, but manually removing ads
                // has been historically required as adblockers no longer have
                // the Hitchhiker-era rules.
                $this->removeAds($playerResponse);
            }

             // Push these over to the global object.
             $yt->playerResponse = $playerResponse;
             $yt->watchNextResponse = $nextResponse;

            \Rehike\Profiler::start("modelbake");

            WatchModel::bake(
                yt:      $yt,
                data:    $nextResponse,
                videoId: $yt->videoId,
                rydData: $rydResponse
            )->then(function ($watchModelResult) use ($yt) {
                $yt->page = $watchModelResult;

                if (isset($yt->page->title))
                {
                    $this->setTitle($yt->page->title);
                }
            });
            
            \Rehike\Profiler::end("modelbake");
        });
    }

    /**
     * Handles SPF requests.
     * 
     * Specifically, this binds the player data to the SPF data in order to
     * refresh the player on the client-side.
     */
    public function tryGetSpfData(?object &$data): bool
    {
        $yt = $this->yt;

        $data = null;

        if ("PLAYER_2014" != Config::getConfigProp("appearance.playerChoice") && "PLAYER_2015" != Config::getConfigProp("appearance.playerChoice") && "PLAYER_2015_NEW" != Config::getConfigProp("appearance.playerChoice"))
        {
            if (isset($yt->playerResponse))
            {
                $data = (object) [
                    'swfcfg' => (object) [
                        'args' => (object) [
                            'raw_player_response' => null,
                            'raw_watch_next_response' => null
                        ]
                    ]
                ];

                $data->swfcfg->args->raw_player_response = $yt->playerResponse;
                $data->swfcfg->args->raw_watch_next_response = $yt->watchNextResponse;
        
                if (isset($yt->page->playlist))
                {
                    $data->swfcfg->args->is_listed = '1';
                    $data->swfcfg->args->list = $yt->playlistId;
                    $data->swfcfg->args->videoId = $yt->videoId;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Remove ads from a player response if they exist.
     */
    protected function removeAds(object $playerResponse): void
    {
        if (isset($playerResponse->playerAds))
            unset($playerResponse->playerAds);

        if (isset($playerResponse->adPlacements))
            unset($playerResponse->adPlacements);
    }
};
