<?php
namespace Rehike\Nepeta\IPC\Process;

use Closure;

/**
 * Manages inter-process communciation between the Nepeta server and client.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class NepetaIpc
{
    public string $serverAddr;
    private $socket;

    /**
     * @var Closure(array &$result)
     */
    private Closure $cbMessageHandler;

    public function __construct()
    {
        if (defined("Rehike\\Nepeta\\NEPETA_CONTEXT_CLIENT"))
        {
            $this->serverAddr = \Rehike\Nepeta\NEPETA_INTERNAL_SERVER_ADDRESS;
            $this->socket = stream_socket_client($this->serverAddr, $errno, $errstr, 10);
        }
        else
        {
            $this->serverAddr = "udp://127.0.0.1:" . $this->generateRandomPort();
            $this->socket = stream_socket_server($this->serverAddr, $errno, $errstr, STREAM_SERVER_BIND);
        }

        stream_set_blocking($this->socket, false);
    }

    public function __destruct()
    {
        fclose($this->socket);
    }

    public function registerMessageHandler(callable $handler)
    {
        $this->cbMessageHandler = Closure::fromCallable($handler);
    }

    public function listen(): void
    {
        if (!$this->socket)
        {
            throw new \Exception("fuck you");
        }
        else
        {
            // while ($conn = @stream_socket_accept($socket, 1))
            // {
            //     $message = fread($conn, 1024);
            //     \Rehike\Logging\DebugLogger::print($message);
            //     fclose($conn);
            // }

            // $data = stream_socket_recvfrom($socket, 1024);
            // $data2 = stream_socket_recvfrom($socket, 1024);
            // \Rehike\Logging\DebugLogger::print($data);
            // \Rehike\Logging\DebugLogger::print($data2);

            $readWatch = [$this->socket];
            $writeWatch = null;
            $exceptWatch = null;

            $startTime = time();

            // Any miss where the script stalls has a significant hit of 2 seconds on the
            // execution time.
            // if (defined("\Rehike\Nepeta\NEPETA_CONTEXT_CLIENT"))
            // {
            //     while (true)
            //     {
            //         if (!feof($socket))
            //         {
            //             $data = fgets($socket, 1024);

            //             if ($data)
            //             {
            //                 $resultStruct = ($this->cbMessageHandler)($data);
            //             }
            //         }
            //     }
            // }
            // else
            $timeout = defined("REHIKE_NEPETA_DEBUG") ? 1 : 2;
            while (($numChanged = stream_select($readWatch, $writeWatch, $exceptWatch, $timeout)) || true)
            {
                //if (defined("REHIKE_NEPETA_DEBUG")) \RehikeNepetaBase\reportEarlyError("hi");

                if ($numChanged === false)
                {
                    // error occurred
                    if (defined("REHIKE_NEPETA_DEBUG")) \RehikeNepetaBase\reportEarlyError("bad");
                    \Rehike\Logging\DebugLogger::print("error");
                    break;
                }
                else if ($numChanged > 0)
                {
                    if (defined("REHIKE_NEPETA_DEBUG")) \RehikeNepetaBase\reportEarlyError("!!!");
                    for ($i = 0; $i < $numChanged; $i++)
                    {
                        $data = stream_socket_recvfrom($this->socket, 1024);

                        if ($data == "<Nepeta:EndMessageStream>")
                        {
                            // In this case, we terminate the loop.
                            break 2;
                        }
                        else
                        {
                            $resultStruct = ($this->cbMessageHandler)($data);
                        }

                        \Rehike\Logging\DebugLogger::print($data);
                    }
                }
                else if ($numChanged === 0)
                {
                    // In this case, we stalled for too long and timed out
                    // prematurely. This is not good.
                    if (defined("REHIKE_NEPETA_DEBUG")) \RehikeNepetaBase\reportEarlyError("bad2");
                    \Rehike\Logging\DebugLogger::print("Nepeta IPC timeout.");
                    $this->sendMessage("<Nepeta:ClientTimeout>");
                    break;
                }

                // if (time() >= $startTime + 5)
                // {
                //     break;
                // }
            }
        }

        //throw new \Exception("done!");
    }

    public function sendMessage(string $message): void
    {
        \Rehike\Logging\DebugLogger::print("Sent message: " . $message);
        stream_socket_sendto($this->socket, $message);
    }

    public function getSocketHandle()
    {
        return $this->socket;
    }

    private function generateRandomPort(): int
    {
        return 6007;
        //return rand(1024, 65535);
    }
}