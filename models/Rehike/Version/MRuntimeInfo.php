<?php
namespace Rehike\Model\Rehike\Version;

use Rehike\RuntimeInfo;
use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;
use Rehike\i18n\Internal\Lang\NamespaceBoundLanguageApi;

/**
 * Gets information about the user's runtime environment, namely the operating
 * system and PHP version.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MRuntimeInfo
{
    public string $headingText;
    public object $osInfo;
    public object $phpInfo;
    public object $browserInfo;

    public function __construct()
    {
        $strings = i18n::getNamespace("rehike/version");

        $this->headingText = $strings->get("extraInfo");

        $this->osInfo = $this->getOsInfo();

        $this->osInfo->title = $strings->get("operatingSystem");

        $this->phpInfo = $this->getPhpInfo();

        $this->browserInfo = (object)[
            "title" => $strings->get("browserVersion")
        ];
    }

    /**
     * Gets information about the user's operating system.
     */
    private function getOsInfo(): object
    {
        $osName = php_uname("s");

        if ("Windows NT" == $osName)
        {
            return $this->getWindowsInfo();
        }
        else if ("Darwin" == $osName)
        {
            return $this->getMacOsInfo();
        }
        else if ("Linux" == $osName)
        {
            return $this->getLinuxInfo();
        }
        
        return (object)[
            "prominentOsName" => "Unknown"
        ];
    }

    /**
     * Gets platform information for Windows NT platforms.
     * 
     * TODO: Windows 9x support :P
     */
    private function getWindowsInfo(): object
    {
        $logo = "win8";
        $alternateOsName = "Windows";

        $baseInfo = new RuntimeInfo;
        $buildNumber = $baseInfo->osBuildNumber;
        
        if (is_int($buildNumber))
        {
            // Logo selection:
            if ($buildNumber < 9200)
            {
                $logo = "winxp";
            }
            else if ($buildNumber >= 22000)
            {
                $logo = "win11";
            }
        }

        return (object)[
            "title" => "< to be assigned later >",
            "prominentOsName" => $baseInfo->osDisplayName,
            "logo" => $logo,
            "info" => [
                "build $buildNumber",
                $baseInfo->architecture
            ]
        ];
    }

    /**
     * Gets platform information for Mac OS X platforms.
     * 
     * Mac OS 9 support???
     */
    private function getMacOsInfo(): object
    {
        // We need to determine the version of macOS being used. If we fail,
        // then we will report the XNU kernel version instead.
        $osName = "Darwin";
        $version = php_uname("r");
        $logo = "apple";

        $aaaaaaaaaadonterror = shell_exec("sw_vers -productVersion");
        $swVers = trim($aaaaaaaaaadonterror ?? "");
        $swParts = explode(".", $swVers);
        $swMajor = $swParts[0] ?? null;
        $swMinor = $swParts[1] ?? null;
        $swPatch = $swParts[2] ?? null;

        if ($swVers)
        {
            $osName = "macOS";
            $version = $swVers;

            $logo = "macos";

            if ($swMajor == 10)
            {
                if ($swMinor <= 11)
                {
                    $osName = "OS X";
                    $logo = "apple";
                }
                if ($swMinor <= 6)
                {
                    $osName = "Mac OS X";
                    $logo = "apple";
                }
            }
        }

        return (object)[
            "title" => "< to be assigned later >",
            "prominentOsName" => $osName,
            "logo" => $logo,
            "info" => [
                $version,
                php_uname("m") // architecture
            ]
        ];
    }

    /**
     * Gets platform information for Linux platforms.
     * 
     * I fucking hate Linux I'm sorry why is it such a mess.
     */
    private function getLinuxInfo(): object
    {
        return (object)[
            "title" => "< to be assigned later >",
            "prominentOsName" => "Linux",
            "logo" => "linux",
            "info" => [
                php_uname("r"), // version
                php_uname("m")  // architecture
            ]
        ];
    }

    /**
     * Gets information about the user's current PHP environment.
     */
    private function getPhpInfo(): object
    {
        $strings = i18n::getNamespace("rehike/version");

        $baseInfo = new RuntimeInfo;

        $seeMoreButton = new class($strings) extends MButton
        {
            public object $navigationEndpoint;
            
            public function __construct(NamespaceBoundLanguageApi $strings)
            {
                $this->setText($strings->get("runtimeInfoMoreInfoButton"));
                $this->size = "small";

                // The original idea that I had was to make this open a dialog,
                // but I thought that it'd be nicer to do it this way. phpinfo()
                // already reports all the necessary information and more, and
                // it's built into the engine.
                $this->navigationEndpoint = (object)[
                    "commandMetadata" => (object)[
                        "webCommandMetadata" => (object)[
                            "url" => "/rehike/server_info"
                        ]
                    ]
                ];
                $this->customAttributes = [ "target" => "_blank" ];
                
                // We don't want the link to trigger SPF navigation, since that
                // is incompatible with target="_blank".
                $this->class[] = "spf-nolink";
            }
        };

        return (object)[
            "title" => $strings->get("phpVersion"),
            "version" => phpversion(),
            "serverVersion" => $baseInfo->serverVersion,
            "seeMoreButton" => $seeMoreButton
        ];
    }
}