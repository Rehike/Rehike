<?php
namespace Rehike\Nepeta;

use Rehike\ConfigManager\Config;
use Rehike\FileSystem;

/**
 * Provides core services for Nepeta.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class NepetaCore
{
    public const NEPETA_EXT_PATH = "nepeta_test";

    /**
     * Stores all loaded packages.
     */
    private static array $packages = [];

    private static ?NepetaTheme $currentTheme = null;

    /**
     * Performs early startup services.
     */
    public static function init(): void
    {
        self::loadAllPackages();
    }

    /**
     * Checks if Nepeta is enabled.
     */
    public static function isNepetaEnabled(): bool
    {
        return true == Config::getConfigProp("experiments.enableNepeta");
    }

    /**
     * Get the current theme set by the user.
     */
    public static function getTheme(): ?NepetaTheme
    {
        return self::$currentTheme;
    }

    private static function enumeratePackages(): array
    {
        $scan = scandir($_SERVER["DOCUMENT_ROOT"] . "/" . self::NEPETA_EXT_PATH);

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
        return $_SERVER["DOCUMENT_ROOT"] . "/" . self::NEPETA_EXT_PATH . "/" . $package;
    }

    private static function loadPackageByName(string $packageName): NepetaResult
    {
        $result = new NepetaResult(NepetaResult::FAILED);

        $path = self::getPackagePath($packageName);
        $result->set(self::loadPackage($path));

        return $result;
    }

    private static function loadPackage(string $packagePath): NepetaResult
    {
        $result = new NepetaResult(NepetaResult::FAILED);

        $manifestPath = $packagePath . "/manifest.json";
        if (!file_exists($manifestPath))
        {
            return $result;
        }

        $manifest = json_decode(FileSystem::getFileContents($manifestPath));
        if (!$manifest)
        {
            return $result;
        }

        $info = new NepetaPackageInfo;
        $info->id = $manifest->id;
        $info->name = $manifest->name;
        $info->author = $manifest->author;
        $info->insertionPoint = $manifest->insertion_point;
        $info->templates = ((array)$manifest->templates) ?? null;
        $info->type = NepetaPackageType::fromString($manifest->extension_type);
        $info->pathOnDisk = $packagePath;

        self::$packages[$info->id] = $info;

        if (NepetaPackageType::TYPE_THEME == $info->type && null != $info->templates)
        {
            self::$currentTheme = new NepetaTheme(
                $info,
                $info->templates
            );
        }

        $result->set(NepetaResult::SUCCESS);

        return $result;
    }
}