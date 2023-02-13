<?php
namespace Rehike\Model\Watch\Watch8;

use Rehike\TemplateFunctions;
use Rehike\Model\Common\Subscription\MSubscriptionActions;
use Rehike\Model\Common\MButton;
use Rehike\Model\Common\MToggleButton;
use Rehike\Model\Clickcard\MSigninClickcard;
use Rehike\Model\Common\Menu\MMenu;
use Rehike\Model\Common\Menu\MMenuItem;
use Rehike\ConfigManager\ConfigManager;
use Rehike\Signin\API as SignIn;
use Rehike\Util\ExtractUtils;
use Rehike\i18n;

/**
 * Implement the model for the primary info renderer.
 * 
 * This is generally a restructuring of the Kevlar formatted data
 * returned by InnerTube with WEB v2 as parameters.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MVideoPrimaryInfoRenderer
{
    /** @var string */
    public $title = "";

    /** @var string */
    public $viewCount = "";

    /** @var object[] */
    public $superTitle;

    /** @var mixed */
    public $badges;

    /** @var MOwner */
    public $owner;

    /** @var MActionButton[] */
    public $actionButtons = [];

    /** @var MLikeButtonRenderer */
    public $likeButtonRenderer = [];

    // dataHost == get_called_class of caller
    public function __construct($dataHost, $videoId)
    {
        $info = &$dataHost::$primaryInfo ?? null;
        $i18n = i18n::newNamespace("watch/primary");
        $i18n->registerFromFolder("i18n/watch");

        if (!is_null($info))
        {
            $this->title = $info->title ?? null;

            // Also set title of the whole page from this property
            $dataHost::$title = TemplateFunctions::getText($this->title);

            if (isset($info->viewCount->videoViewCountRenderer))
            $this->viewCount = (true === ConfigManager::getConfigProp("appearance.noViewsText"))
            ? ExtractUtils::isolateViewCnt(TemplateFunctions::getText($info->viewCount->videoViewCountRenderer->viewCount))
            : TemplateFunctions::getText($info->viewCount->videoViewCountRenderer->viewCount) ?? null;
            $this->badges = $info->badges ?? null;
            $this->superTitle = isset($info->superTitleLink) ? new MSuperTitle($info->superTitleLink) : null;
            $this->likeButtonRenderer = new MLikeButtonRenderer($dataHost, $info->videoActions->menuRenderer, $videoId);
            $this->owner = new MOwner($dataHost);

            // Create action butttons
            $orderedButtonQueue = [];

            /*
             * Updated to just hardcode this (it's generally fine, any video can be added to a playlist)
             * 
             * To elaborate, the previous implementation iterated the available action buttons from
             * the InnerTube response, which usually worked.
             * 
             * However, as of August 2022, YouTube has been experimenting with a rewrite of the action
             * buttons architecture, seemingly in an attempt to fix the Kevlar frontend watch page
             * imploding cuz they have 12 action buttons with full labels on the screen and typically
             * only report and sometimes transcript in the overflow menu.
             * 
             * No, of course they didn't bother fixing this in the most intuitive ways, like having
             * hidden or dynamic label visibility (even though they always tell you what they are
             * if you hover over the buttons anyways so THERE IS NO FUCKING POINT TO THE ADDITIONAL
             * LABEL), instead they added a new type of button called "menuFlexibleItemRenderer" inside
             * a new container of "flexibleItems".
             * 
             * It seems that, instead of the most obvious change of going from:
             * 
             * 👍 12K 👎 DISLIKE ➡️ SHARE ⬇️ DOWNLOAD ❤️ THANKS ✂️ CLIP ➕ SAVE  ...
             * 
             * to:
             * 
             * 👍 12K 👎  ➡️  ⬇️  ❤️  ✂️  ➕   ...
             * 
             * or (they will never actually use the overflow menu tho):
             * 
             * 👍 12K 👎  ➡️  ➕   ...
             *          ┌───────────────┐
             *          │ 🚩 Report     │
             *          │ 🗒️ Transcript │
             *          │ ⬇️ Download   │
             *          │ ❤️ Thanks     │
             *          │ ✂️ Clip       │
             *          └───────────────┘
             * 
             * They instead decided to rework the API to "lazily" fix their broken ass frontend,
             * and this is why sometimes "add to" would randomly not render, because this experiment
             * only applies to ~1/10th of the unique visitor IDs.
             */
            if (!$dataHost::$isKidsVideo)
            {
                $orderedButtonQueue[] = MActionButton::buildAddtoButton($videoId);
            }

            // Share button should always be built unless this is a
            // Kids video
            if (!$dataHost::$isKidsVideo)
            {
                $shareButton = MActionButton::buildShareButton();

                if (null != $shareButton) $orderedButtonQueue[] = $shareButton;
            }

            // Report button shows as an action button for livestreams, rather than
            // residing in the overflow menu.
            if ($dataHost::$isLive)
            {
                $orderedButtonQueue[] = MActionButton::buildReportButton();
            }
            else
            {
                $orderedButtonQueue[] = MActionButton::buildMoreButton();
            }

            $this->actionButtons = &$orderedButtonQueue;
        }
    }
}

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

    public function __construct($dataHost)
    {
        $secInfo = &$dataHost::$secondaryInfo;
        $info = $secInfo->owner->videoOwnerRenderer;
        $i18n = i18n::getNamespace("watch/primary");

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
                ? ExtractUtils::isolateSubCnt(TemplateFunctions::getText($info->subscriberCountText))
                : null
            ;

            // Build the subscription button from the InnerTube data.
            if (!SignIn::isSignedIn()) {
                $this->subscriptionButtonRenderer = MSubscriptionActions::signedOutStub($subscribeCount);
            } else if (isset($secInfo->subscribeButton->subscribeButtonRenderer)) {
                $this->subscriptionButtonRenderer = MSubscriptionActions::fromData($secInfo->subscribeButton->subscribeButtonRenderer, $subscribeCount);
            } else if (isset($secInfo->subscribeButton->buttonRenderer)) { // channel settings button
                $this->channelSettingsButtonRenderer = new MButton((object) [
                    "style" => "default",
                    "size" => "default",
                    "text" => (object) [
                        "simpleText" => $i18n->channelSettings
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
                    ]
                ]);
            }
        }
    }
}

