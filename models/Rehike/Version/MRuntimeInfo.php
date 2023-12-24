<?php
namespace Rehike\Model\Rehike\Version;

use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;
use YukisCoffee\CoffeeTranslation\Lang\NamespaceBoundLanguageApi;

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

        $buildString = php_uname("v");
        preg_match("/build (\d+) \((.+)\)/", $buildString, $matches);
        $buildNumber = $matches[1];

        //$buildNumber = 22000;
        
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

            // Name fallbacks:
            if ($buildNumber >= 2195)
                $alternateOsName = "Windows 2000";
            if ($buildNumber >= 2600)
                $alternateOsName = "Windows XP";
            if ($buildNumber >= 3790)
                $alternateOsName = "Windows Server 2003";
            if ($buildNumber >= 6000)
                $alternateOsName = "Windows Vista";
            if ($buildNumber >= 7600)
                $alternateOsName = "Windows 7";
            if ($buildNumber >= 9200)
                $alternateOsName = "Windows 8";
            if ($buildNumber >= 9600)
                $alternateOsName = "Windows 8.1";
            if ($buildNumber >= 10240)
                $alternateOsName = "Windows 10";
            if ($buildNumber >= 22000)
                $alternateOsName = "Windows 11";
        }

        $osName = $matches[2] ?? $alternateOsName;
        //$osName = $alternateOsName;

        $architecture = php_uname("m");
        if ("AMD64" == $architecture)
        {
            $architecture = "x64";
        }

        return (object)[
            "title" => "< to be assigned later >",
            "prominentOsName" => $osName,
            "logo" => $logo,
            "info" => [
                "build $buildNumber",
                $architecture
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

        $fullServerVersion = $_SERVER["SERVER_SOFTWARE"];
        $serverVersion = "";

        // PHP development server may violate RFC-3875 § 4.1.17, and we probably
        // don't want it displaying weirdly anyways.
        if (preg_match("/Development Server/", $fullServerVersion))
        {
            $serverVersion = "Built-in dev server";
        }
        else
        {
            // RFC-3875 § 4.1.17 specifies that basically the following pattern
            // must be used for server names:
            //
            //    - ServerName/1.0 (Additional Information)
            //
            // Hence, it's safe to use the following code to retrieve the server
            // name and version for compliant web servers, which comprises most
            // PHP servers available.
            $serverVersion = explode(" ", $_SERVER["SERVER_SOFTWARE"])[0];
        }

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
            "serverVersion" => $serverVersion,
            "seeMoreButton" => $seeMoreButton
        ];
    }
}