<?php
namespace Rehike\Model\Watch\Watch8;

use Rehike\TemplateFunctions;
use Rehike\Model\Common\Subscription\MSubscriptionActions;
use Rehike\Model\Common\MButton;
use Rehike\Model\Common\MToggleButton;
use Rehike\Model\Clickcard\MSigninClickcard;
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
        $i18n -> registerFromFolder("i18n/watch");

        if (!is_null($info))
        {
            $this->title = $info->title ?? null;

            // Also set title of the whole page from this property
            $dataHost::$title = TemplateFunctions::getText($this->title);

            $this->viewCount = (true === ConfigManager::getConfigProp("noViewsText")) ? ExtractUtils::isolateViewCnt(TemplateFunctions::getText($info->viewCount->videoViewCountRenderer->viewCount)) : TemplateFunctions::getText($info->viewCount->videoViewCountRenderer->viewCount) ?? null;
            $this->badges = $info->badges ?? null;
            $this->superTitle = isset($info->superTitleLink) ? new MSuperTitle($info->superTitleLink) : null;
            $this->likeButtonRenderer = new MLikeButtonRenderer($dataHost, $info->videoActions->menuRenderer, $videoId);
            $this->owner = new MOwner($dataHost);

            // Create action butttons
            $orderedButtonQueue = [];

            // Action button menu
            $menu = $info -> videoActions -> menuRenderer ?? null;

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
                $orderedButtonQueue[] = MActionButton::buildW8AddtoButton($menu);//$videoId);
            }

            // Share button should always be built unless this is a
            // Kids video
            if (!$dataHost::$isKidsVideo)
            {
                $shareButton = MActionButton::buildShareButton($menu);

                if (null != $shareButton) $orderedButtonQueue[] = $shareButton;
            }

            // Report button shows as an action button for livestreams, rather than
            // residing in the overflow menu.
            if ($dataHost::$isLive)
            {
                $orderedButtonQueue[] = MActionButton::buildReportButton($menu);
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
                $this->subscriptionButtonRenderer = MSubscriptionActions::fromData((object)[], $subscribeCount);
                // TODO: signin url (as href property)
            } else if (isset($secInfo -> subscribeButton -> subscribeButtonRenderer)) {
                $this->subscriptionButtonRenderer = MSubscriptionActions::fromData($secInfo -> subscribeButton -> subscribeButtonRenderer, $subscribeCount);
            } else if (isset($secInfo -> subscribeButton -> buttonRenderer)) { // channel settings button
                $this->subscriptionButtonRenderer = MSubscriptionActions::buildMock($subscribeCount);
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
    }

    /**
     * Find an action button based on its icon type.
     * 
     * @var  object  $menu      menuRenderer containing action buttons
     * @var  string  $iconType  Icon type of the action button
     * 
     * @return object
     */
    public static function findActionButton($menu, $iconType) {
        $topLevelButtons = $menu -> topLevelButtons ?? null;
        $flexibleItems = $menu -> flexibleItems ?? null;

        // Look in top level buttons
        if (!is_null($topLevelButtons)) for ($i = 0; $i < count($topLevelButtons); $i++) {
            $curIconType = $topLevelButtons[$i] -> buttonRenderer -> icon -> iconType ?? null;
            if (!is_null($curIconType) && $curIconType == $iconType) {
                return $topLevelButtons[$i] -> buttonRenderer;
            }
        }

        // Look in "flexible items"
        if (!is_null($flexibleItems)) for ($i = 0; $i < count($flexibleItems); $i++) {
            $curIconType = $flexibleItems[$i] -> menuFlexibleItemRenderer -> topLevelButton -> buttonRenderer -> icon -> iconType ?? null;
            if (!is_null($curIconType) && $curIconType == $iconType) {
                return $flexibleItems[$i] -> menuFlexibleItemRenderer -> topLevelButton -> buttonRenderer;
            }
        }

        // Found nothing! >:[
        return null;
    }

    /**
     * Build a watch8 add to playlists button, or its signed out
     * stub.
     * 
     * @return void
     */
    public static function buildW8AddtoButton($menu)
    {
        $button = self::findActionButton($menu, "PLAYLIST_ADD");
        $i18n = i18n::getNamespace("watch/primary");

        if (is_null($button)) return null;

        $buttonCfg = [
            "label" => $i18n -> get("actionAddTo"),
            "class" => []
        ];

        if (!SignIn::isSignedIn())
        {
            $buttonCfg += [
                "clickcard" => MSigninClickcard::fromData($button -> command -> modalEndpoint -> modal -> modalWithTitleAndButtonRenderer ?? null),
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
                    "videoId" => $button -> command -> addToPlaylistServiceEndpoint -> videoId ?? null
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
     * Build a watch7 or watch8 share button.
     * 
     * @return MActionButton|null
     */
    public static function buildShareButton($menu)
    {
        $button = self::findActionButton($menu, "SHARE");

        if (!isset($button)) return null;

        return new self([
            "label" => TemplateFunctions::getText($button -> text),
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
    public static function buildReportButton($menu)
    {
        $button = self::findActionButton($menu, "FLAG");

        return new self([
            "label" => TemplateFunctions::getText($button -> text),
            "class" => "report-button",
            "actionPanelTrigger" => "report",
            "clickcard" => MSigninClickcard::fromData($button -> command -> modalEndpoint -> modal -> modalWithTitleAndButtonRenderer ?? null),
            "attributes" => [ // Clickcard attributes
                "orientation" => "horizontal",
                "position" => "topright"
            ]
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
        $origLikeButton = &$info->topLevelButtons[0]->segmentedLikeDislikeButtonRenderer->likeButton->toggleButtonRenderer;
        $origDislikeButton = &$info->topLevelButtons[0]->segmentedLikeDislikeButtonRenderer->dislikeButton->toggleButtonRenderer;

        $likeA11y = $origLikeButton->accessibility->label;
        $dislikeA11y = $origDislikeButton->accessibility->label;

        $isLiked = $origLikeButton->isToggled;
        $isDisliked = $origDislikeButton->isToggled;

        // Extract like count from like count string
        $likeCount = ExtractUtils::isolateLikeCnt($likeA11y ?? "");
        
        if (is_numeric(str_replace(",", "", $likeCount)))
            $likeCountInt = (int)str_replace(",", "", $likeCount);

        // Account for RYD API data if it exists
        if ($dataHost::$useRyd && "" !== $likeCount)
        {
            $rydData = &$dataHost::$rydData;

            $dislikeCountInt = (int)$rydData -> dislikes;

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

        $this -> icon = (object) [];

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
        if ($active && is_numeric($likeCount)) $likeCount++;

        $this->accessibility = (object) [
            "data" => (object) [
                "label" => $a11y
            ]
        ];

        $this->tooltip = "I like this"; // TODO: i18n
        
        if ($active)
            $this->tooltip = "Unlike";

        $signinMessage = "Like this video?";
        $signinDetail = "Sign in to make your opinion count.";

        // Store a reference to the current sign in state.
        $signedIn = SignIn::isSignedIn();

        if ($signedIn) {
            $this -> attributes["post-action"] = "/service_ajax?name=likeEndpoint";
            $this -> class[] = "yt-uix-post-anchor";
        }

        if (!$signedIn && !$active) {
            $this->clickcard = new MSigninClickcard($signinMessage, $signinDetail, null);
        } elseif ($signedIn && !$active) {
            $this -> attributes["post-data"] = "action=like&id=" . $videoId;
        } elseif ($signedIn && $active) {
            $this -> attributes["post-data"] = "action=removelike&id=" . $videoId;
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
        if ($active && is_numeric($dislikeCount)) $dislikeCount++;

        $this->accessibilityAttributes = [
            "label" => $a11y
        ];

        $this->tooltip = "I dislike this"; // TODO: i18n

        $signinMessage = "Don't like this video?";
        $signinDetail = "Sign in to make your opinion count.";

        // Store a reference to the current sign in state.
        $signedIn = SignIn::isSignedIn();

        if ($signedIn) {
            $this -> attributes["post-action"] = "/service_ajax?name=likeEndpoint";
            $this -> class[] = "yt-uix-post-anchor";
        }

        if (!$signedIn && !$active) {
            $this->clickcard = new MSigninClickcard($signinMessage, $signinDetail, null);
        } elseif ($signedIn && !$active) {
            $this -> attributes["post-data"] = "action=dislike&id=" . $videoId;
        } elseif ($signedIn && $active) {
            $this -> attributes["post-data"] = "action=removedislike&id=" . $videoId;
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