/**
 * Defines an abstract watch action button.
 * 
 * These are the buttons for watch interaction, such
 * as add to and share.
 * 
 * TODO(dcooper): more action button
 */
class MActionButton extends MButton
{
    // Define default button properties.
    public $style = "STYLE_OPACITY";
    public $icon;
    public $class = [
        "pause-resume-autoplay"
    ];

    public function __construct($data)
    {
        parent::__construct([]);

        $this->icon = (object) [];

        // Set the button data as provided.
        $this->setText($data["label"] ?? "");
        $this->tooltip = $data["tooltip"] ?? $data["label"];

        $this->targetId = $data["id"] ?? null;

        // Push provided attributes if they exist.
        if (@$data["attributes"])
        foreach ($data["attributes"] as $key => $value)
        {
            $this->attributes += [$key => $value];
        }

        if (@$data["class"])
        {
            if (is_string($data["class"]))
            {
                $this->class[] = $data["class"];
            }
            else
            {
                /*
                 * BUG (kirasicecreamm): This used += operator to
                 * append the arrays, which is useful for associative,
                 * but not numerical arrays.
                 * 
                 * This caused it to ignore the 0th item and so on
                 * as it conflicted with the preexisting index in
                 * this parent class.
                 */
                $this->class = array_merge($this->class, $data["class"]);
            }
        }

        if (@$data["actionPanelTrigger"])
        {
            $this->class[] = "action-panel-trigger";
            $this->class[] = "action-panel-trigger-" . $data["actionPanelTrigger"];

            if (!@$data["fakeActionPanel"])
            $this->attributes["trigger-for"] = "action-panel-" . $data["actionPanelTrigger"];
            
            // Watch8 only: (if doing w7 in the future edit this)
            $this->attributes["button-toggle"] = "true";
        }

        if (@$data["clickcard"])
        {
            $this->clickcard = &$data["clickcard"];
        }

        if (@$data["videoActionsMenu"])
        {
            $this->videoActionsMenu = &$data["videoActionsMenu"];
        }

        if (@$data["menu"])
        {
            $this->menu = &$data["menu"];
        }
    }

