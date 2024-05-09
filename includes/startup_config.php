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
    }
}