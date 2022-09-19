<?php
namespace Rehike\Model\Channels\Channels4;

use Rehike\Util\ExtractUtils;
use Rehike\Util\ImageUtils;
use Rehike\TemplateFunctions as TF;
use Rehike\Model\Common\Subscription\MSubscriptionActions;

class MHeader
{
    public $title;
    public $badges;
    public $thumbnail;
    public $banner;
    public $headerLinks;
    public $tabs;
    public $subscriptionButtons;

    private $subscriptionCount;

    public function __construct($header, $baseUrl)
    {
        // Add the title if it exists
        if ($a = @$header->title)
        {
            $this->title = (object)[
                "text" => $a,
                "href" => $baseUrl
            ];
        }
        
        // Add the avatar if it exists
        if ($a = @$header->avatar)
        {
            $this->thumbnail = $a;
            $this->thumbnail->thumbnails[0]->url = ImageUtils::changeGgphtImageSize($this->thumbnail->thumbnails[0]->url, 100);
            $this->thumbnail->href = $baseUrl;
        }

        // Add the banner if it exists
        if ($a = @$header->banner)
        {
            $this->banner = (object) [
                "image" => $a -> thumbnails[0] -> url ?? null,
                "hdImage" => $a -> thumbnails[3] -> url ?? null
            ];
            $this->banner->isCustom = true;
        }
        else
        {
            $this->banner = new MDefaultBanner();
            $this->banner->isCustom = false;
        }

        // Add the banner links if they exist.
        if ($a = @$header->headerLinks->channelHeaderLinksRenderer)
            $this->headerLinks = self::getHeaderLinks($a);

        // Add the badges
        if ($a = @$header->badges)
            $this->badges = $a;

        // Add the subscription button
        if ($a = @$header->subscribeButton->subscribeButtonRenderer)
        {
            $count = "";

            if (isset($header->subscriberCountText))
            {
                $count = ExtractUtils::isolateSubCnt(TF::getText($header->subscriberCountText));
                $this->subscriptionCount = TF::getText($header->subscriberCountText);
            }

            $this->subscriptionButton = MSubscriptionActions::fromData(
                $a, $count
            );
        }
    }

    public function addTabs($tabs)
    {
        $this->tabs = $tabs;
    }

    public function getTitle()
    {
        return isset($this->title->text) ? $this->title->text : "";
    }

    public function getThumbnail()
    {
        return $this->thumbnail->thumbnails[0]->url ?? "";
    }

    public function getSubscriptionCount()
    {
        return $this->subscriptionCount ?? "";
    }

    /**
     * Process the header links provided and add href
     * properties to them.
     */
    protected static function getHeaderLinks($headerLinks)
    {
        $response = $headerLinks;

        if (isset($response->primaryLinks))
        {
            $response->primaryLinks[0]->href =
                $response->primaryLinks[0]->navigationEndpoint->urlEndpoint->url
            ;
        }

        if (isset($response->secondaryLinks))
        {
            for ($i = 0, $j = count($response->secondaryLinks); $i < $j; $i++)
            {
                $response->secondaryLinks[$i]->href =
                    $response->secondaryLinks[$i]->navigationEndpoint->urlEndpoint->url
                ;
            }
        }

        return $response;
    }
}