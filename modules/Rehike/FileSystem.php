<?php
namespace Rehike;

use \Rehike\Exception\FileSystem\FsFileDoesNotExistException;
use \Rehike\Exception\FileSystem\FsMkdirException;
use \Rehike\Exception\FileSystem\FsWriteFileException;
use \Rehike\Exception\FileSystem\FsFileReadFailureException;

/**
 * Implements common file system helpers.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class FileSystem
{
    /**
     * Get the extension of a filename.
     * 
     * This returns everything past the first dot in the filename, so
     * formats like .tar.gz are supported.
     */
    public static function getExtension(string $filename): string
    {
        // Ignore the first . (i.e. in ./)
        if ("." == $filename[0])
        {
            $filename = substr($filename, 1);
        }

        // Split the filename by "."
        $ext = explode(".", $filename);

        // Remove the first item (everything before the first .)
        array_splice($ext, 0, 1);

        // Rejoin the extension by "."
        $ext = implode(".", $ext);

        return $ext;
    }

    /**
     * Get the containing folder of a file path.
     */
    public static function getFolder(string $path): string
    {
        // Convert the path to account for Windows separation.
        $path = self::unwindows($path);

        // Split the path by the separator
        $root = explode("/", $path);

        // Remove the last item (the filename)
        array_splice($root, count($root) - 1, 1);

        // Rejoin
        $root = implode("/", $root);

        return $root;
    }

    /**
     * Un-Windows a path (convert \ to /)
     */
    public static function unwindows(string $path): string
    {
        return str_replace("\\", "/", $path);
    }

    /**
     * Get a "rehike://" URL which anonymises the path the user stored their
     * Rehike installation. This is useful for logging purposes.
     */
    public static function getRehikeRelativePath(string $path): string
    {
        $path = self::unwindows($path);
        return str_replace($_SERVER["DOCUMENT_ROOT"] . "/", "rehike://", $path);
    }

    /**
     * Write a file.
     * 
     * @param $path of the file to write to.
     * @param $contents to write.
     * @param $recursive (create folders leading to the filename if they don't exist)
     * @param $append to the file instead of erasing it
     * @return void
     */
    public static function writeFile(
            string $path, 
            string $contents, 
            bool $recursive = true, 
            bool $append = false
    ): void
    {
        // Make sure all folders leading to the path exist if the
        // recursive option is enabled.
        if ($recursive)
        {
            $folder = self::getFolder($path);

            if (!is_dir($folder))
            {
                self::mkdir($folder, 0777, true);
            }
        }

        // Determine fopen mode from append value
        $fopenMode = $append ? "a" : "w";

        // Use fopen to write the file
        $fh = @\fopen($path, $fopenMode);
        
        if ($fh === false)
        {
            // Issue #598: The lack of error checking at this point caused the
            // PHP interpreter itself to throw a TypeError when the next call
            // happened with a null file handle.
            throw new FsWriteFileException("Failed to open file for writing.");
        }
        
        $status = @\fwrite($fh, $contents);

        // Validate
        if (false == $fh || false == $status)
        {
            throw new FsWriteFileException("Failed to write file \"$path\"");
        }

        \fclose($fh);
    }

    //
    // Wrapper operations for standard library functions
    //

    public static function getFileContents(
            string $filename, 
            bool $useIncludePath = true, 
            /* resource */ $context = null, 
            ?int $offset = null, 
            ?int $length = null
    ): string
    {
        $status = @\file_get_contents(
            $filename,
            $useIncludePath,
            $context
        );

        if (false == $status)
        {
            if (\file_exists($filename))
            {
                throw new FsFileReadFailureException(
                    "Failed to read file \"$filename\". Double check if PHP has " .
                    "permission to access the file and try again."
                );
            }
            else
            {
                throw new FsFileDoesNotExistException(
                    "Attempted to read nonexistent file \"$filename\""
                );
            }
        }
        else
        {
            return $status;
        }
    }

    public static function fileExists(string $filename): bool
    {
        return \file_exists($filename) ||
               \file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . $filename);
    }

    public static function mkdir(
            string $dirname, 
            int $mode = 0777, 
            bool $recursive = false
    ): void
    {
        $status = @\mkdir($dirname, $mode, $recursive);

        if (false == $status)
        {
            throw new FsMkdirException(
                "Failed to create directory \"$dirname\""
            );
        }
    }
}