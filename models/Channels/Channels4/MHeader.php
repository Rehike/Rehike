<?php
namespace Rehike\Model\Channels\Channels4;

use Rehike\ViewModelParser;
use Rehike\Model\Appbar\MAppbarNav;
use Rehike\Model\Appbar\MAppbarNavItem;
use Rehike\Model\Common\Subscription\MSubscriberCount;
use Rehike\Util\ExtractUtils;
use Rehike\Util\ImageUtils;
use Rehike\Util\ParsingUtils;
use Rehike\Model\Common\Subscription\MSubscriptionActions;

/**
 * Model for the channels4 page header.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
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
    protected ?object $frameworkUpdates = null;

    protected $subscriptionCount;

    public function __construct($header, $baseUrl, bool $isOld = true, ?object $frameworkUpdates = null)
    {
        if (!$isOld)
        {
            // New format (March 2024):
            $this->frameworkUpdates = $frameworkUpdates;
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
        
        // The subscription count is stored elsewhere, because of course it is.
        // In fact, I'm not even sure if this can be reliably considered to be the
        // subscription count of the channel, meaning that there is a possibility that
        // we will need to apply another fucking heuristic to determine if the string
        // is the subscriber count.
        $subscriberCountFullString = @$content->metadata->contentMetadataViewModel->metadataRows[1]
            ->metadataParts[0]->text->content;
            
        $subscriberCount = ExtractUtils::isolateSubCnt(ParsingUtils::getText($subscriberCountFullString));
        
        $primaryActionButtonContainer = $content->actions->flexibleActionsViewModel->actionsRows[0]->actions[0];
        
        // Add the subscribe button (or equivalent item):
        if ($viewModel = @$primaryActionButtonContainer->subscribeButtonViewModel)
        {
            // Logged-in subscribe button
            if ($this->frameworkUpdates)
            {
                // In order to determine whether or not the user is subscribed, we need to parse
                // the mutation entities.
                $parser = new ViewModelParser($viewModel, $this->frameworkUpdates);
                $entities = $parser->getViewModelEntities([
                    "stateEntityStoreKey" => "state"
                ]);
                
                $subscribeStatus = $entities["state"]->payload->subscriptionStateEntity->subscribed;
                    
                if ($subscribeStatus == true)
                {
                    $actionsModelStatus = @$viewModel->unsubscribeButtonContent->onTapCommand->innertubeCommand
                        ->signalServiceEndpoint->actions[0]->openPopupAction->popup->confirmDialogRenderer
                        ->confirmButton->serviceEndpoint->unsubscribeEndpoint->params ?? "";
                }
                else
                {
                    $actionsModelStatus = @$viewModel->subscribeButtonContent->onTapCommand->innertubeCommand
                        ->subscribeEndpoint->params ?? "";
                }
                    
                $this->subscriptionButton = new MSubscriptionActions([
                    "branded" => true,
                    "longText" => $subscriberCount,
                    "shortText" => $subscriberCount,
                    "isSubscribed" => $subscribeStatus ?? false,
                    "channelExternalId" => $viewModel->channelId,
                    "params" => $actionsModelStatus,
                    "unsubConfirmDialog" => @$viewModel->unsubscribeButtonContent->onTapCommand
                        ->innertubeCommand->signalServiceEndpoint->actions[0]->openPopupAction->popup
                        ->confirmDialogRenderer ?? null,
                    //"notificationStateId" => $data->notificationPreferenceButton->subscriptionNotificationToggleButtonRenderer->currentStateId ?? 3
                ]);
            }
        }
        else if (
            ($viewModel = @$primaryActionButtonContainer->buttonViewModel) &&
            @$viewModel->targetId == "channel-customize-button"
        )
        {
            // Channel owner
            $this->subscriptionButton = MSubscriptionActions::buildMock($subscriberCount);
        }
        else if (!\Rehike\Signin\API::isSignedIn())
        {
            $this->subscriptionButton = MSubscriptionActions::signedOutStub($subscriberCount);
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
        
        // Add the subscribe button (or equivalent item):
        if ($a = @$header->subscribeButton->subscribeButtonRenderer)
        {
            // Logged-in subscribe button
            if (isset($header->subscriberCountText))
            {
                $count = ExtractUtils::isolateSubCnt(ParsingUtils::getText($header->subscriberCountText));
                $this->subscriptionCount = ParsingUtils::getText($header->subscriberCountText);
            }

            $this->subscriptionButton = MSubscriptionActions::fromData(
                $a, $count
            );
        }
        else if (isset($header->editChannelButtons))
        {
            // Channel owner
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
            // Logged-out subscribe button
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