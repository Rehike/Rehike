<?php
use \Rehike\Controller\core\AjaxController;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\YtApp;

use Com\Youtube\Innertube\Request\NextRequestParams;
use Com\Youtube\Innertube\Request\NextRequestParams\UnknownThing;

use Rehike\Network;
use Rehike\Async\Promise;

use Rehike\i18n\i18n;

use Rehike\Util\Base64Url;
use Rehike\ConfigManager\Config;
use Rehike\Helper\WatchUtils;
use Rehike\Util\ExtractUtils;
use Rehike\Util\ParsingUtils;

use YukisCoffee\CoffeeRequest\Exception\GeneralException;

/**
 * Annotations invideo controller.
 * 
 * @author Toru the Red Fox
 * @author The Rehike Maintainers
 */
return new class extends AjaxController
{
	public string $contentType = "application/xml";
	public bool $useTemplate = false;
	
	public function onPost(YtApp $yt, RequestMetadata $request): void
    {
		$this->initPlayer($yt); // post doesn't initialize this by itself because it usually doesn't need to
        $this->onGet($yt, $request);
    }

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
		$videoId = $request->params->video_id; 
		if ($videoId == null && $videoId !== "") {
			http_response_code(400);
			die();
		}
		
		// set up innertube request to fetch some data
		$yt->videoId = $videoId;
		
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
		// TODO (kirasicecreamm): Clean up this algo, make better
        if (isset($request->params->t))
        {
            preg_match_all("/\d{1,6}/", $request->params->t, $times);
            $times = $times[0];
            if (count($times) == 1)
            {
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

        Promise::all([
			"player"     => $playerRequest,
            "next"       => $nextRequest,
        ])->then(function ($responses) use ($yt) {
            \Rehike\Profiler::end("watch_requests");
            $nextResponse = $responses["next"]->getJson();
			$playerResponse = $responses["player"]->getJson();
	
			// Push these over to the global object.
			$yt->watchNextResponse = $nextResponse;
			
			ob_start();
			$videoId = $yt->videoId; 
			$iv_url = "https://storage.googleapis.com/biggest_bucket/annotations/".substr($videoId,0,1)."/".substr($videoId,0,3)."/".$videoId.".xml.gz";
			$ch = curl_init($iv_url);
			$options = array(
				CURLOPT_RETURNTRANSFER => true,  // don't echo web page
				CURLOPT_HEADER         => false,  // don't return headers
				CURLOPT_FOLLOWLOCATION => true,   // follow redirects
				CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
				CURLOPT_ENCODING       => "",     // handle compressed
				CURLOPT_USERAGENT      => "test", // name of client
				CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
				CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
				CURLOPT_TIMEOUT        => 120,    // time-out on response
				CURLOPT_ENCODING	   => '',
			);
			curl_setopt_array($ch, $options);
			$out = curl_exec($ch);
			$code = curl_getinfo($ch)["http_code"];
			
			// Close the cURL resource, and free system resources
			curl_close($ch);
			
			if ($code !== 200)
			{
				$xml = new SimpleXMLElement("<document><annotations></annotations></document>");
			}
			else
			{
				$xml = simplexml_load_string($out);
				foreach ($xml->xpath("//annotation[@style='branding']") as $node)
				{ 
					// remove any existing branding
					unset($node[0]); // remove the original annotation if present as we're going to be making our own
				}
			}
			
			// Wrapped in isset to prevent crashes
			if (isset($yt->watchNextResponse->contents->twoColumnWatchNextResults->results->results))
			{
				$results = $yt->watchNextResponse->contents->twoColumnWatchNextResults->results->results;
			}
			
			// For sub-result references, iteration must be used
			if (isset($results->contents))
			for ($i = 0; $i < count($results->contents); $i++) 
			foreach ($results->contents[$i] as $name => &$value)
			switch ($name)
			{
				case "videoPrimaryInfoRenderer":
					$primaryInfo = &$value;
					break;
				case "videoSecondaryInfoRenderer":
					$secondaryInfo = &$value;
					break;
				case "itemSectionRenderer":
					// Determine based on section ID instead
					if (isset($value->sectionIdentifier))
					switch ($value->sectionIdentifier)
					{
						case "comment-item-section":
							$commentSection = &$value;
							break;
					}
					break;
			}
			
			$authorUid = $secondaryInfo->owner->videoOwnerRenderer->navigationEndpoint->browseEndpoint->browseId ?? null;
			$authorUrl = 'https://www.youtube.com'.$secondaryInfo->owner->videoOwnerRenderer->navigationEndpoint->commandMetadata->webCommandMetadata->url ?? null;//'https://www.youtube.com/user/needforspeedfan145';
			$authorName = $secondaryInfo->owner->videoOwnerRenderer->title->runs[0]->text ?? null;
			$subscribeCount = isset($secondaryInfo->owner->videoOwnerRenderer->subscriberCountText)
                ? ExtractUtils::isolateSubCnt(ParsingUtils::getText($secondaryInfo->owner->videoOwnerRenderer->subscriberCountText))
                : null
            ;
			$i18n = i18n::getNamespace("channels");
			if ($subscribeCount === "1")
            {
                $subscribeCount = i18n::getFormattedString(
                    "misc", 
                    "subscriberTextSingular", 
                    $subscribeCount
                );
            }
            else
            {
                $subscribeCount = i18n::getFormattedString(
                    "misc", 
                    "subscriberTextPlural", 
                    $subscribeCount
                );
            }

			$hasBranding = $playerResponse->annotations !== null;
			
			if ($hasBranding) {
				$brandingData = $playerResponse->annotations[0]->playerAnnotationsExpandedRenderer;
				$brandingAnnotationUid = $brandingData->annotationId;
				$thumbnail = $brandingData->featuredChannel->watermark->thumbnails[0];
				$brandingAnnotation = $xml->annotations->addChild('annotation');
				$brandingAnnotation->addAttribute('id','channel:'.$brandingAnnotationUid);
				$brandingAnnotation->addAttribute('style','branding');
				$brandingAnnotation->addAttribute('type','branding');
				
				$json = (object) [];
				$json->end_ms = intval($brandingData->featuredChannel->endTimeMs);
				$json->num_subscribers = $subscribeCount;
				$json->start_ms = intval($brandingData->featuredChannel->startTimeMs);
				$json->image_url = $thumbnail->url;
				$json->image_type = 0;
				$json->image_width = intval($thumbnail->width);
				$json->image_height = intval($thumbnail->height);
				$json->channel_name = $authorName;
				$json->subscription_token = ""; // figure out how to get this, if necessary at all?
				$json->is_mobile = false; // probably should determine this using useragent
				$json->channel_id = $authorUid;
				
				$brandingAnnotation->addChild('data',json_encode($json));
				$brandingAnnotation->addChild('segment');
				$brandingAnnotation->addChild('action');
				$brandingAnnotation->action->addAttribute('trigger', 'click');
				$brandingAnnotation->action->addAttribute('type', 'openUrl');
				$brandingAnnotation->action->addChild('url');
				$brandingAnnotation->action->url->addAttribute('type', 'hyperlink');
				$brandingAnnotation->action->url->addAttribute('target', 'new');
				$brandingAnnotation->action->url->addAttribute('value', $authorUrl);
			}
			
			$doc = new DOMDocument();
			$doc->formatOutput = TRUE;
			$doc->loadXML($xml->asXML());
			$out = $doc->saveXML();
	
			echo $out;
		});
    }
};
