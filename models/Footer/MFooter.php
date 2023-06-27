<?php
namespace Rehike\Model\Footer;

use Rehike\i18n;
use Rehike\Model\Common\MButton;
use Rehike\ConfigManager\ConfigManager;
use Rehike\Player\Configurable;

class MFooter {
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

    public function __construct() {
        $i18n = i18n::newNamespace("footer");
        $i18n->registerFromFolder("i18n/footer");
        $rehikeVersion = (object) \Rehike\Version\VersionController::$versionInfo;
        $rehikeVersion->semanticVersion = \Rehike\Version\VersionController::getVersion();

        $this->logoTooltip = $i18n->logoTooltip;

        $this->primaryLinks[] = new MFooterLink(
            $i18n->primaryAbout,
            "/yt/about/"
        );
        $this->primaryLinks[] = new MFooterLink(
            $i18n->primaryPress,
            "/yt/press/"
        );
        $this->primaryLinks[] = new MFooterLink(
            $i18n->primaryCopyright,
            "/yt/copyright/"
        );
        $this->primaryLinks[] = new MFooterLink(
            $i18n->primaryCreators,
            "/yt/creators/"
        );
        $this->primaryLinks[] = new MFooterLink(
            $i18n->primaryAdvertise,
            "/yt/advertise/"
        );
        $this->primaryLinks[] = new MFooterLink(
            $i18n->primaryDevs,
            "/yt/dev/"
        );
        if (ConfigManager::getConfigProp("appearance.showVersionInFooter"))
        $this->primaryLinks[] = new MFooterLink(
            $i18n->primaryVersion($rehikeVersion->semanticVersion, $rehikeVersion->branch),
            "/rehike/version"
        );
        $this->secondaryLinks[] = new MFooterLink(
            $i18n->secondaryTerms,
            "/t/terms"
        );
        $this->secondaryLinks[] = new MFooterLink(
            $i18n->secondaryPrivacy,
            "//www.google.com/intl/en/policies/privacy/"
        );
        $this->secondaryLinks[] = new MFooterLink(
            $i18n->secondaryPolicySafety,
            "/yt/policyandsafety"
        );
        $this->secondaryLinks[] = new MFooterLink(
            $i18n->secondaryFeedback,
            "//support.google.com/youtube/"
        );
        $this->secondaryLinks[] = new MFooterLink(
            $i18n->secondaryTestTube,
            "/new"
        );
        $this->copyright = $i18n->secondaryCopyright(date("Y"));
        $this->buttons[] = new MHistoryButton();
        $this->buttons[] = new MHelpButton();
    }
}