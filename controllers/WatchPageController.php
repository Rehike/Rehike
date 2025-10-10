<?php
namespace Rehike\Controller;

use Rehike\Controller\core\NirvanaController;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\ControllerV2\{
    IGetController,
    IGetControllerAsync,
    IPostController,
};

use Com\Youtube\Innertube\Request\NextRequestParams;
use Com\Youtube\Innertube\Request\NextRequestParams\UnknownThing;

use Rehike\Network;
use Rehike\Async\Promise;
use function Rehike\Async\async;

use Rehike\Util\Base64Url;
use Rehike\ConfigManager\Config;
use Rehike\Helper\WatchUtils;
use Rehike\Model\Common\MAlert;
use Rehike\Util\ExtractUtils;

use Rehike\Model\Watch\WatchBakery;

/**
 * Controller for the watch page.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class WatchPageController extends NirvanaController implements IGetControllerAsync
{
    public string $template = 'watch';
    
    // Watch should only load the guide after everything else is done.
    protected bool $delayLoadGuide = true;
    
    public function onGetAsync(): Promise
    {
        return async(function(){
        
        $yt = $this->yt;
        $request = $this->getRequest();
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
        if (isset($_GET["has_verified"]) && ($_GET["has_verified"] == "1" || $_GET["has_verified"] == true))
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

        // Parse complex &t parameter timestamps (such as "2h12m43s")
        if (isset($request->params->t))
        {
            $t = strtolower($request->params->t);
            $startTime = 0;
        
            if (preg_match_all('/(\d+)([hms]?)/', $t, $matches, PREG_SET_ORDER))
            {
                // 0th member of match is full string, so it's ignored.
                foreach ($matches as [, $value, $unit])
                {
                    $value = (int)$value;
                    switch ($unit)
                    {
                        case 'h':
                            $startTime += $value * 3600;
                            break;
                        case 'm':
                            $startTime += $value * 60;
                            break;
                        case 's':
                        case '':
                            $startTime += $value;
                            break;
                    }
                }
            }
        }

        \Rehike\Profiler::start("watch_requests");
        // Makes the main watch request.
        $nextRequest = Network::innertubeRequest(
            "next",
            $sharedRequestParams + $nextOnlyParams
        );
        
        $playerRequestClient = "WEB";
        $playerRequestClientVersion = "2.20230331.00.00";
        
        if (Config::getConfigProp("experiments.temp20240827_playerMode") == "USE_EMBEDDED_PLAYER_REQUEST")
        {
            $playerRequestClient = "WEB_EMBEDDED_PLAYER";
            $playerRequestClientVersion = "1.20230331.00.00";
        }
        
        // XXX: indentation level unchanged to avoid messing with history;
        // this code WILL be removed.
        if (Config::getConfigProp("experiments.temp20240827_playerMode") != "USE_EMBEDDED_PLAYER_DIRECTLY")
        {

        // Unlike Polymer, Hitchhiker had all of the player data already
        // available in the initial response. So an additional player request
        // is used.
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
            ] + $sharedRequestParams,
            $playerRequestClient,
            $playerRequestClientVersion
        );
        
        }
        else
        {
            $playerRequest = new Promise(fn($r) => $r(new class extends \stdClass
            {
                public function getJson(): object
                {
                    return (object)[];
                }
            }));
        }
        
        $storyboardRequest = new Promise(fn($r) => $r());

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

        $responses = yield Promise::all([
            "next"       => $nextRequest,
            "player"     => $playerRequest,
            "ryd"        => $rydRequest,
            "storyboard" => $storyboardRequest
        ]);
        
        // Block maintained to not affect Git history after refactor. That's all.
        {
            \Rehike\Profiler::end("watch_requests");
            $nextResponse = $responses["next"]->getJson();
            $playerResponse = $responses["player"]->getJson();

            try
            {
                $rydResponse = $responses["ryd"]?->getJson() ?? (object)[];
            }
            catch (\Exception $e)
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
            
            $watchBakery = new WatchBakery();

            $watchModelResult = yield $watchBakery->bake(
                yt:      $yt,
                data:    $nextResponse,
                videoId: $yt->videoId,
                rydData: $rydResponse
            );
            
            $yt->page = $watchModelResult;

            if (isset($yt->page->title))
            {
                $this->setTitle($yt->page->title);
            }
            
            \Rehike\Profiler::end("modelbake");
        }
        
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

        if (isset($playerResponse->adSlots))
            unset($playerResponse->adSlots);
    }
}