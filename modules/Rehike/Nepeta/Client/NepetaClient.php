<?php
namespace Rehike\Nepeta\Client;

use const Rehike\Nepeta\NEPETA_INTERNAL_SERVER_ADDRESS;

/**
 * 
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class NepetaClient
{
    public static $g_socket;

    public static function init(): void
    {
        set_exception_handler(self::class."::exceptionHandler");

        $handler = new ClientModHandler;
        self::$g_socket = $handler->getSocket();
        $handler->listen();

        // global $argv;
        // self::$g_socket = stream_socket_client(NEPETA_INTERNAL_SERVER_ADDRESS, $errno, $errstr, 30);
        // if (!self::$g_socket)
        // {
        //     \RehikeNepetaBase\reportEarlyError("Failed to connect to socket.");
        //     exit();
        // }
        // else
        // {
        //     // Stop the early shutdown handler from doing anything:
        //     if (REHIKE_NEPETA_DEBUG)
        //     {
        //         define("REHIKE_NEPETA_CONNECTION_ESTABLISHED", true);
        //     }

        //     stream_socket_sendto(self::$g_socket, "<Nepeta:ClientConnected>");
        //     stream_socket_sendto(self::$g_socket, "<Nepeta:ClientBeginHandshake>");
        //     stream_socket_sendto(self::$g_socket, "Hello from client!! :3");
        // }

        // //throw new \Exception("Fuck");

        self::shutdown();

        //while (true); // block forever
    }

    public static function shutdown(): void
    {
        stream_socket_sendto(self::$g_socket, "<Nepeta:EndMessageStream>");
        //fclose(self::$g_socket);
    }

    public static function exceptionHandler(\Throwable $e)
    {
        stream_socket_sendto(self::$g_socket, var_export($e, true));
        stream_socket_sendto(self::$g_socket, "<Nepeta:EndMessageStream>");
        //fclose(self::$g_socket);
    }
}