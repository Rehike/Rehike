<?php
namespace Rehike\Logging;

use Rehike\ErrorHandler\ErrorHandler;
use Rehike\ErrorHandler\ErrorPage\FatalErrorPage;
use Rehike\FileSystem;
use Rehike\Logging\Common\FormattedString;

use DateTime;

/**
 * Manages the creation of log files.
 * 
 * @author The Rehike Maintainers
 */
class LogFileManager
{
    public static function createErrorLogFile(FatalErrorPage $errorLog): void
    {
        $logFile = new LogFile;
        $logFile->setError($errorLog);

        self::writeLogFile($logFile);

        // We manually free memory for log files, as they have the possibility
        // to be outrageously large.
        unset($logFile);
    }

    public static function createExceptionLogFile(FormattedString $exceptionLog): void
    {
        $logFile = new LogFile;
        $logFile->setException($exceptionLog);
        
        self::writeLogFile($logFile);

        // We manually free memory for log files, as they have the possibility
        // to be outrageously large.
        unset($logFile);
    }

    public static function writeLogFile(LogFile $file): void
    {
        $data = $file->render();
        $fileName = self::getLogFileName();
        $timeStr = date("YmdHis");

        $path = "logs/{$timeStr}-{$fileName}.txt";

        FileSystem::writeFile($path, $data);
        ErrorHandler::setLogFileName($path);
    }

    public static function pruneLogFiles(): void
    {
        foreach (glob("logs/*.txt") as $filePath)
        {
            if (!preg_match("/(\d+)\-/", $filePath, $matches))
            {
                continue;
            }

            $timestamp = $matches[1];
            $fileTime = new DateTime($timestamp);
            $currentTime = new DateTime();

            if ($fileTime->diff($currentTime)->days > 5)
            {
                unlink($filePath);
            }
        }
    }

    private static function getLogFileName(): string
    {
        $url = parse_url($_SERVER["REQUEST_URI"]);
        $path = $url["path"];

        // Normalize the path name to be safely stored as a file name.
        // In this process, "/" characters are removed from the beginnings and ends, and all
        // ones in the middle of the string are replaced with underscores.
        $pageName = preg_replace("/[^\\w]/", "-", str_replace("/", "_", trim($path, "/")));

        return empty($pageName) ? "home" : $pageName;
    }
}