    /**
     * Build a watch8 add to playlists button, or its signed out
     * stub.
     * 
     * @return void
     */
    public static function buildAddtoButton($videoId)
    {
        $i18n = i18n::getNamespace("watch/primary");

        $buttonCfg = [
            "label" => $i18n->get("actionAddTo"),
            "class" => []
        ];

        if (!SignIn::isSignedIn())
        {
            $buttonCfg += [
                "clickcard" => new MSigninClickcard(
                    $i18n->addToClickcardHeading,
                    $i18n->addToClickcardTip,
                    [
                        "text" => $i18n->clickcardSignIn,
                        "href" => "https://accounts.google.com/ServiceLogin?continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fnext%3D%252F%253Faction_handle_signin%3Dtrue%26feature%3D__FEATURE__%26hl%3Den%26app%3Ddesktop&passive=true&hl=en&uilel=3&service=youtube"
                    ]
                ),
                "attributes" => [ // Clickcard attributes
                    "orientation" => "vertical",
                    "position" => "bottomleft"
                ]
            ];
        }
        else
        {
            $buttonCfg += [
                "videoActionsMenu" => (object)[
                    "contentId" => "yt-uix-videoactionmenu-menu",
                    "videoId" => $videoId
                ]
            ];

            $buttonCfg["class"] += [
                "yt-uix-menu-trigger",
                "yt-uix-videoactionmenu-button"
            ];
        }

        $buttonCfg["class"][] = "addto-button";

        return new self($buttonCfg);
    }

    /**
     * Build a watch8 share button.
     * 
     * @return MActionButton|null
     */
    public static function buildShareButton()
    {
        $i18n = i18n::getNamespace("watch/primary");

        return new self([
            "label" => $i18n->actionShare,
            "actionPanelTrigger" => "share"
        ]);
    }

    /**
     * Build a watch8 report button, which is used on livestreams.
     * 
     * If the video is not a livestream, then the report button appears in
     * the more button's menu instead.
     * 
     * @return MActionButton
     */
    public static function buildReportButton()
    {
        $i18n = i18n::getNamespace("watch/primary");

        $buttonCfg = [
            "label" => $i18n->actionReport,
            "class" => "report-button",
            "actionPanelTrigger" => "report"
        ];

        if (!SignIn::isSignedIn()) {
            $buttonCfg += [
                "fakeActionPanel" => true,
                "clickcard" => new MSigninClickcard(
                    $i18n->reportClickcardHeading,
                    $i18n->reportClickcardTip,
                    [
                        "text" => $i18n->clickcardSignIn,
                        "href" => "https://accounts.google.com/ServiceLogin?continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fnext%3D%252F%253Faction_handle_signin%3Dtrue%26feature%3D__FEATURE__%26hl%3Den%26app%3Ddesktop&passive=true&hl=en&uilel=3&service=youtube"
                    ]
                ),
                "attributes" => [ // Clickcard attributes
                    "orientation" => "horizontal",
                    "position" => "topright"
                ]
            ];
        }

        return new self($buttonCfg);
    }

    /**
     * Build a watch8 more button.
     */
    public static function buildMoreButton()
    {
        $i18n = i18n::getNamespace("watch/primary");

        return new self([
            "label" => $i18n->actionMore,
            "tooltip" => $i18n->actionMoreTooltip,
            "id" => "action-panel-overflow-button",
            "menu" => new MActionPanelOverflowMenu()
        ]);
    }
}

class MActionPanelOverflowMenu extends MMenu {
    public $menuId = "action-panel-overflow-menu";

