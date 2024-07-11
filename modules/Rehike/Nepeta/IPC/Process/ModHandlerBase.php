<?php
namespace Rehike\Nepeta\IPC\Process;

/**
 * 
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class ModHandlerBase
{
    protected NepetaIpc $ipc;

    public function __construct()
    {
        $this->ipc = new NepetaIpc;
        $this->ipc->registerMessageHandler([$this, "handleMessageWrapper"]);

        $this->init();
    }

    abstract protected function init(): void;

    public function handleMessageWrapper(string $originalMessage): array
    {
        $message = $this->parseMessage($originalMessage);
        $messageId = $message[0];
        $args = array_slice($message, 0);

        return $this->handleMessage($messageId, $args);
    }

    abstract protected function handleMessage(string $messageId, array $args): array;

    public function sendMessage(string $messageId, array $args = []): void
    {
        $encodedMessage = "<Nepeta:" . $messageId;

        if (count($args) > 0)
        {
            $encodedMessage .= " ";
            $encodedMessage = implode(" ", $args);
        }

        $encodedMessage .= ">";

        $this->ipc->sendMessage($encodedMessage);
    }

    public function listen(): void
    {
        $this->ipc->listen();
    }

    public function getSocket()
    {
        return $this->ipc->getSocketHandle();
    }

    /**
     * Parses a message.
     * 
     * The messages are reliably encoded in the following format:
     * <Nepeta:MESSAGE_ID ARGUMENTS>
     * where arguments are any arbitrary data not including the character ">".
     * 
     * If that must be encoded, then use "&gt;".
     */
    protected function parseMessage(string $message): array
    {
        $innerMessage = explode("<Nepeta:", $message)[1];
        $innerMessage = explode(">", $innerMessage)[0];

        $innerMessage = str_replace("&gt;", ">", $innerMessage);
        $innerMessage = str_replace("&amp;", "&", $innerMessage);

        return explode(" ", $innerMessage);
    }
}