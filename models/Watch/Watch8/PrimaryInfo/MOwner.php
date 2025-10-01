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
        $firstItem = $bakery->collaborators->getCollaborators()[0];
        
        $this->title = $firstItem->name;
        $this->thumbnail = (object)[
            "thumbnails" => [
                [
                    "url" => $firstItem->avatarUrl,
                ],
            ],
        ];
        
        if ($firstItem->verified)
        {
            $this->badges = (object)[];
        }
        
        $this->navigationEndpoint = $firstItem->navigationEndpoint;
        
        // Build the subscription button (this data is unique to this specific variant of the renderer):
        if (!SignIn::isSignedIn())
        {
            $this->subscriptionButtonRenderer = MSubscriptionActions::signedOutStub($firstItem->subscriberCount);
        }
        else if (isset($firstItem->rawData->trailingButtons->buttons[0]->subscribeButtonViewModel))
        {
            $viewModel = $firstItem->rawData->trailingButtons->buttons[0]->subscribeButtonViewModel;
            $viewModelParser = new ViewModelParser($viewModel, $bakery->frameworkUpdates);
            
            $this->subscriptionButtonRenderer = MSubscriptionActions::fromViewModel(
                $viewModel, $viewModelParser, $firstItem->subscriberCount
            );
        }
    }
}