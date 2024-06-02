<?php
namespace Rehike\Nepeta\IPC\Process;

/**
 * Manages the creation of additional PHP processes.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class PhpProcessManager
{
    public static ?string $phpBinPath = null;

    public static function __initStatic(): void
    {
        self::$phpBinPath = PhpFinder::findPhpBinary();
    }

    public static function createProcess(string $script, string $args = ""): void
    {
        $php = self::$phpBinPath;
        //$args = escapeshellarg($args);

        pclose(
            popen("start /B $php $script $args 1> NUL 2>&1 & ", "r")
        );
    }
}