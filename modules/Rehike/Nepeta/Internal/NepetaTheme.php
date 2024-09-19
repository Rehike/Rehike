<?php
namespace Rehike\Nepeta\Internal;

/**
 * API for Nepeta themes.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class NepetaTheme
{
    private array $templates = [];
    private NepetaPackageInfo $ownerPackage;

    public function __construct(NepetaPackageInfo $package, array $templates)
    {
        $this->ownerPackage = $package;
        $packagePath = $package->pathOnDisk;

        foreach ($templates as $namespace => $path)
        {
            if ("/" != $path[0])
            {
                $this->templates[$namespace] = $packagePath . "/" . $path;
            }
            else
            {
                $this->templates[$namespace] = $path;
            }
        }
    }

    public function getOverrideTemplatesPaths(): array
    {
        $out = [];

        foreach ($this->templates as $namespace => $path)
        {
            if (0 === strpos($namespace, "override:"))
            {
                $out[$namespace] = $path;
            }
        }

        return $out;
    }

    public function getAddedTemplatePaths(): array
    {
        $out = [];

        foreach ($this->templates as $namespace => $path)
        {
            if ("override:" != substr($namespace, strlen("override:")))
            {
                $out[$namespace] = $path;
            }
        }

        return $out;
    }

    public function getAllTemplatePaths(): array
    {
        return $this->templates;
    }
}