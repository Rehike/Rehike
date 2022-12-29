<?php
namespace Rehike\Model\Watch\Watch8\PrimaryInfo;

use Rehike\Model\Common\MButton;
use Rehike\Model\Clickcard\MSigninClickcard;
use Rehike\Signin\API as SignIn;
use Rehike\i18n;

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