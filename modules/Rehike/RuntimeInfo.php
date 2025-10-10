<?php
namespace Rehike;

/**
 * Gets information about the current runtime environment.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class RuntimeInfo
{
    public string $osDisplayName;
    public string $osBuildNumber;
    public string $architecture;
    public string $internalOsName;
    public string $phpVersion;
    public string $serverVersion;

    public function __construct()
    {
        $this->getOsInfo();
        $this->getPhpInfo();
    }

    /**
     * Gets information about the user's operating system.
     */
    private function getOsInfo(): void
    {
        $osName = php_uname("s");
        
        $this->internalOsName = $this->osDisplayName = $osName;
        $this->osBuildNumber = "<unknown>";
        $this->architecture = "<unknown>";

        if ("Windows NT" == $osName)
        {
            $this->getWindowsInfo();
        }
        else if ("Darwin" == $osName)
        {
            $this->getMacOsInfo();
        }
        else if ("Linux" == $osName)
        {
            $this->getLinuxInfo();
        }
    }

    /**
     * Gets platform information for Windows NT platforms.
     */
    private function getWindowsInfo(): void
    {
        $alternateOsName = "Windows";

        $buildString = php_uname("v");
        preg_match("/build (\d+) \((.+)\)/", $buildString, $matches);
        $buildNumber = $matches[1];
        
        if (is_int($buildNumber))
        {
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

        $architecture = php_uname("m");
        if ("AMD64" == $architecture)
        {
            $architecture = "x64";
        }

        $this->osDisplayName = $osName;
        $this->internalOsName = "Windows NT";
        $this->osBuildNumber = $buildNumber;
        $this->architecture = $architecture;
    }

    /**
     * Gets platform information for Mac OS X platforms.
     */
    private function getMacOsInfo(): void
    {
        // We need to determine the version of macOS being used. If we fail,
        // then we will report the XNU kernel version instead.
        $osName = "Mac OS X";
        $version = php_uname("r");

        $aaaaaaaaaadonterror = shell_exec("sw_vers -productVersion");
        $swVers = trim($aaaaaaaaaadonterror ?? "");

        $this->osDisplayName = $osName;
        $this->internalOsName = "Darwin";
        $this->osBuildNumber = $swVers ?? $version;
        $this->architecture = php_uname("m");
    }

    /**
     * Gets platform information for Linux platforms.
     * 
     * I fucking hate Linux I'm sorry why is it such a mess.
     */
    private function getLinuxInfo(): void
    {
        $this->osDisplayName = "Linux";
        $this->internalOsName = "Linux";
        $this->osBuildNumber = php_uname("r");
        $this->architecture = php_uname("m");
    }

    /**
     * Gets information about the user's current PHP environment.
     */
    private function getPhpInfo(): void
    {
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

        $this->phpVersion = phpversion();
        $this->serverVersion = $serverVersion;
    }
}