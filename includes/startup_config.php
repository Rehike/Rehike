<?php
/**
 * Manages very-early startup configuration.
 */

if (file_exists("config.json"))
{
    $json = json_decode(file_get_contents("config.json"));
    
    if ($json)
    {
        // EXPERIMENT -- Tick injection for scheduling:
        if (
            isset($json->experiments->tickInjectionForScheduling) &&
            $json->experiments->tickInjectionForScheduling == true
        )
        {
            require "includes/file_override_stream_wrapper.php";
            RehikeBase\FileOverrideStreamWrapper::wrap();
        }

        // Nepeta startup: We want to know if Nepeta is enabled before startup
        // is conducted, but need to be careful about calling anything before
        // the autoloader is available, so we just set a global flag.
        if (
            isset($json->experiments->enableNepeta) &&
            $json->experiments->enableNepeta
        )
        {
            global $g_fRehikeNepetaEnabled;
            $g_fRehikeNepetaEnabled = true;
        }
    }
}