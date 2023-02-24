<?php
namespace Rehike\Model\Channels\Channels4;

use Rehike\TemplateFunctions;
use Rehike\Util\ImageUtils;
use Rehike\Util\ExtractUtils;
use Rehike\Model\Common\Subscription\MSubscriptionActions;

/**
 * Provides a header for the Music, Sports,
 * and Gaming channels.
 */
class MCarouselHeader extends MHeader
{
    public function __construct($data, $baseUrl)
    {
        foreach ($data->contents as $content)
        {
            if ($header = @$content->topicChannelDetailsRenderer)
            {
                if (isset($header->title))
                {
                    $this->title = (object) [
                        "text" => TemplateFunctions::getText($header->title),
                        "href" => $baseUrl
                    ];
                }

                $this->banner = new MDefaultBanner();

                $this->url = $baseUrl;

                if (isset($header->avatar))
                {
                    $this->thumbnail = ImageUtils::changeSize($header->avatar->thumbnails[0]->url, 100);
                }

                if (isset($header->subscribeButton->subscribeButtonRenderer))
                {
                    $count = ExtractUtils::isolateSubCnt(TemplateFunctions::getText($header->subtitle));
                    $this->subscriptionCount = TemplateFunctions::getText($header->subtitle);

                    $this->subscriptionButton = MSubscriptionActions::fromData(
                        $header->subscribeButton->subscribeButtonRenderer,
                        $count
                    );
                }
            }
        }
    }
}