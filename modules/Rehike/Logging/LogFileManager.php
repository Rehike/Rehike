<?php
namespace Rehike\Logging;

use Rehike\ErrorHandler\ErrorHandler;
use Rehike\ErrorHandler\ErrorPage\FatalErrorPage;
use Rehike\FileSystem;
use Rehike\Logging\Common\FormattedString;

/**
 * Manages the creation of log files.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
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

    private static function getLogFileName(): string
    {
        $pageName =  explode("?", explode("/", $_SERVER["REQUEST_URI"])[0])[0];

        return empty($pageName) ? "home" : $pageName;
    }
}