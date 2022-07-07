<?php
namespace Rehike;

use \Rehike\Exception\FileSystem\FsFileDoesNotExistException;
use \Rehike\Exception\FileSystem\FsMkdirException;
use \Rehike\Exception\FileSystem\FsWriteFileException;

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
     * 
     * @param string $filename
     * @return string
     */
    public static function getExtension($filename)
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
     * 
     * @param string $path
     * @return string
     */
    public static function getFolder($path)
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
     * 
     * @param string $path
     * @return string
     */
    public static function unwindows($path)
    {
        return str_replace("\\", "/", $path);
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
    public static function writeFile($path, $contents, $recursive = true, $append = false)
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
        
        $status = @\fwrite($fh, $contents);

        // Validate
        if (false == $fh || false == $status)
        {
            throw new FsWriteFileException("Failed to write file \"$path\"");
        }

        \fclose($fh);
    }

    //
    // Alias operations
    //
    public static function getFileContents($filename, $useIncludePath = false, $context = null, $offset = 0, $length = null)
    {
        $status = @\file_get_contents($filename, $useIncludePath, $context, $offset, $length);

        if (false == $status)
        {
            throw new FsFileDoesNotExistException(
                "Attempted to read nonexistent file \"$filename\""
            );
        }
        else
        {
            return $status;
        }
    }

    public static function fileExists($filename)
    {
        return \file_exists($filename);
    }

    public static function mkdir($dirname, $mode = 0777, $recursive = false)
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