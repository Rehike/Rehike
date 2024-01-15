<?php
use \Rehike\Controller\core\HitchhikerController;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\YtApp;

use Com\Youtube\Innertube\Request\NextRequestParams;
use Com\Youtube\Innertube\Request\NextRequestParams\UnknownThing;

use Rehike\Network;
use Rehike\Async\Promise;

use Rehike\Util\Base64Url;
use Rehike\ConfigManager\Config;
use Rehike\Util\WatchUtils;
use Rehike\Util\ExtractUtils;
use Rehike\Util\ParsingUtils;

use Rehike\Model\Watch\WatchModel;
use YukisCoffee\CoffeeRequest\Exception\GeneralException;

/**
 * Annotations invideo controller.
 * 
 * @author Toru the Red Fox
 * @author The Rehike Maintainers
 */
return new class extends HitchhikerController {
	public string $contentType = "application/xml";
	public bool $useTemplate = false;
	
	public function onPost(YtApp $yt, RequestMetadata $request): void
    {
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

        Promise::all([
            "next"       => $nextRequest,
        ])->then(function ($responses) use ($yt) {
            \Rehike\Profiler::end("watch_requests");
            $nextResponse = $responses["next"]->getJson();
	
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
			
			$xml;
			
			if ($code !== 200) {
				//http_response_code(404);
				//ob_end_clean();
				$xml = new SimpleXMLElement("<document><annotations></annotations></document>");
				echo $iv_url;
			} else {
				$xml = simplexml_load_string($out);
				foreach ($xml->xpath("//annotation[@style='branding']") as $node) { // remove any existing branding
					//unset($node[0]); // remove the original annotation if present as we're going to be making our own
				}
			}
			
			//self::destructureData($yt->watchNextResponse->contents);
			
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
			$image_url = 'https://i.ytimg.com/an/'.substr($authorUid, 2).'/featured_channel.jpg?v='.substr(bin2hex(random_bytes(4)), 2);
			
			$ch2 = curl_init($image_url);
			$options = array(
				CURLOPT_RETURNTRANSFER => true,   // don't echo web page
				CURLOPT_HEADER         => false,  // don't return headers
				CURLOPT_FOLLOWLOCATION => true,   // follow redirects
				CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
				CURLOPT_ENCODING       => "",     // handle compressed
				CURLOPT_USERAGENT      => "test", // name of client
				CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
				CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
				CURLOPT_TIMEOUT        => 120,    // time-out on response
				CURLOPT_HEADER		   => true,
				CURLOPT_CUSTOMREQUEST  => 'HEAD',
				CURLOPT_ENCODING	   => '',
			);
			curl_setopt_array($ch2, $options);
			curl_exec($ch2);
			$code = curl_getinfo($ch2)["http_code"];
			
			// Close the cURL resource, and free system resources
			curl_close($ch2);
			
			$hasBranding = $code !== 404;
			
			// Generate 16 bytes (128 bits) of random data or use the data passed into the function.
			$data = $videoId . 'AJKwieuISJDHGBiueSajsghkEWUIO';
			$data = $data ?? random_bytes(16);
			assert(strlen($data) == 16);
			
			// Set version to 0100
			$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
			// Set bits 6-7 to 10
			$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
			
			// Output the 36 character UUID.
			$brandingAnnotationUid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
			
			if ($hasBranding) {
				$brandingAnnotation = $xml->annotations->addChild('annotation');
				$brandingAnnotation->addAttribute('id','channel:'.$brandingAnnotationUid);
				$brandingAnnotation->addAttribute('style','branding');
				$brandingAnnotation->addAttribute('type','branding');
				$data = '{
					"end_ms":215000,
					"standalone_subscribe_button_data":
					{
						"subscribeCount":"'.$subscribeCount.'",
						"classic":true,
						"signinUrl":"https:\/\/accounts.google.com\/ServiceLogin?uilel=3\u0026service=youtube\u0026hl=en\u0026continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Fcontinue_action%3DQUFFLUhqbjZ1eldPMHJUYlFYb1Y1WFRXdGJpNGdXQzItUXxBQ3Jtc0tuUVJNY1ZCMHB0eXN2OXZqWE5VNnVXeF9UMS1ibXRHaGlHY3FWS01tVWpmTnNDOHVZQWFjamg3VTFabHBBNW1mZ2ViaklhaThrUDIzQjFuUGJDdFEtbDlDUnQ3MnZyZHVjQU1PMWpHQmhXdzJPVFBiRmhRTEZ2ZVBmb2F2d19KNWZMQmpGTWk5cWdvTENkcmNDd19hN0RET3RSVlBtMk5sYUNUT0VwMzNwZFVhSGJ6b3Z6ekhEcEdTUEpuZlUwVXBOZ21uWncxRDFRal85eUVHeC1RMjlmdVdvVTdXc01Sc2prZUt2ZEg0SGxYQWEwY05z%26app%3Ddesktop%26feature%3Div%26hl%3Den%26action_handle_signin%3Dtrue%26next%3D'.urlencode(urlencode($authorUrl)).'\u0026passive=true",
						"subscribeText":"Subscribe",
						"feature":"iv",
						"unsubscribeText":"Subscribed",
						"subscribed":false,
						"unsubscribeCount":"'.($subscribeCount+1).'",
						"enabled":false
					},
					"num_subscribers":"'.$subscribeCount.'",
					"start_ms":1000,
					"image_url":'.json_encode($image_url).',
					"image_type":1,
					"image_width":40,
					"use_standalone_subscribe_button":true,
					"image_height":40,
					"channel_name":"'.$authorName.'",
					"subscription_token":["",
					""],
					"is_mobile":false,
					"channel_id":"'.$authorUid.'",
					"session_data":{
						"ei":"YvQrXPT4C-GC8gSw46e4Cg",
						"feature":"iv",
						"itct":"CAMQ8zcY____________ASITCPSx_Y3bzd8CFWGBnAodsPEJpyj4HTICaXZIvrXYroe2wflS",
						"src_vid":"'.$videoId.'",
						"annotation_id":"'.$brandingAnnotationUid.'"
					}
				}';
				$brandingAnnotation->addChild('data',$data);
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
