<?php
$g_rehikeStartupConfig;

/**
 * Manages very-early startup configuration.
 */

if (file_exists("config.json"))
{
    $g_rehikeStartupConfig = @json_decode(file_get_contents("config.json"));
    
    if ($g_rehikeStartupConfig)
    {
        // EXPERIMENT -- Tick injection for scheduling:
        if (
            isset($g_rehikeStartupConfig->experiments->tickInjectionForScheduling) &&
            $g_rehikeStartupConfig->experiments->tickInjectionForScheduling == true
        )
        {
            require "includes/file_override_stream_wrapper.php";
            RehikeBase\FileOverrideStreamWrapper::wrap();
        }
    }
}