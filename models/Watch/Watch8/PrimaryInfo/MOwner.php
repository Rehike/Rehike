<?php
namespace Rehike\Model\Watch\Watch8\PrimaryInfo;

use Rehike\Util\ParsingUtils;
use Rehike\Model\Common\Subscription\MSubscriptionActions;
use Rehike\Model\Common\MButton;
use Rehike\Signin\API as SignIn;
use Rehike\Util\ExtractUtils;
use Rehike\i18n\i18n;

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

    public function __construct($dataHost)
    {
        $secInfo = &$dataHost::$secondaryInfo;
        $info = $secInfo->owner->videoOwnerRenderer;
        $i18n = i18n::getNamespace("watch");

        $signInInfo = (object) SignIn::getInfo();
        $hasChannel = SignIn::isSignedIn() && isset($signInInfo->ucid);
        if ($hasChannel) $ucid = $signInInfo->ucid;

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
}