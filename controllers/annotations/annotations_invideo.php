<?php
use \Rehike\Controller\core\HitchhikerController;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\YtApp;

/**
 * Controller for the oops (error) page.
 * 
 * Very simple one, I know. All it's needed for is making a bridge between
 * CV2 and the static error page.
 * 
 * @author The Rehike Maintainers
 */
return new class extends HitchhikerController {
	public string $contentType = "application/xml";
	
	public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        $this->onGet($yt, $request);
    }

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
		$videoId = $request->params->video_id; 
		$iv_url = "https://storage.googleapis.com/biggest_bucket/annotations/".substr($videoId,0,1)."/".substr($videoId,0,3)."/".$videoId.".xml.gz";
		if ($videoId != null && $videoId !== "") {
			ob_start();
			$ch = curl_init($iv_url);
			$options = array(
				CURLOPT_RETURNTRANSFER => false,  // echo web page
				CURLOPT_HEADER         => false,  // don't return headers
				CURLOPT_FOLLOWLOCATION => true,   // follow redirects
				CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
				CURLOPT_ENCODING       => "",     // handle compressed
				CURLOPT_USERAGENT      => "test", // name of client
				CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
				CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
				CURLOPT_TIMEOUT        => 120,    // time-out on response
				CURLOPT_ENCODING	   => '',
				CURLOPT_MAX_RECV_SPEED_LARGE => 56000 // 56kb/s max
			);
			curl_setopt_array($ch, $options);
			$out = curl_exec($ch);
			$code = curl_getinfo($ch)["http_code"];
			
			// Close the cURL resource, and free system resources
			curl_close($ch);
			
			
			if ($code !== 200) {
				http_response_code(404);
				ob_end_clean();
			} else { ob_end_flush(); }
			
			die();
		}
    }
};