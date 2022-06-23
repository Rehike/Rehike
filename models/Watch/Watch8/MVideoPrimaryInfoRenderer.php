<?php
namespace Rehike\Model\Watch\Watch8;

use Rehike\TemplateFunctions;
use Rehike\Model\Common\Subscription\MSubscriptionActions;
use Rehike\Model\Common\MButton;
use Rehike\Model\Common\MToggleButton;
use Rehike\Model\Clickcard\MSigninClickcard;

include_once "controllers/utils/extractUtils.php";

use \ExtractUtils;

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
    public function __construct($dataHost)
    {
        $info = &$dataHost::$primaryInfo ?? null;

        if (!is_null($info))
        {
            $this->title = $info->title ?? null;

            // Also set title of the whole page from this property
            $dataHost::$title = TemplateFunctions::getText($this->title);

            $this->viewCount = $info->viewCount->videoViewCountRenderer->viewCount ?? null;
            $this->badges = $info->badges ?? null;
            $this->superTitle = isset($info->superTitleLink) ? new MSuperTitle($info->superTitleLink) : null;
            $this->likeButtonRenderer = new MLikeButtonRenderer($dataHost, $info->videoActions->menuRenderer);
            $this->owner = new MOwner($dataHost);

            // Create action butttons
            $orderedButtonQueue = [];

            // Share button should always be built unless this is a
            // Kids video
            if (!$dataHost::$isKidsVideo)
            {
                $orderedButtonQueue[] = MActionButton::buildShareButton();
            }

            if (!$dataHost::$isKidsVideo)
            foreach (@$info->videoActions->menuRenderer->topLevelButtons as $b)
            if (isset($b->buttonRenderer) && ($button = @$b->buttonRenderer))
            switch ($button->icon->iconType)
            {
                case "PLAYLIST_ADD":
                    // Push to the beginning of the array
                    // since this should always come first
                    array_unshift($orderedButtonQueue, MActionButton::buildW8AddtoButton());
                    break;
                case "FLAG":
                    $orderedButtonQueue[] = MActionButton::buildReportButton();
                    break;
            }

            $this->actionButtons = &$orderedButtonQueue;
        }
    }
}

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

    /** @var MSubscriptionButton */
    public $subscriptionButtonRenderer;

    public function __construct($dataHost)
    {
        $info = &$dataHost::$secondaryInfo->owner->videoOwnerRenderer;

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

            $this->subscriptionButtonRenderer = MSubscriptionActions::fromData(
                (object)[], $subscribeCount
            );
        }
    }
}

class MActionButton extends MButton
{
    public $style = "opacity";
    public $hasIcon = true;
    public $noIconMarkup = true;
    public $class = [
        "pause-resume-autoplay"
    ];

    public function __construct($data)
    {
        parent::__construct([]);

        $this->setText($data["label"] ?? "");
        $this->tooltip = $data["tooltip"] ?? $data["label"];

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
                $this->class += $data["class"];
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
    }

    public static function buildW8AddtoButton()
    {
        /*
         * TODO (kirasicecreamm): Get sign in status and build
         * clickcard conditionally.
         * 
         * The groundwork is done, so it should just be a simple
         * conditional here.
         */
        return new self([
            "label" => "Add to", // TODO: i18n
            "class" => "addto-button",
            "clickcard" => new MSigninClickcard(
                "Want to watch this again later?",
                "Sign in to add this video to a playlist."
            ),
            "attributes" => [ // Clickcard attributes
                "orientation" => "vertical",
                "position" => "bottomleft"
            ]
        ]);
    }

    public static function buildShareButton()
    {
        return new self([
            "label" => "Share",
            "actionPanelTrigger" => "share"
        ]);
    }

