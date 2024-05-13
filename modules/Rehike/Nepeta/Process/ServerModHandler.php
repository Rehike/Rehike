<?php
namespace Rehike\Nepeta\Process;

use Override;

/**
 * 
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ServerModHandler extends ModHandlerBase
{
    public const CLIENT_MAIN_SCRIPT = "nepeta_client_main.php";

    protected function init(): void
    {
        self::forkPhpProcess($this->ipc->serverAddr);
    }

    /**
     * Handles messages send from the client.
     */
    #[Override]
    protected function handleMessage(string $messageId, array $args): array
    {
        \Rehike\Logging\DebugLogger::print($messageId);

        switch ($messageId)
        {
            case "ClientConnected":
                break;
            case "ClientHandshake":
                $this->sendMessage("BeginServerHandshake");
                break;
        }

        return [
            "status" => "success"
        ];
    }

    private static function forkPhpProcess(string $serverAddr): void
    {
        $clientScriptPath = $_SERVER["DOCUMENT_ROOT"] . "/modules/Rehike/Nepeta/Client/" . self::CLIENT_MAIN_SCRIPT;

        $root = $_SERVER["DOCUMENT_ROOT"];

        $args = implode(' ', [
            "--server_address \"$serverAddr\"",
            "--root_directory \"$root\""
        ]);
        
        PhpProcessManager::createProcess($clientScriptPath, $args);
    }
}