<?php
namespace Rehike\Nepeta\Internal;

use Rehike\Nepeta\Internal\NepetaFileSystemManager as FileSystem;

/**
 * Implements the Nepeta extension package manager.
 * 
 * Nepeta packages are folders with manifest.json files, similarly to Chromium
 * extensions.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class PackageManager
{
    /**
     * Stores all available packages.
     */
    private static array $availablePackages = [];

    /**
     * Stores all loaded packages.
     */
    private static array $packages = [];

    /**
     * Used to check if we are currently initializing.
     */
    private static bool $isInitializing = false;

    /**
     * Caches package information in memory while Nepeta loads.
     */
    private static array $packageInfoCache = [];

    public static function init(): void
    {
        self::$isInitializing = true;
        
        self::$availablePackages = self::enumeratePackages();

        self::loadAllPackages();

        self::finishInitialization();
    }

    public static function finishInitialization(): void
    {
        self::$isInitializing = false;

        // We can now clear the package information cache, since we won't be
        // looking at them every five seconds.
        self::$packageInfoCache = [];
    }

    /**
     * PUBLIC API : Get all available packages.
     */
    public static function getAvailablePackages(): array
    {
        return self::$availablePackages;
    }

    /**
     * Enumerates all packages in the installation folder.
     * 
     * @see NepetaCore::NEPETA_EXT_PATH  Installation path constant.
     */
    private static function enumeratePackages(): array
    {
        $scan = scandir(
            $_SERVER["DOCUMENT_ROOT"] . "/" . NepetaCore::NEPETA_EXT_PATH
        );

        if ($scan)
        {
            // Remove "." and ".." from the output:
            return array_diff($scan, [".", ".."]);
        }

        // If there are no packages, then return an empty array:
        return [];
    }

    /**
     * Loads all packages found on the disk.
     */
    private static function loadAllPackages(): NepetaResult
    {
        $result = new NepetaResult(NepetaResult::SUCCESS);

        foreach (self::enumeratePackages() as $packageRequest)
        {
            $packageInfo = self::getPackageInfo($packageRequest);

            if (self::shouldLoadPackage($packageInfo->id))
            {
                $result->set(self::loadPackage($packageInfo));

                if ($result != NepetaResult::SUCCESS)
                {
                    return $result;
                }
            }
        }

        return $result;
    }

    /**
     * Gets the path of a package on disk.
     */
    private static function getPackagePath(string $package): string
    {
        return $_SERVER["DOCUMENT_ROOT"] . "/" . 
            NepetaCore::NEPETA_EXT_PATH . "/" . $package;
    }

    /**
     * Determines if a package has been seen before.
     */
    private static function isKnownPackage(string $packageId): bool
    {
        return null !== LightweightConfigManager::getProp(
            "nepetaSettings.$packageId"
        );
    }

    /**
     * Determine if we should load a package.
     * 
     * Unknown packages are loaded by default. The only time an installed
     * package will not be loaded is if it is manually disabled in the user
     * config.
     */
    private static function shouldLoadPackage(string $packageId): bool
    {
        return !self::isKnownPackage($packageId) ||
            false !== LightweightConfigManager::getProp(
                "nepetaSettings.$packageId.enabled"
            );
    }

    /**
     * PUBLIC API : Loads information about a package from disk.
     */
    public static function getPackageInfo(string $packageName): ?NepetaPackageInfo
    {
        return self::getPackageInfoByPath(self::getPackagePath($packageName));
    }

    /**
     * Loads information about a package from the manifest file on disk.
     */
    private static function getPackageInfoByPath(string $packagePath): ?NepetaPackageInfo
    {
        if (isset(self::$packageInfoCache[$packagePath]))
        {
            return self::$packageInfoCache[$packagePath];
        }

        $manifestPath = $packagePath . "/manifest.json";
        if (!file_exists($manifestPath))
        {
            return null;
        }

        $manifest = json_decode(FileSystem::getFileContents($manifestPath));
        if (!$manifest)
        {
            return null;
        }

        $info = new NepetaPackageInfo;
        $info->id = $manifest->id;
        $info->name = $manifest->name;
        $info->author = $manifest->author;
        $info->insertionPoint = $manifest->insertion_point;
        $info->templates = ((array)$manifest->templates) ?? null;
        $info->type = NepetaPackageType::fromString($manifest->extension_type);
        $info->pathOnDisk = $packagePath;

        if (self::$isInitializing)
        {
            self::$packageInfoCache[$packagePath] = $info;
        }

        return $info;
    }

    /**
     * Loads a package into the session.
     */
    private static function loadPackage(NepetaPackageInfo $info): NepetaResult
    {
        $result = new NepetaResult(NepetaResult::FAILED);

        // Insert the package into the loaded packages registry:
        self::$packages[$info->id] = $info;

        if (NepetaPackageType::TYPE_THEME == $info->type && null != $info->templates)
        {
            NepetaCore::setTheme(new NepetaTheme(
                $info,
                $info->templates
            ));
        }

        $result->set(NepetaResult::SUCCESS);

        return $result;
    }
}