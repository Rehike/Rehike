<?php
namespace Rehike\Controller\ajax;

use \Rehike\Controller\core\HitchhikerController;
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

use Rehike\ControllerV2\{
    IGetController,
    IPostController,
};

/**
 * Controller for /get_video_metadata
 * 
 * @author Toru the Red Fox
 * @author The Rehike Maintainers
 */
class GetVideoMetadataController extends HitchhikerController implements IGetController, IPostController
{
	public string $contentType = "application/json";
	public bool $useTemplate = false;
	
	public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        $this->onGet($yt, $request);
    }

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
		$videoId = $request->params->video_id; 
		if ($videoId == null && $videoId !== "")
		{
			http_response_code(400);
			die();
		}
		
		// set up innertube request to fetch some data
		$yt->videoId = $videoId;
		
		$sharedRequestParams = [
			'videoId' => $yt->videoId
		];

        \Rehike\Profiler::start("watch_requests");
        // Makes the main watch request.
        $nextRequest = Network::innertubeRequest(
            "next",
            $sharedRequestParams
        );
		
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
			"ryd"        => $rydRequest,
        ])->then(function ($responses) use ($yt) {
            \Rehike\Profiler::end("watch_requests");
            $nextResponse = $responses["next"]->getJson();
			// Push these over to the global object.
			$yt->watchNextResponse = $nextResponse;
			
			try
            {
                $rydResponse = $responses["ryd"]?->getJson() ?? (object)[];
            }
            catch (\Exception $e)
            {
                $rydResponse = (object) [];
            }
			
			$videoId = $yt->videoId; 
			
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
                    "<strong>".$subscribeCount."</strong>"
                );
            }
            else
            {
                $subscribeCount = i18n::getFormattedString(
                    "misc", 
                    "subscriberTextPlural", 
                    "<strong>".$subscribeCount."</strong>"
                );
            }
			
			$metadata = (object) [];
			
			$metadata->video_info = (object) [];
			$metadata->user_info = (object) [];
			//$metadata->primary_info = $primaryInfo;
			//$metadata->secondary_info = $secondaryInfo;
			//$metadata->ryd_data = $rydResponse;
			
			$metadata->video_info->likes_count_unformatted = (int) filter_var($primaryInfo->videoActions->menuRenderer->topLevelButtons[0]->segmentedLikeDislikeButtonRenderer->likeButton->toggleButtonRenderer->defaultText->accessibility->accessibilityData->label, FILTER_SANITIZE_NUMBER_INT);
			$metadata->video_info->view_count_string = $primaryInfo->viewCount->videoViewCountRenderer->viewCount->simpleText;
			$metadata->video_info->view_count = (int) filter_var($metadata->video_info->view_count_string, FILTER_SANITIZE_NUMBER_INT);
			$metadata->video_info->subscription_ajax_token = "QUFFLUhqa05hcW1pNEUtYXJtVDV2Y0QtcXM0Nmppb2RHZ3xBQ3Jtc0trSHZ3NXp3eUdDVDdKYTI2WlB2VWFnRkxvQUJBbU52ZGdKWEh1UXRRWEl2QmJfbnFWSnlCNEpjLUEtX254S09scnN5YzdwbVpHWEdNdXdqZ1RyaHh1azRGVGlQQ0d6TThnTVhtNUJ4cFRHZjdvX09zYm5sNkplbHNtVURCVE1QZjZMY0cwT3VWMkJQendOajJlZmpBOFVuYWxUNFE=";
			$metadata->video_info->likes_dislikes_string = null;//"Likes: 2. Dislikes: 0.";
			$metadata->video_info->description = $secondaryInfo->attributedDescription->content;
			$metadata->video_info->dislikes_count_unformatted = $rydResponse->dislikes;
			
			$metadata->user_info->channel_logo_url = $secondaryInfo->owner->videoOwnerRenderer->thumbnail->thumbnails[1]->url;
			$metadata->user_info->external_channel_id = $authorUid;
			$metadata->user_info->public_name = $authorName;
			$metadata->user_info->public_title = $authorName;
			$metadata->user_info->external_id = substr($authorUid,2);
			//$metadata->user_info->subscription_button_html = "<span class=\" yt-uix-button-subscription-container\" ><button class=\"yt-uix-button yt-uix-button-size-default yt-uix-button-subscribe-branded yt-uix-button-has-icon yt-uix-subscription-button yt-can-buffer\" type=\"button\" onclick=\";return false;\" aria-busy=\"false\" aria-live=\"polite\" aria-role=\"button\" data-channel-external-id=\"UCM9gXqO8VQKZDrnOdRweqIQ\" data-href=\"https://accounts.google.com/ServiceLogin?passive=true&amp;continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26app%3Ddesktop%26continue_action%3DQUFFLUhqa1VzZ05DNXJiRzAyMG9aekNZLTRSaVhqd0F6QXxBQ3Jtc0trYnJETWRqbkhlZlNhNHJ1Q1F4YkJhQUlnZEZMWEdXWV9WRVVLMThHdVhuWVpZeTVRSXZ2TEdmZFpzNDBCV3MtN2dCc3NjOGZkclF5TDU5SjRlM1JFLW11djFDQzFkbEhFbzFvYk1QSk5EN3pJLV9rekYtXzFFR1ZDNUFKei1TVUdHNnI3UzBTeFZEQlNscHZZS3dXSTVNZXB5aEVESGlDaHQ3UUhSeE1KRkNmSHRlUy1aNDMyUXFjTFlhWUVER1k5OFhFeTU%253D%26feature%3Dsubscribe%26hl%3Dru%26next%3D%252Fchannel%252FUCM9gXqO8VQKZDrnOdRweqIQ&amp;hl=ru&amp;service=youtube&amp;uilel=3\" data-style-type=\"branded\" data-sessionlink=\"feature=trailer-endscreen&amp;ei=Om6kU7-tL8KwqAPNjYCoBA\"><span class=\"yt-uix-button-icon-wrapper\"><img src=\"http://s.ytimg.com/yts/img/pixel-vfl3z5WfW.gif\" class=\"yt-uix-button-icon yt-uix-button-icon-subscribe\"></span><span class=\"yt-uix-button-content\"><span class=\"subscribe-label\" aria-label=\"Подписаться\">Подписаться</span><span class=\"subscribed-label\" aria-label=\"Отменить подписку\">Подписка оформлена</span><span class=\"unsubscribe-label\" aria-label=\"Отменить подписку\">Отменить подписку</span> </span></button><span class=\"yt-subscription-button-subscriber-count-branded-horizontal\" >30</span>  <span class=\"yt-subscription-button-disabled-mask\" title=\"\"></span>\n</span>";
			$metadata->user_info->image_url = $secondaryInfo->owner->videoOwnerRenderer->thumbnail->thumbnails[0]->url;
			$metadata->user_info->subscriber_count_string = $num_subscribers;
			$metadata->user_info->channel_paid = 0;
			$metadata->user_info->channel_url = $secondaryInfo->owner->videoOwnerRenderer->navigationEndpoint->commandMetadata->webCommandMetadata->url;
			$metadata->user_info->username = null;
			$metadata->user_info->channel_banner_url = null;
			$metadata->user_info->channel_external_id = $authorUid; // they list this twice under a slightly rearranged name????
			
			
			$out = json_encode($metadata);
	
			echo $out;
		});
    }
}