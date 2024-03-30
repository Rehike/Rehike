<?php
namespace Rehike\Model\Masthead;

use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;
use Rehike\Signin\API as SignIn;
use Rehike\ConfigManager\Config;
use Rehike\YtApp;

use Rehike\Model\Masthead\{
    AccountPicker\MAccountPickerButton,
    CreationMenu\MCreationMenu,
    Notifications\MNotificationButton,
    UploadButton\MUploadButton,
    UploadButton\MUploadIconButton
};

class MMasthead
{
    /** @var string */
    public $a11ySkipNav;

    /** @var MAppbarGuideToggle */
    public $guideToggle;

    /** @var object */
    public $logoTooltip;

    /** @var string */
    public $countryCode;

    /** @var MMastheadSearch */
    public $searchbox;

    /** @var MButton[] */
    public $buttons = [];

    /** @var object */
    public $notificationStrings;

    public function __construct($appbarEnabled)
    {
        $i18n = i18n::getNamespace("masthead");

        $this->a11ySkipNav = $i18n->get("a11ySkipNav");

        if ($appbarEnabled)
            $this->guideToggle = new MAppbarGuideToggle();

        $this->logoTooltip = $i18n->get("logoTooltip");
        $this->searchbox = new MMastheadSearchbox();

        $this->notificationStrings = (object) [
            "none" => $i18n->get("notificationsNone"),
            "singular" => $i18n->get("notificationsSingular"),
            "plural" => $i18n->get("notificationsPlural"),
        ];

        switch (Config::getConfigProp("appearance.uploadButtonType"))
        {
            case "BUTTON":
                $this->buttons[] = new MUploadButton();
                break;
            case "ICON":
                $this->buttons[] = new MUploadIconButton();
                break;
            default:
                $this->buttons[] = new MCreationMenu();
                break;
        }

        $yt = YtApp::getInstance();
        
        if ("US" != $yt->gl)
        {
            $this->countryCode = $yt->gl;
        }

        if (SignIn::isSignedIn())
        {
            $this->buttons[] = new MNotificationButton();
            $this->buttons[] = new MAccountPickerButton();
        }
        else
        {
            $this->buttons[] = new MSignInButton();
        }
    }
}