    public function __construct() {
        $i18n = i18n::getNamespace("watch/primary");

        $reportCfg = [
            "label" => $i18n->actionReport,
            "actionPanelTrigger" => "report",
            "hasIcon" => true
        ];

        if (SignIn::isSignedIn()) {
            $reportCfg += [
                "closeOnSelect" => true
            ];
        } else {
            $reportCfg += [
                "clickcard" => new MSigninClickcard(
                    $i18n->reportClickcardHeading,
                    $i18n->reportClickcardTip,
                    [
                        "text" => $i18n->clickcardSignIn,
                        "href" => "https://accounts.google.com/ServiceLogin?continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fnext%3D%252F%253Faction_handle_signin%3Dtrue%26feature%3D__FEATURE__%26hl%3Den%26app%3Ddesktop&passive=true&hl=en&uilel=3&service=youtube"
                    ]
                ),
                "attributes" => [ // Clickcard attributes
                    "orientation" => "horizontal",
                    "position" => "topright"
                ],
                "fakeActionPanel" => true
            ];
        }

        $this->items[] = new MMenuItem($reportCfg);
        $this->items[] = new MMenuItem([
            "actionPanelTrigger" => "transcript",
            "closeOnSelect" => true,
            "label" => $i18n->actionTranscript,
            "hasIcon" => true
        ]);
    }
}

/**
 * Defines the like button (and dislike button) container.
 * 
 * This stores two copies of both the like and dislike buttons, which are
 * used for their individual activation states. This is how hitchhiker
 * handled this.
 */
class MLikeButtonRenderer
{
    /** @var MLikeButton */
    public $likeButton;
    public $activeLikeButton;

    /** @var MDislikeButton */
    public $dislikeButton;
    public $activeDislikeButton;

    /** @var MSparkbars */
    public $sparkbars;

    public function __construct($dataHost, &$info, &$videoId)
    {
        if (isset($info->topLevelButtons[0]->segmentedLikeDislikeButtonRenderer)) {
            $origLikeButton = $info->topLevelButtons[0]->segmentedLikeDislikeButtonRenderer->likeButton->toggleButtonRenderer ?? null;
            $origDislikeButton = $info->topLevelButtons[0]->segmentedLikeDislikeButtonRenderer->dislikeButton->toggleButtonRenderer ?? null;
        } else {
            $origLikeButton = $info->topLevelButtons[0]->toggleButtonRenderer ?? null;
            $origDislikeButton = $info->topLevelButtons[0]->toggleButtonRenderer ?? null;
        }

        $likeA11y = $origLikeButton->accessibility->label ?? "";
        $dislikeA11y = $origDislikeButton->accessibility->label ?? "";

        $isLiked = $origLikeButton->isToggled ?? false;
        $isDisliked = $origDislikeButton->isToggled ?? false;

        // Extract like count from like count string
        $likeCount = ExtractUtils::isolateLikeCnt($likeA11y ?? "");
        
        if (is_numeric(str_replace(",", "", $likeCount)))
            $likeCountInt = (int)str_replace(",", "", $likeCount);

        // Account for RYD API data if it exists
        if ($dataHost::$useRyd && "" !== $likeCount)
        {
            $rydData = &$dataHost::$rydData;

            $dislikeCountInt = (int)$rydData->dislikes;

            $this->sparkbars = new MSparkbars($likeCountInt, $dislikeCountInt);
        }

        $this->likeButton = new MLikeButton(@$likeCountInt, $likeA11y, !$isLiked, $videoId);
        $this->activeLikeButton = new MLikeButton(@$likeCountInt, $likeA11y, $isLiked, $videoId, true);
        $this->dislikeButton = new MDislikeButton(@$dislikeCountInt, $dislikeA11y, !$isDisliked, $videoId);
        $this->activeDislikeButton = new MDislikeButton(@$dislikeCountInt, $dislikeA11y, $isDisliked, $videoId, true);
    }
}

/**
 * Define an abstract actual "like button" button (also used for dislikes).
 */
class MLikeButtonRendererButton extends MToggleButton
{
    protected $hideNotToggled = true;

    public $style = "opacity";
    public $icon;
    public $attributes = [
        "orientation" => "vertical",
        "position" => "bottomright",
        "force-position" => "true"
    ];

    public function __construct($type, $active, $count, $state)
    {
        parent::__construct($state);

        $this->icon = (object) [];

        $class = "like-button-renderer-" . $type;
        $this->class[] = $class;
        $this->class[] = $class . "-" . ($active ? "clicked" : "unclicked");
        if ($active)
            $this->class[] = "yt-uix-button-toggled";

        if (!is_null($count))
            $this->setText(number_format($count));
    }
}

/**
 * Define the like button.
 */
