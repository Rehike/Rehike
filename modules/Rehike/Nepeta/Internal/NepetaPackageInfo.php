<?php
namespace Rehike\Nepeta\Internal;

/**
 * Used for storing a package manifest.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class NepetaPackageInfo
{
    /**
     * A unique ID used to refer to the package.
     */
    public string $id;

    /**
     * The path of the package on disk.
     */
    public string $pathOnDisk;
    
    /**
     * The display name of the package.
     */
    public string $name;
    
    /**
     * The author of the package.
     */
    public string $author;

    /**
     * The type of the package.
     */
    public int $type;

    /**
     * An optional path to a directory storing template files.
     */
    public ?array $templates = null;

    /**
     * An insertion point script to be loaded.
     */
    public ?string $insertionPoint = null;
}