    public static function buildReportButton()
    {
        return new self([
            "label" => "Report",
            "class" => "report-button",
            "actionPanelTrigger" => "report",
            "clickcard" => new MSigninClickcard(
                "Need to report the video?",
                "Sign in to report inappropriate content."
            ),
            "attributes" => [ // Clickcard attributes
                "orientation" => "horizontal",
                "position" => "topright"
            ]
        ]);
    }
}

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

    public function __construct($dataHost, &$info)
    {
        $origLikeButton = &$info->topLevelButtons[0]->toggleButtonRenderer;
        $origDislikeButton = &$info->topLevelButtons[1]->toggleButtonRenderer;

        $likeA11y = $origLikeButton->accessibility->label;
        $dislikeA11y = $origDislikeButton->accessibility->label;

        $isLiked = $origLikeButton->isToggled;
        $isDisliked = $origDislikeButton->isToggled;

        // Extract like count from like count string
        $likeCount = ExtractUtils::isolateLikeCnt($likeA11y ?? "");
        
        if (is_numeric(str_replace(",", "", $likeCount)))
            $likeCountInt = (int)str_replace(",", "", $likeCount);

        // Since December 2021, dislikes are unavailable.
        $dislikeCount = "";

        // Account for RYD API data if it exists
        if ($dataHost::$useRyd && "" !== $likeCount)
        {
            $rydData = &$dataHost::$rydData;

            $dislikeCount = number_format($rydData -> dislikes);

            $dislikeCountInt = (int)$rydData -> dislikes;

            $this->sparkbars = new MSparkbars($likeCountInt, $dislikeCountInt);
        }

        $this->likeButton = new MLikeButton(@$likeCountInt, $likeA11y, !$isLiked);
        $this->activeLikeButton = new MLikeButton(@$likeCountInt, $likeA11y, $isLiked, true);
        $this->dislikeButton = new MDislikeButton(@$dislikeCountInt, $dislikeA11y, !$isDisliked);
        $this->activeDislikeButton = new MDislikeButton(@$dislikeCountInt, $dislikeA11y, $isDisliked, true);
    }
}

class MLikeButtonRendererButton extends MToggleButton
{
    protected $hideNotToggled = true;

    public $style = "opacity";
    public $hasIcon = true;
    public $noIconMarkup = true;
    public $attributes = [
        "orientation" => "vertical",
        "position" => "bottomright",
        "force-position" => "true"
    ];

    public function __construct($type, $active, $count, $state)
    {
        parent::__construct($state);

        $class = "like-button-renderer-" . $type;
        $this->class[] = $class;
        $this->class[] = $class . "-" . ($active ? "clicked" : "unclicked");
        if ($active)
            $this->class[] = "yt-uix-button-toggled";

        if (!is_null($count))
            $this->setText(number_format($count));
    }
}

class MLikeButton extends MLikeButtonRendererButton
{
    public function __construct($likeCount, $a11y, $isLiked, $active = false)
    {
        if ($active) $likeCount++;

        $this->accessibilityAttributes = [
            "label" => $a11y
        ];

        $this->tooltip = "I like this"; // TODO: i18n
        
        if ($active)
            $this->tooltip = "Unlike";

        $signinMessage = "Like this video?";
        $signinDetail = "Sign in to make your opinion count.";

        $signedIn = false; // TODO

        if (!$signedIn && !$active)
        {
            $this->clickcard = new MSigninClickcard($signinMessage, $signinDetail);
        }

        parent::__construct("like-button", $active, $likeCount, $isLiked);
    }
}

class MDislikeButton extends MLikeButtonRendererButton
{
    public function __construct($dislikeCount, $a11y, $isDisliked, $active = false)
    {
        if ($active) $dislikeCount++;

        $this->accessibilityAttributes = [
            "label" => $a11y
        ];

        $this->tooltip = "I dislike this"; // TODO: i18n

        $signinMessage = "Don't like this video?";
        $signinDetail = "Sign in to make your opinion count.";

        $signedIn = false; // TODO

        if (!$signedIn && !$active)
        {
            $this->clickcard = new MSigninClickcard($signinMessage, $signinDetail);
        }

        parent::__construct("dislike-button", $active, $dislikeCount, $isDisliked);
    }
}

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

class MSuperTitle
{
    public $items = [];

    public function __construct($superTitleLink)
    {
        foreach ($superTitleLink->runs as $run) if (" " != $run->text)
        {
            $this->items[] = (object)[
                "text" => preg_replace("/For/", "for", preg_replace("/On/", "on", ucwords(strtolower($run->text)))),
                "url" => TemplateFunctions::getUrl($run)
            ];
        }
    }
}