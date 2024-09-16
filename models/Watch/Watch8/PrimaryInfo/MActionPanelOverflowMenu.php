<?php
namespace Rehike\Model\Watch\Watch8\PrimaryInfo;

use Rehike\Model\Clickcard\MSigninClickcard;
use Rehike\Model\Common\Menu\MMenu;
use Rehike\Model\Common\Menu\MMenuItem;
use Rehike\SignInV2\SignIn;
use Rehike\i18n\i18n;
use Rehike\YtApp;

class MActionPanelOverflowMenu extends MMenu
{
    public $menuId = "action-panel-overflow-menu";

    public function __construct()
    {
        $i18n = i18n::getNamespace("watch");

        $reportCfg = [
            "label" => $i18n->get("actionReport"),
            "actionPanelTrigger" => "report",
            "hasIcon" => true
        ];

        if (SignIn::isSignedIn())
        {
            $reportCfg += [
                "closeOnSelect" => true
            ];
        }
        else
        {
            $reportCfg += [
                "clickcard" => new MSigninClickcard(
                    $i18n->get("reportClickcardHeading"),
                    $i18n->get("reportClickcardTip"),
                    [
                        "text" => $i18n->get("clickcardSignIn"),
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

        if (isset(YtApp::getInstance()->playerResponse->captions->playerCaptionsTracklistRenderer->captionTracks[0]->baseUrl))
        {
            $this->items[] = new MMenuItem([
                "actionPanelTrigger" => "transcript",
                "closeOnSelect" => true,
                "label" => $i18n->get("actionTranscript"),
                "hasIcon" => true
            ]);
        }
    }
}