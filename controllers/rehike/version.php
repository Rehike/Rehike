<?php
namespace Rehike\Controller\Version;

use Rehike\YtApp;
use Rehike\i18n\i18n;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Controller\core\HitchhikerController;
use Rehike\Controller\core\NirvanaController;
use Rehike\Version\VersionController;
use Rehike\Version\RemoteGit;

use Rehike\Model\Rehike\Version\MVersionPage;
use Rehike\Version\VersionInfo;

use const Rehike\Constants\GH_ENABLED;
use const Rehike\Constants\GH_REPO;

class GetVersionController extends NirvanaController
{
    /**
     * Reference to Rehike\Version\VersionController::$versionInfo
     */
    public static VersionInfo $versionInfo;

    public string $template = "rehike/version/main";

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $yt->page = (object)self::bake();
        $this->setTitle(i18n::getRawString("rehike/version", "aboutRehike"));
    }

    public static function bake(): MVersionPage
    {
        self::$versionInfo = &VersionController::$versionInfo;

        $initStatus = VersionController::init();

        if (false == $initStatus)
        {
            return new MVersionPage(null);
        }

        // If remote git is expected, report it
        if (GH_ENABLED)
            self::$versionInfo->expectRemoteGit = true;

        // ...and attempt to use it
        if (
            @self::$versionInfo->branch && 
            ( $rg = RemoteGit::getInfo(self::$versionInfo->branch) )
        )
        {
            self::$versionInfo->remoteGit = $rg;

            // If the previous commit is reported, but not the current commit,
            // attempt to retrieve the current commit hash from git.
            if (@self::$versionInfo->previousHash && !@self::$versionInfo->currentHash)
            for ($i = 1, $l = count($rg); $i < $l; $i++)
            {
                $currentItem = &$rg[$i];
                $previousItem = &$rg[$i - 1];

                if (self::$versionInfo->previousHash == @$currentItem->sha)
                {
                    // Previous item must be the current hash
                    // so use its hash
                    self::$versionInfo->currentHash = $previousItem->sha;
                }
            }
        }

        self::$versionInfo->semanticVersion = VersionController::getVersion();

        // Return a version page model.
        return new MVersionPage(self::$versionInfo);
    }
}

// export
return new GetVersionController();