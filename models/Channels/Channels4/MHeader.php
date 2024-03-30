<?php
namespace Rehike\Model\Channels\Channels4;

use Rehike\Model\Appbar\MAppbarNav;
use Rehike\Model\Appbar\MAppbarNavItem;
use Rehike\Util\ExtractUtils;
use Rehike\Util\ImageUtils;
use Rehike\Util\ParsingUtils;
use Rehike\Model\Common\Subscription\MSubscriptionActions;

class MHeader
{
    public $title;
    public $badges;
    public $thumbnail;
    public $url;
    public $banner;
    public $headerLinks;
    public $tabs;
    public $subscriptionButton;
    public $nonexistentMessage;

    protected $subscriptionCount;

    public function __construct($header, $baseUrl, bool $isOld = true)
    {
        if (!$isOld)
        {
            // New format (March 2024):
            $this->constructFromViewModel($header, $baseUrl);
        }
        else
        {
            // Old format:
            $this->constructFromRenderer($header, $baseUrl);
        }
    }

    /**
     * Construct from an InnerTube view model structure.
     */
    protected function constructFromViewModel($header, $baseUrl): void
    {
        $content = $header->content->pageHeaderViewModel;

        // Add the title if it exists.
        if ($a = @$content->title->dynamicTextViewModel->text->content)
        {
            $this->title = (object)[
                "text" => $a,
                "href" => $baseUrl
            ];
        }

        // Add the avatar if it exists.
        if ($a = @$content->image->decoratedAvatarViewModel->avatar->avatarViewModel)
        {
            $this->thumbnail = ImageUtils::changeSize(
                $a->image->sources[0]->url, 100
            );
        }

        $this->url = $baseUrl;

        // Add the banner if it exists.
        if ($a = @$content->banner->imageBannerViewModel)
        {
            $this->banner = (object)[
                "image" => $a->image->sources[0]->url ?? null,
                "hdImage" => $a->image->sources[3]->url ?? null
            ];
            $this->banner->isCustom = true;
        }
        else
        {
            $this->banner = new MDefaultBanner();
            $this->banner->isCustom = false;
        }
    }

    /**
     * Construct from legacy InnerTube renderer structure.
     */
    protected function constructFromRenderer($header, $baseUrl): void
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
            $this->thumbnail = ImageUtils::changeSize($a->thumbnails[0]->url, 100);
        }

        $this->url = $baseUrl;

        // Add the banner if it exists
        if ($a = @$header->banner)
        {
            $this->banner = (object) [
                "image" => $a->thumbnails[0]->url ?? null,
                "hdImage" => $a->thumbnails[3]->url ?? null
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


        $count = "";
        // Add the subscription button
        if ($a = @$header->subscribeButton->subscribeButtonRenderer)
        {

            if (isset($header->subscriberCountText))
            {
                $count = ExtractUtils::isolateSubCnt(ParsingUtils::getText($header->subscriberCountText));
                $this->subscriptionCount = ParsingUtils::getText($header->subscriberCountText);
            }

            $this->subscriptionButton = MSubscriptionActions::fromData(
                $a, $count
            );
        }
        // Channel owner
        else if (isset($header->editChannelButtons))
        {
            if (isset($header->subscriberCountText))
            {
                $count = ExtractUtils::isolateSubCnt(ParsingUtils::getText($header->subscriberCountText));
                $this->subscriptionCount = ParsingUtils::getText($header->subscriberCountText);
            }

            $this->subscriptionButton = MSubscriptionActions::buildMock(
                $count
            );
        }
        else if (isset($header->subscribeButton))
        {
            if (isset($header->subscriberCountText))
            {
                $count = ExtractUtils::isolateSubCnt(ParsingUtils::getText($header->subscriberCountText));
                $this->subscriptionCount = ParsingUtils::getText($header->subscriberCountText);
            }

            $this->subscriptionButton = MSubscriptionActions::signedOutStub($count);
        }
    }

    public function addTabs($tabs, $partSelect = false)
    {
        $this->tabs = [];

        foreach ($tabs as &$tab)
        {
            if (isset($tab->tabRenderer))
            {
                if (!isset($tab->tabRenderer->title))
                {
                    continue;
                }

                if (@$tab->tabRenderer->selected)
                {
                    $tab->tabRenderer->status = $partSelect ? MAppbarNavItem::StatusPartiallySelected : MAppbarNavItem::StatusSelected;
                }
                else
                {
                    $tab->tabRenderer->status = MAppbarNavItem::StatusUnselected;
                }

                unset($tab->tabRenderer->selected);
            }

            if (!@$tab->hidden)
            {
                $this->tabs[] = $tab;
            }
        }
    }

    public function getTitle()
    {
        return isset($this->title->text) ? $this->title->text : "";
    }

    public function getThumbnail()
    {
        return $this->thumbnail;
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