<?php
use Rehike\Controller\core\NirvanaController;

use Com\Youtube\Innertube\Request\NextRequestParams;
use Com\Youtube\Innertube\Request\NextRequestParams\UnknownThing;

use Rehike\Request;
use Rehike\Util\Base64Url;
use Rehike\ConfigManager\ConfigManager;
use Rehike\Util\WatchUtils;
use Rehike\Util\ExtractUtils;

//
// export
//
return new class extends NirvanaController {
    public $template = 'watch';

    public function onGet(&$yt, $request)
    {
        $this->useJsModule("www/watch");

        // invalid request redirect
        if (!isset($_GET['v'])) {
            header('Location: /');
            die();
        }

        // Somewhere along the way, we removed this code
        // during a codebase clean up and never reimplemented it.
        // It's about time I fix that lol
        if ("1" == @$_COOKIE['wide']) 
        {
            $yt -> theaterMode = $_COOKIE['wide'];
        } 
        else 
        {
            $yt -> theaterMode = "0";
            $_COOKIE['wide'] = "0";
        }

        // begin request
        $yt->videoId = $request->params->v;
        $yt->playlistId = $request->params->list ?? null;
        $yt->playlistIndex = (string) ((int) ($request->params->index ?? '1'));

        if (0 == $yt->playlistIndex) $yt->playlistIndex = 1;

        $yt->playerParams = $request->params->pp ?? null;

        $watchRequestParams = [
            'videoId' => $yt->videoId
        ];

        // Required for LC link implementation
        $nextOnlyParams = [];

        // Generate LC (local comment) param
        if (isset($request->params->lc))
        {
            $param = new NextRequestParams();
            
            // I don't know if this is needed, but I want to include it
            // anyways.
            $param->setUnknownThing(new UnknownThing(["a" => 0]));

            $param->setLinkedCommentId($request->params->lc);

            $nextOnlyParams += [
                "params" => Base64Url::encode($param->serializeToString())
            ];
        }

        if (!is_null($yt->playlistId)) {
            $watchRequestParams['playlistId'] = $yt->playlistId;
            $watchRequestParams['playlistIndex'] = $yt->playlistIndex;
        }

        // TODO (kirasicecreamm): Clean up this algo, make better
        if (isset($request->params->t)) {
            preg_match_all("/\d{1,6}/", $request->params->t, $times);
            $times = $times[0];
            if (count($times) == 1) { // before you whine "waaahh use case" I CAN'T IT BREAKS IT FOR NO FUCKING REASON, if you wanna make this better, go ahead
                $startTime = (int) $times[0];
            } else if (count($times) == 2) {
                $startTime = ((int) $times[0] * 60) + (int) $times[0];
            } else if (count($times) == 3) {
                $startTime = ((int) $times[0] * 3600) + ((int) $times[1] * 60) + (int) $times[2];
            } else {
                $startTime = 0;
            }
        }

        Request::queueInnertubeRequest(
            "watch", "next", (object)(
                $watchRequestParams + $nextOnlyParams
            )
        );

        Request::queueInnertubeRequest(
            "player", "player", (object) ([
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
            ] + $watchRequestParams)
        );

        $dislikesData = null;

        /**
         * Determine whether or not to use the Return YouTube Dislike
         * API to return dislikes. Retrieved from application config.
         */
        if (true === ConfigManager::getConfigProp("useReturnYouTubeDislike"))
        {
            $ch = curl_init("https://returnyoutubedislikeapi.com/votes?videoId=" . $yt->videoId);
            curl_setopt_array($ch, [
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1
            ]);

            $rydResponse = curl_exec($ch);
            curl_close($ch);
            $dislikesData = json_decode($rydResponse);
        }

        $responses = Request::getResponses();

        $response = $responses["watch"];
        $presponse = $responses["player"];
        $yt->response = $response;

        $ytdata = json_decode($response);
        $playerResponse = json_decode($presponse);
        $yt->playerResponse = $playerResponse;
        // remove ads lol
        if (isset($yt->playerResponse->playerAds)) unset($yt->playerResponse->playerAds);
        if (isset($yt->playerResponse->adPlacements)) unset($yt->playerResponse->adPlacements);

        // end request

        $yt->page = \Rehike\Model\Watch\WatchModel::bake($yt, $ytdata, $yt -> videoId, $dislikesData);

        $yt->rawWatchNextResponse = $response;
    }

    public function handleSpfData(&$data)
    {
        $yt = &$this->yt;

        if (isset($yt->playerResponse)) {
            $data->data = (object) [
                'swfcfg' => (object) [
                    'args' => (object) [
                        'raw_player_response' => null,
                        'raw_watch_next_response' => null
                    ]
                ]
            ];

            $data->data->swfcfg->args->raw_player_response = $yt->playerResponse;
            $data->data->swfcfg->args->raw_watch_next_response = json_decode($yt->rawWatchNextResponse);
    
            if (isset($yt->page->playlist)) {
                $data->data->swfcfg->args->is_listed = '1';
                $data->data->swfcfg->args->list = $yt->playlistId;
                $data->data->swfcfg->args->videoId = $yt->videoId;
            }
        }
    }
};