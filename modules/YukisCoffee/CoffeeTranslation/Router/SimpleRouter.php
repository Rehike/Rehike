<?php
namespace YukisCoffee\CoffeeTranslation\Router;

use YukisCoffee\CoffeeTranslation\CoffeeTranslation;

use YukisCoffee\CoffeeTranslation\Lang\{
    SourceInfo,
    Record\LanguageRecord,
    Parser\RecordFileParser
};

use YukisCoffee\CoffeeTranslation\Router\SimpleRouter\Cacher;

/**
 * A simple, file-system-based router for translation files.
 * 
 * In this, a translation URI is simply its file-system location. A root folder
 * is provided, whose subdirectories are named language identifiers (i.e. en-US),
 * which then may contain additional files and subdirectories for namespaces.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class SimpleRouter implements IRouter
{
    public function resolveLocation(string $langId, string $uri): ResourceInfo
    {
        return $this->internalResolveLocation($langId, $uri);
    }

    public function resolveLocationAsEncoding(
            string $langId,
            string $uri,
            string $encoding
    ): ResourceInfo
    {
        return $this->internalResolveLocation($langId, $uri, $encoding);
    }

    public function languageExists(string $langId): bool
    {
        return file_exists($this->getRootDir() . "/$langId");
    }

    public function locationExists(string $langId, string $uri): bool
    {
        return $this->tryGetEffectivePath($langId, $uri, $ignored);
    }

    // TODO
    public function getCulture()
    {

    }

    protected function internalResolveLocation(
            string $langId,
            string $uri,
            string $encoding = ""
    ): ResourceInfo
    {
        $resourceInfo = new ResourceInfo;

        if ($this->tryGetEffectivePath($langId, $uri, $effectivePath))
        {
            $result = $this->getLanguageRecord($effectivePath, $encoding);
            $resourceInfo->record = $result;
            $resourceInfo->resourceExists = true;
        }
        else
        {
            $resourceInfo->record = null;
            $resourceInfo->resourceExists = false;
        }

        return $resourceInfo;
    }

    protected function getRootDir(): string
    {
        return CoffeeTranslation::getConfigApi()
            ->getRootDirectory();
    }

    protected function getDefaultExtension(): string
    {
        return CoffeeTranslation::getConfigApi()
            ->getDefaultFileExtension();
    }

    protected function getLanguageRecord(string $path, string $encoding): LanguageRecord
    {
        // if (Cacher::has($path))
        // {
        //     return Cacher::get($path);
        // }

        $contents = file_get_contents($path);

        if (empty($encoding))
        {
            if (true == CoffeeTranslation::getIsMbStringSupported())
            {
                $encoding = mb_detect_encoding($contents, [
                    "UTF-16",
                    "UTF-8"
                ]);
            }
            else
            {
                $encoding = "UTF-8";
            }
        }

        $source = new SourceInfo($path, $encoding, $contents);

        $result = RecordFileParser::parse($source);
        //Cacher::insert($path, $result);

        return $result;
    }

    protected function tryGetEffectivePath(
            string $langId,
            string $uri,
            ?string &$out
    ): bool
    {
        $rootDir = $this->getRootDir();
        $defaultExt = $this->getDefaultExtension();

        $fileTryList = [ $uri ];

        if (!empty($defaultExt))
        {
            $bits = explode("/", $uri);

            if (strpos($bits[count($bits) - 1], ".") === false)
            {
                $bits[count($bits) - 1] .= "." . $defaultExt;
                $fileTryList[] = implode("/", $bits);
            }
        }

        foreach ($fileTryList as $filename)
        {
            $path = "$rootDir/$langId/$filename";

            if (file_exists($path))
            {
                $out = $path;
                return true;
            }
        }

        $out = null;
        return false;
    }
}