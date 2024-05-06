<?php
namespace RehikeNepetaBase
{
    define("REHIKE_NEPETA_DEBUG", true);
    define("Rehike\\Nepeta\\NEPETA_CONTEXT_CLIENT", true);

    /**
     * Allows debug printing of Nepeta client initialization errors.
     */
    function reportEarlyError(string $message): void
    {
        if (REHIKE_NEPETA_DEBUG)
        {
            // Only works on Windows.
            if (extension_loaded("ffi"))
            {
                /** @var object IDE hack */
                $libc = \FFI::cdef(
                    "int MessageBoxA(
                        uint32_t hWnd,
                        void * lpText,
                        void * lpCaption,
                        uint32_t uType);", 
                    "user32.dll"
                );

                $libc->MessageBoxA(
                    0, // hwnd = NULL
                    "Nepeta init fatal error: " . $message, 
                    "Nepeta Client Error", 
                    0 // MB_OK
                );
            }
            else
            {
                $escapedMsg = str_replace('"', '\\u0022', $message);
                $escapedMsg = str_replace("'", "\\'", $escapedMsg);
                $escapedMsg = str_replace("\n", "\\n", $escapedMsg);
                popen("mshta \"javascript:alert('Nepeta init fatal error: $escapedMsg');close()\"", "r");
            }
        

            //exit();
        }
    }

    // This will get overridden once the socket is initialized, because then
    // we can report errors through there:
    if (REHIKE_NEPETA_DEBUG)
    {
        register_shutdown_function(function() {
            if (defined("REHIKE_NEPETA_CONNECTION_ESTABLISHED"))
                return;

            $lastError = error_get_last();

            reportEarlyError("Early shutdown. Last error: " . var_export($lastError, true));
        });
    }

    /*
     * We use arguments for initialization information for the socket itself.
     * 
     * The only things that are really needed are the root directory, which is
     * used for initializing the autoloader, and the server address, which is
     * used to connect to the server. Once a connection is established, all
     * other initialization data is sent over the socket.
     */
    function parseArgs($args): void
    {
        $key = "";
        $matchingValue = false;
        foreach ($args as $arg)
        {
            // Parsing keys (which begin with --).
            if (substr($arg, 0, 2) == "--")
            {
                $key = substr($arg, 2);

                if (!$matchingValue)
                {
                    // Bad args, but we can't reliably report this error, so we
                    // just die and hope the programmer can catch on.
                    reportEarlyError("Bad arguments.");
                    die();
                }

                // We just parsed a key, so toggle this to catch if there's a
                // subsequent key.
                $matchingValue = false;

                continue;
            }
            
            // Parsing values (anything else)
            $value = $arg; // better name
            switch (strtolower($key))
            {
                case "server_address":
                    define(
                        "Rehike\\Nepeta\\NEPETA_INTERNAL_SERVER_ADDRESS",
                        trim($value, "\"\'"));
                    break;

                case "root_directory":
                    define(
                        "Rehike\\Nepeta\\NEPETA_INTERNAL_ROOT",
                        trim($value, "\"\'"));
                    break;
            }

            // We're parsing a value, so denote this so the parser knows to
            // expect another key.
            $matchingValue = true;
        }
    }
    
    parseArgs($argv);
    assert(is_string(\Rehike\Nepeta\NEPETA_INTERNAL_SERVER_ADDRESS));
    assert(is_string(\Rehike\Nepeta\NEPETA_INTERNAL_ROOT));
}

namespace Rehike\Nepeta\Client
{
    include "nepeta_client_autoloader.php";
    set_time_limit(0);

    NepetaClient::init();
}