class MLikeButton extends MLikeButtonRendererButton
{
    public function __construct($likeCount, $a11y, $isLiked, $videoId, $active = false)
    {
        $i18n = i18n::getNamespace("watch/primary");

        if ($active && is_numeric($likeCount)) $likeCount++;

        $this->accessibility = (object) [
            "accessibilityData" => (object) [
                "label" => $a11y
            ]
        ];

        $this->tooltip = $i18n->actionLikeTooltip;
        
        if ($active)
            $this->tooltip = $i18n->actionLikeTooltipActive;

        $signinMessage = $i18n->likeClickcardHeading;
        $signinDetail = $i18n->voteClickcardTip;

        // Store a reference to the current sign in state.
        $signedIn = SignIn::isSignedIn();

        if ($signedIn) {
            $this->attributes["post-action"] = "/service_ajax?name=likeEndpoint";
            $this->class[] = "yt-uix-post-anchor";
        }

        if (!$signedIn && !$active) {
            $this->clickcard = new MSigninClickcard($signinMessage, $signinDetail, [
                "text" => $i18n->clickcardSignIn,
                "href" => "https://accounts.google.com/ServiceLogin?continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fnext%3D%252F%253Faction_handle_signin%3Dtrue%26feature%3D__FEATURE__%26hl%3Den%26app%3Ddesktop&passive=true&hl=en&uilel=3&service=youtube"
            ]);
        } elseif ($signedIn && !$active) {
            $this->attributes["post-data"] = "action=like&id=" . $videoId;
        } elseif ($signedIn && $active) {
            $this->attributes["post-data"] = "action=removelike&id=" . $videoId;
        }

        parent::__construct("like-button", $active, $likeCount, $isLiked);
    }
}

/**
 * Define the dislike button.
 */
class MDislikeButton extends MLikeButtonRendererButton
{
    public function __construct($dislikeCount, $a11y, $isDisliked, $videoId, $active = false)
    {
        $i18n = i18n::getNamespace("watch/primary");

        if ($active && is_numeric($dislikeCount)) $dislikeCount++;

        $this->accessibilityAttributes = [
            "label" => $a11y
        ];

        $this->tooltip = $i18n->actionDislikeTooltip; // TODO: i18n

        $signinMessage = $i18n->dislikeClickcardHeading;
        $signinDetail = $i18n->voteClickcardTip;

        // Store a reference to the current sign in state.
        $signedIn = SignIn::isSignedIn();

        if ($signedIn) {
            $this->attributes["post-action"] = "/service_ajax?name=likeEndpoint";
            $this->class[] = "yt-uix-post-anchor";
        }

        if (!$signedIn && !$active) {
            $this->clickcard = new MSigninClickcard($signinMessage, $signinDetail, [
                "text" => $i18n->clickcardSignIn,
                "href" => "https://accounts.google.com/ServiceLogin?continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fnext%3D%252F%253Faction_handle_signin%3Dtrue%26feature%3D__FEATURE__%26hl%3Den%26app%3Ddesktop&passive=true&hl=en&uilel=3&service=youtube"
            ]);
        } elseif ($signedIn && !$active) {
            $this->attributes["post-data"] = "action=dislike&id=" . $videoId;
        } elseif ($signedIn && $active) {
            $this->attributes["post-data"] = "action=removedislike&id=" . $videoId;
        }

        parent::__construct("dislike-button", $active, $dislikeCount, $isDisliked);
    }
}

/**
 * Define the sparkbars (sentiment bars; like to dislike ratio bar).
 */
class MSparkbars
{
    /** @var float */
    public $likePercent = 50;
    
    /** @var float */
    public $dislikePercent = 50;
    
    public function __construct($likeCount, $dislikeCount)
    {
        if (0 != $likeCount + $dislikeCount)
        {
            $this->likePercent = ($likeCount / ($likeCount + $dislikeCount)) * 100;
            $this->dislikePercent = 100 - $this->likePercent;
        }
    }
}

/**
 * Define the super title (links that appear above the title, such as hashtags).
 */
class MSuperTitle
{
    public $items = [];

    public function __construct($superTitleLink)
    {
        foreach ($superTitleLink->runs as $run) if (" " != $run->text)
        {
            $this->items[] = (object)[
                "text" => $run->text,
                "url" => TemplateFunctions::getUrl($run)
            ];
        }
    }
}