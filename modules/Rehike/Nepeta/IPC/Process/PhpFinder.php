<?php
namespace Rehike\Nepeta\IPC\Process;

/**
 * Find the location of the PHP executable.
 * 
 * Substantially adopted from code from Symfony, so I included their license.
 * https://github.com/symfony/process/blob/7.0/PhpExecutableFinder.php
 * 
 * I simply made it a little more portable by cutting out an extra depenency,
 * and reformatted it to match the rest of Rehike's code.
 * 
 * @license
 *  Copyright (c) 2004-present Fabien Potencier
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class PhpFinder
{
    public static function findPhpBinary(): ?string
    {
        $isWindows = "\\" == DIRECTORY_SEPARATOR;

        if ($php = getenv("PHP_BINARY"))
        {
            if (!is_executable($php))
            {
                $command = $isWindows
                    ? "where"
                    : "command -v --";

                if (
                    \function_exists("exec") && 
                    $php == strtok(exec($command . " " . escapeshellarg($php)), \PHP_EOL)
                )
                {
                    if (!is_executable($php))
                    {
                        return null;
                    }
                }
                else
                {
                    return null;
                }
            }

            if (@is_dir($php))
            {
                return null;
            }

            return $php;
        }

        if ($php = getenv("PHP_PATH"))
        {
            if (!@is_executable($php) || @is_dir($php))
            {
                return null;
            }

            return $php;
        }

        if (
            @is_executable($php = \PHP_BINDIR . ( "\\" == $isWindows ? "\\php.exe" : "/php" )) &&
            !@is_dir($php)
        )
        {
            return $php;
        }

        $dirs = [\PHP_BINDIR];

        if ($isWindows)
        {
            $dirs[] = "C:\\xampp\\php\\";
        }

        foreach ($dirs as $dir)
        {
            if (file_exists($dir . ($isWindows ? "\\php.exe" : "/php")))
            {
                return $dir . ($isWindows ? "\\php.exe" : "/php");
            }
        }

        return null;
    }
}