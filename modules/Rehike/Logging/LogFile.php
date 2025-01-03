<?php
namespace Rehike\Logging;

use Rehike\Version\VersionController;
use Rehike\RuntimeInfo;
use Rehike\ConfigManager\Config;
use Rehike\ControllerV2\Router;
use Rehike\Logging\Common\FormattedString;
use Rehike\SignInV2\SignIn;
use Rehike\Network;
use Rehike\ErrorHandler\ErrorPage\FatalErrorPage;

use Rehike\Async\Promise;
use Rehike\Async\Promise\PromiseStatus;
use Rehike\Network\IResponse;

use const Rehike\Constants\GH_ENABLED;

/**
 * Puts together log files.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class LogFile
{
    private bool $hasException = false;
    private bool $hasError = false;
    private FormattedString $exceptionLog;
    private FatalErrorPage $errorLog;

    public function setException(FormattedString $exceptionLog): void
    {
        $this->hasException = true;
        $this->exceptionLog = $exceptionLog;
    }

    public function setError(FatalErrorPage $errorLog): void
    {
        $this->hasError = true;
        $this->errorLog = $errorLog;
    }

    public function render(): string
    {
        $out = "|\\> Rehike log file!!!\n\n";

        $out .= "<MESSAGE_NETWORK_PRIVACY_PLACEHOLDER>";

        $out .= "=== Request/session information ===\n";
        $out .= " - Page URL: " . $_SERVER["REQUEST_URI"] . "\n";
        $out .= " - Logged in: " . (SignIn::isSignedIn() ? "Yes" : "No") . "\n";
        $out .= " - Successful requests: " . $this->getSuccessfulRequests() . "\n";
        $out .= " - Router destination: " . $this->getRouterInfo() . "\n";

        $out .= "\n\n\n";

        $out .= "=== Configuration information ===\n";
        $out .= " - Rehike version: " . VersionController::getVersion() . "\n";
        $out .= " - Nightly release: " . (VersionController::$versionInfo->isRelease
            ? "No"
            : "Yes") . "\n";
        $out .= " - Git-cloned copy: " . (VersionController::$versionInfo->supportsDotGit
            ? "Yes"
            : "No") . "\n";
        $out .= " - Operating system: " . $this->getOperatingSystem() . "\n";
        $out .= " - PHP version: " . phpversion() . "\n";
        $out .= " - Server software: " . $this->getServerSoftware() . "\n";
        
        if (Config::isInitialized())
        {
            $configLog = $this->indentDumpedObject(
                json_encode(Config::getConfig(), JSON_PRETTY_PRINT)
            );
        }
        else
        {
            $configLog = "(unavailable)";
        }
        
        $out .= " - User configuration: " . $configLog . "\n";

        $out .= "\n\n\n";

        if ($this->hasException)
        {
            $out .= "=== Exception information ===\n";
            $out .= $this->indentFullString($this->exceptionLog->getRawText());

            $out .= "\n\n\n";
        }
        else if ($this->hasError)
        {
            $out .= "=== Error information ===\n";
            $out .= " - Error type: " . $this->errorLog->getType() . "\n";
            $out .= " - File: " . $this->errorLog->getFile() . "\n";
            $out .= " - Message: " . $this->errorLog->getMessage() . "\n";

            $out .= "\n\n\n";
        }

        $requestInfo = Network::getSessionRequestLog();
        $numOfRequests = count($requestInfo);

        if ($numOfRequests > 0)
        {
            $out .= "=== Network information (messy :P) ===\n";       

            foreach ($requestInfo as $request)
            {
                /** @var Promise */
                $requestPromise = $request["INTERNAL_promise"];
                unset($request["INTERNAL_promise"]);

                $out .= var_export($request, true);
                $out .= "\n";
                
                if ($requestPromise->status != PromiseStatus::PENDING)
                {
                    /** @var Response */
                    $result = $requestPromise->result;
                    $status = $result->status;
                    $headers = $result->headers;
                    $text = $result->getText();
                    $textLen = strlen($text);

                    $out .= "\n====== Response information =====\n\n";
                    $out .= " - Status: " . (string)$status . "\n";
                    $out .= " - Headers: " . var_export($headers, true) . "\n";
                    $out .= " - Response text($textLen):\n";
                    $out .= $text;
                    $out .= "\n\n\n\n\n";

                    $out = str_replace(
                        "<MESSAGE_NETWORK_PRIVACY_PLACEHOLDER>",
                        "\nHey you!! *Read me before you upload this anywhere!*\n" .
                        "This log file includes the dumps of your network responses, which may include\n" .
                        "some private information that you might not want uploaded (including but not\n" .
                        "limited to: your IP address, email address, YT channel link, etc.).\n" .
                        "\n" .
                        "If you don't wish to submit this personal information, then please find and\n" .
                        "replace any and all instances of sensitive information included in this file\n" .
                        "prior to upload.\n" .
                        "\n" .
                        "Thanks for reading, and have a good day!\n\n\n",
                        $out
                    );
                }
                else
                {
                    $out .= "\n====== No response information available =====\n\n";
                    $out = str_replace(
                        "<MESSAGE_NETWORK_PRIVACY_PLACEHOLDER>",
                        "",
                        $out
                    );
                }
            }
        }

        return $out;
    }

    private function getOperatingSystem(): string
    {
        $runtimeInfo = new RuntimeInfo;
        return $runtimeInfo->osDisplayName . " ($runtimeInfo->internalOsName $runtimeInfo->osBuildNumber)";
    }

    private function getServerSoftware(): string
    {
        $runtimeInfo = new RuntimeInfo;
        return $runtimeInfo->serverVersion;
    }

    private function getSuccessfulRequests(): string
    {
        $requestInfo = Network::getSessionRequestLog();

        $numOfRequests = count($requestInfo);
        $numSuccessful = 0;
        $numFailed = 0;
        $numPending = 0;

        foreach ($requestInfo as $request)
        {
            /** @var Promise */
            $requestPromise = $request["INTERNAL_promise"];

            switch ($requestPromise->status)
            {
                case PromiseStatus::PENDING:
                    $numPending++;
                    break;
                case PromiseStatus::RESOLVED:
                    $numSuccessful++;
                    break;
                case PromiseStatus::REJECTED:
                    $numFailed++;
                    break;
            }
        }

        $out = "{$numSuccessful}/{$numOfRequests}";

        if ($numFailed || $numPending)
        {
            $out .= " (";
            $notesCount = 0;

            if ($numPending)
            {
                $out .= "{$numPending} pending";
                $notesCount++;
            }

            if ($numFailed)
            {
                if ($notesCount != 0)
                {
                    $out .= ", ";
                }

                $out .= "{$numFailed} failed";
                $notesCount++;
            }

            $out .= ")";
        }

        return $out;
    }

    private function getRouterInfo(): string
    {
        $info = Router::getInternalDebug();
        $type = $info["type"];
        $route = $info["route"];

        return "$type, $route";
    }

    private function indentFullString(string $input): string
    {
        return implode(array_map(fn($in) => "   " . $in . "\n", explode("\n", $input)));
    }

    private function indentDumpedObject(string $dumpedObject): string
    {
        $out = "";
        $lines = explode("\n", $dumpedObject);
        $out .= $lines[0];

        for ($i = 1; $i < count($lines); $i++)
        {
            $out .= "\n   " . $lines[$i];
        }

        return $out;
    }
}