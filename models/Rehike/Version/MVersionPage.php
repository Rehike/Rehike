<?php
namespace Rehike\Model\Rehike\Version;

use Rehike\Model\Rehike\Panel\RehikePanelPage;

use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;
use Rehike\Version\VersionInfo;

use const Rehike\Constants\IS_RELEASE;
use const Rehike\Constants\GH_ENABLED;
use const Rehike\Constants\GH_REPO;

class MVersionPage extends RehikePanelPage
{
    public $headingText;
    public MButton $githubButton;
    public MButton $licenseInfoButton;
    public $brandName;
    public $version = ""; // This gets replaced later
    public $nightlyNotice;
    public $nightlyInfo;
    public $failedNotice;
    public $nonGitNotice;
    public MRuntimeInfo $runtimeInfo;

    protected $isNightly = false;

    public function __construct(?VersionInfo $data)
    {
        parent::__construct("rehike-version");
        $strings = i18n::getNamespace("rehike/version");

        $this->headingText = $strings->get("headingVersionInfo");
        $this->brandName = $strings->get("brandName");

        if (@$data->semanticVersion)
        {
            $this->version = $strings->format("versionHeader", $data->semanticVersion);
        }

        if (!IS_RELEASE && null != $data)
        {
            $this->nightlyNotice = new MNightlyNotice();
            $this->nightlyInfo = new MNightlyInfo($data);
            $this->isNightly = true;
        }

        if (null == $data)
        {
            $this->failedNotice = new MFailedNotice();
        }

		// We don't want this to show up if the failed notice is already visible.
        if (!IS_RELEASE && !@$data->supportsDotGit && !isset($this->failedNotice))
        {
            $this->nonGitNotice = new MNonGitNotice();
        }

        $this->runtimeInfo = new MRuntimeInfo();

        $this->licenseInfoButton = new class($strings) extends MButton {
            public function __construct($strings)
            {
                $this->setText($strings->get("creditsButton"));
                $this->class[] = "rehike-version-credits-button";
            }
        };

        if (GH_ENABLED)
        {
            $this->githubButton = new class($strings) extends MButton {
                public object $commandMetadata;
                
                public function __construct($strings)
                {
                    $this->commandMetadata = (object)["webCommandMetadata" => (object)[]];

                    $this->setText($strings->get("githubLinkButton"));
                    $this->style = "DARK";
                    $this->icon = (object)["iconType" => "RH_GITHUB_ICON"];
                    $this->customAttributes = [ "target" => "_blank" ];
                    $this->commandMetadata->webCommandMetadata->url =
                        "//github.com/" . GH_REPO;
                }
            };
        }
    }
}