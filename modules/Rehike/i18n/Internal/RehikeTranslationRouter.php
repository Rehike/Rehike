<?php
namespace Rehike\i18n\Internal;

use YukisCoffee\CoffeeTranslation\Router\SimpleRouter;
use YukisCoffee\CoffeeTranslation\Router\IRouter;
use YukisCoffee\CoffeeTranslation\Lang\Record\LanguageRecord;
use YukisCoffee\CoffeeTranslation\Lang\Record\RecordEntries;
use YukisCoffee\CoffeeTranslation\Attributes\Override;

use Rehike\FileSystem;

use ReflectionObject;

/**
 * Rehike-specific translation router.
 * 
 * This custom router provides support for caching to disk.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class RehikeTranslationRouter extends SimpleRouter implements IRouter
{
    /**
     * The folder path to store cached language files in.
     */
    private const CACHE_FOLDER = "cache/i18n";
    
    #[Override]
    protected function getLanguageRecord(string $path, string $encoding): LanguageRecord
    {
        $fileModifiedTime = filemtime($path);
        $latestCachePath = $this->getCacheFolderPath() . "/" . $this->getCacheFileName($path, $fileModifiedTime);
        
        if ($fileModifiedTime && FileSystem::fileExists($latestCachePath))
        {
            try
            {
                $cacheObj = @(include $latestCachePath);
                
                if ($cacheObj)
                {
                    return $cacheObj;
                }
            }
            catch (\Throwable $e)
            {
                throw $e;
                \Rehike\Logging\DebugLogger::print(
                    "Failed to read i18n file cache for paths: \"$path\" \"$latestCachePath\""
                );
            }
        }
        
        $this->deleteAllDeadCachesForFile($path);
        $result = parent::getLanguageRecord($path, $encoding);
        $this->writeCache($path, $result);
        
        return $result;
    }
    
    /**
     * Gets the hashed folder path for the cached file name.
     * 
     * This is done to avoid conflicts when the user changes the language.
     */
    private function getCacheNameHash(string $path): string
    {
        return hash(\PHP_VERSION_ID < 80100 ? 'sha256' : 'xxh128', FileSystem::getRehikeRelativePath($path));
    }
    
    /**
     * Gets the cached file name (base name + extension).
     * 
     * This string is formed like such:
     * 
     * "<basename>-<hashed path>-<last modified timestamp>.php"
     */
    private function getCacheFileName(string $path, int $timestamp): string
    {
        $baseName = basename($path, "." . FileSystem::getExtension($path));
        $hash = $this->getCacheNameHash($path);
        return "$baseName-$hash-$timestamp.php";
    }
    
    /**
     * Writes a language cache file.
     */
    private function writeCache(string $path, LanguageRecord $record): void
    {
        $timestamp = filemtime($path);
        $cacheContents = "<" . "?php return " . var_export($record, true) . ";";
        $fileName = $this->getCacheFileName($path, $timestamp);
        $filePath = $this->getCacheFolderPath() . "/$fileName";
        
        FileSystem::writeFile($filePath, $cacheContents);
    }
    
    /**
     * Deletes all dead cache files for the file.
     */
    private function deleteAllDeadCachesForFile(string $path): void
    {
        $hash = $this->getCacheNameHash($path);
        
        $baseName = basename($path, FileSystem::getExtension($path));
        
        foreach (glob(self::getCacheFolderPath() . "/$baseName-$hash-*.php") as $file)
        {
            unlink($file);
        }
    }

    /**
     * Gets the absolute cache folder path.
     */
    private function getCacheFolderPath(): string
    {
        return $_SERVER["DOCUMENT_ROOT"] . "/" . self::CACHE_FOLDER;
    }
}