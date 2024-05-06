<?php
namespace Rehike\Nepeta;

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
     * An insertion point script to be loaded.
     */
    public ?string $insertionPoint = null;
}