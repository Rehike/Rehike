<?php
namespace Rehike\Nepeta\Internal;

use Rehike\FileSystem;

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

    public static function init(): void
    {
        self::$availablePackages = self::enumeratePackages();

        self::loadAllPackages();
    }

    public static function getAvailablePackages(): array
    {
        return self::$availablePackages;
    }

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

    private static function loadAllPackages(): NepetaResult
    {
        $result = new NepetaResult(NepetaResult::SUCCESS);

        foreach (self::enumeratePackages() as $packageRequest)
        {
            $result->set(self::loadPackageByName($packageRequest));

            if ($result != NepetaResult::SUCCESS)
            {
                return $result;
            }
        }

        return $result;
    }

    private static function getPackagePath(string $package): string
    {
        return $_SERVER["DOCUMENT_ROOT"] . "/" . 
            NepetaCore::NEPETA_EXT_PATH . "/" . $package;
    }

    private static function loadPackageByName(string $packageName): NepetaResult
    {
        $result = new NepetaResult(NepetaResult::FAILED);

        $path = self::getPackagePath($packageName);
        $result->set(self::loadPackage($path));

        return $result;
    }

    /**
     * Loads information about a package.
     */
    public static function getPackageInfo(string $packageName): ?NepetaPackageInfo
    {
        return self::getPackageInfoByPath(self::getPackagePath($packageName));
    }

    private static function getPackageInfoByPath(string $packagePath): ?NepetaPackageInfo
    {
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

        return $info;
    }

    private static function loadPackage(string $packagePath): NepetaResult
    {
        $result = new NepetaResult(NepetaResult::FAILED);

        $info = self::getPackageInfoByPath($packagePath);

        if (null == $info)
        {
            return $result;
        }

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