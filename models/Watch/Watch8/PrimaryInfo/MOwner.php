<?php
namespace Rehike\Model\Watch\Watch8\PrimaryInfo;

use Rehike\Util\ParsingUtils;
use Rehike\Model\Common\Subscription\MSubscriptionActions;
use Rehike\Model\Common\MButton;
use Rehike\SignInV2\SignIn;
use Rehike\Util\ExtractUtils;
use Rehike\i18n\i18n;
use Rehike\Model\Watch\WatchBakery;
use Rehike\ViewModelParser;

/**
 * Defines the video owner information, which appears in the bottom
 * left corner of the primary info renderer.
 */
class MOwner
{
    /** @var string */
    public $title = "";

    /** @var mixed[] */
    public $thumbnail;

    /** @var mixed[] */
    public $badges;

    /** @var object */
    public $navigationEndpoint;

    /**
     * Defines the subscription actions.
     * 
     * These include the subscribe button, the notifications button,
     * and the count at the end.
     *  
     * @var MSubscriptionActions 
     */
    public $subscriptionButtonRenderer;


    /**
     * Defines the channel settings button
     * 
     * @var MButton
     */
    public $channelSettingsButtonRenderer;

    private function __construct()
    {
    }
    
    public static function fromSingleUser(WatchBakery $bakery): self
    {
        $instance = new self();
        $instance->initSingleUser($bakery);
        
        return $instance;
    }
    
    public static function fromFirstCollaborator(WatchBakery $bakery): self
    {
        $instance = new self();
        $instance->initFromFirstCollaborator($bakery);
        
        return $instance;
    }

    /**
     * Initialises from a standard single-owner video.
     */
    private function initSingleUser(WatchBakery $bakery)
    {
        $secInfo = &$bakery->secondaryInfo;
        $info = $secInfo->owner->videoOwnerRenderer;
        $i18n = i18n::getNamespace("watch");

        $signInInfo = SignIn::getSessionInfo();
        $hasChannel = SignIn::isSignedIn() && !is_null($signInInfo->getUcid());
        if ($hasChannel)
        {
            $ucid = $signInInfo->getUcid();
        }

        if (isset($info))
        {
            $this->title = $info->title ?? null;
            $this->thumbnail = $info->thumbnail ?? null;
            $this->badges = $info->badges ?? null;
            $this->navigationEndpoint = $info->navigationEndpoint ?? null;

            // Subscription button
            $subscribeCount = isset($info->subscriberCountText)
                ? ExtractUtils::isolateSubCnt(ParsingUtils::getText($info->subscriberCountText))
                : null
            ;

            // Build the subscription button from the InnerTube data.
            if (!SignIn::isSignedIn())
            {
                $this->subscriptionButtonRenderer = MSubscriptionActions::signedOutStub($subscribeCount);
            }
            else if (isset($secInfo->subscribeButton->subscribeButtonRenderer))
            {
                $this->subscriptionButtonRenderer = MSubscriptionActions::fromData($secInfo->subscribeButton->subscribeButtonRenderer, $subscribeCount);
            }
            else if (isset($secInfo->subscribeButton->buttonRenderer))
            { // channel settings button
                $this->channelSettingsButtonRenderer = new MButton((object) [
                    "style" => "default",
                    "size" => "default",
                    "text" => (object) [
                        "simpleText" => $i18n->get("channelSettings")
                    ],
                    "icon" => true,
                    "navigationEndpoint" => (object) [
                        "commandMetadata" => (object) [
                            "webCommandMetadata" => (object) [
                                "url" => "//studio.youtube.com/channel/$ucid/editing/sections"
                            ]
                        ]
                    ],
                    "class" => [
                        "channel-settings-link"
                    ],
                    "customAttributes" => [
                        "target" => "_blank"
                    ]
                ]);
            }
        }
    }
    
    /**
     * Initialises for a video with multiple collaborators.
     * 
     * In this case, the first collaborator's information is the main one shown.
     */
    private function initFromFirstCollaborator(WatchBakery $bakery)
    {
        $collaboratorsDialog = $bakery->collaborators->getRootData();
        $firstItem = $collaboratorsDialog->customContent->listViewModel->listItems[0]->listItemViewModel;
        
        $this->title = ParsingUtils::getText($firstItem->title);
        $this->thumbnail = (object)[
            "thumbnails" => [
                [
                    "url" => $firstItem->leadingAccessory->avatarViewModel->image->sources[0]->url,
                ],
            ],
        ];
        
        if (isset($firstItem->title->attachmentRuns[0]->element->type->imageType->image->sources[0]->clientResource))
        {
            // XXX(isabella): For now, we will just assume any case like this means there's a badge. If
            // this has some false positives, then uncomment the below condition (and expand it with
            // other cases)
            $attachment = $firstItem->title->attachmentRuns[0]->element->type->imageType->image->sources[0]->clientResource;
            //if ($attachment->imageName == "CHECK_CIRCLE_FILLED")
            //{
                $this->badges = (object)[];
            //}
        }
        
        $this->navigationEndpoint = $firstItem->title->commandRuns[0]->onTap->innertubeCommand;
        
        // Get subscription count:
        $baseSubtitle = ParsingUtils::getText($firstItem->subtitle);
        $subtitleParts = explode("•", $baseSubtitle);
        
        $subscribeCount = null;
        
        if (isset($subtitleParts[1]))
        {
            $formattedSubscriberCount = trim($subtitleParts[1]);
            $subscribeCount = ExtractUtils::isolateSubCnt($formattedSubscriberCount);
        }
        
        // Build the subscription button:
        if (!SignIn::isSignedIn())
        {
            $this->subscriptionButtonRenderer = MSubscriptionActions::signedOutStub($subscribeCount);
        }
        else if (isset($firstItem->trailingButtons->buttons[0]->subscribeButtonViewModel))
        {
            $viewModel = $firstItem->trailingButtons->buttons[0]->subscribeButtonViewModel;
            $viewModelParser = new ViewModelParser($viewModel, $bakery->frameworkUpdates);
            
            $this->subscriptionButtonRenderer = MSubscriptionActions::fromViewModel(
                $viewModel, $viewModelParser, $subscribeCount
            );
        }
    }
}