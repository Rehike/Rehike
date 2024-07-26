<?php
namespace Rehike\Model\Footer;

use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;
use Rehike\ConfigManager\Config;
use Rehike\Player\Configurable;

class MFooter
{
    /** @var string */
    public $logoTooltip;

    /** @var MPickerButton[] */
    public $pickers = [];

    /** @var MButton[] */
    public $buttons = [];

    /** @var object[] */
    public $primaryLinks = [];

    /** @var object[] */
    public $secondaryLinks = [];

    /** @var string */
    public $copyright;

    /** @var bool */
    public $enableCopyright;

    public function __construct()
    {
        $i18n = i18n::getNamespace("footer");
        $rehikeVersion = \Rehike\Version\VersionController::$versionInfo;
        $rehikeVersion->semanticVersion = \Rehike\Version\VersionController::getVersion();

        $this->logoTooltip = $i18n->get("logoTooltip");
        
        // $this->pickers[] = new MPickerLanguageButton();
        // $this->pickers[] = new MPickerCountryButton();
        // $this->pickers[] = new MPickerSafetyButton();

        $this->primaryLinks[] = new MFooterLink(
            $i18n->get("primaryAbout"),
            "/yt/about/"
        );
        $this->primaryLinks[] = new MFooterLink(
            $i18n->get("primaryPress"),
            "/yt/press/"
        );
        $this->primaryLinks[] = new MFooterLink(
            $i18n->get("primaryCopyright"),
            "/yt/copyright/"
        );
        $this->primaryLinks[] = new MFooterLink(
            $i18n->get("primaryCreators"),
            "/yt/creators/"
        );
        $this->primaryLinks[] = new MFooterLink(
            $i18n->get("primaryAdvertise"),
            "/yt/advertise/"
        );
        $this->primaryLinks[] = new MFooterLink(
            $i18n->get("primaryDevs"),
            "/yt/dev/"
        );

        if (Config::getConfigProp("appearance.showVersionInFooter"))
        {
            $this->primaryLinks[] = new MFooterLink(
                $i18n->format(
                    "primaryVersion",
                    $rehikeVersion->semanticVersion ?? "",
                    $rehikeVersion->branch ?? ""
                ),
                "/rehike/version"
            );
        }
        
        $this->secondaryLinks[] = new MFooterLink(
            $i18n->get("secondaryTerms"),
            "/t/terms"
        );
        $this->secondaryLinks[] = new MFooterLink(
            $i18n->get("secondaryPrivacy"),
            "//www.google.com/intl/en/policies/privacy/"
        );
        $this->secondaryLinks[] = new MFooterLink(
            $i18n->get("secondaryPolicySafety"),
            "/yt/policyandsafety"
        );
        $this->secondaryLinks[] = new MFooterLink(
            $i18n->get("secondaryFeedback"),
            "//support.google.com/youtube/"
        );
        $this->secondaryLinks[] = new MFooterLink(
            $i18n->get("secondaryTestTube"),
            "/new"
        );
        $this->copyright = $i18n->format("secondaryCopyright", date("Y"));
        $this->buttons[] = new MHistoryButton();
        $this->buttons[] = new MHelpButton();
    }
}