<?php
namespace Rehike\Nepeta\Client;

use Rehike\Nepeta\Process\ModHandlerBase;
use Override;

/**
 * 
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ClientModHandler extends ModHandlerBase
{
    protected function init(): void
    {
        $this->sendMessage("ClientConnected");
        $this->sendMessage("ClientHandshake");
    }

    /**
     * Handles messages send from the client.
     */
    #[Override]
    protected function handleMessage(string $messageId, array $args): array
    {
        $this->sendMessage("ClientRecieveMessage $messageId");

        \RehikeNepetaBase\reportEarlyError($messageId);

        switch ($messageId)
        {
            case "BeginServerHandshake":
                $this->sendMessage("Print hiiiii");
                break;
        }

        return [
            "status" => "success"
        ];
    }
}