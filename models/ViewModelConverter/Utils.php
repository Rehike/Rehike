<?php
namespace Rehike\Model\ViewModelConverter;

/**
 * Provides common utilities for the view model converters.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Utils
{
    /**
     * Detects the type of an InnerTube command, which is ambiguous in view
     * models.
     * 
     * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
     * @author The Rehike Maintainers
     */
    public static function detectInnertubeCommandType(object $command): string
    {
        // This is a good basis, since even arbitrary things are wrapped up in
        // a navigation endpoint wrapper, but other types still do exist.
        $detectedType = "navigationEndpoint";

        if (isset($command->commandMetadata->webCommandMetadata->apiUrl))
        {
            // Any command containing this is an actual InnerTube command
            // (unlike the OuterTube commands which get conflated with InnerTube
            // ones, like navigationEndpoint) in which a request to InnerTube is 
            // made, but InnerTube has historically called this a serviceEndpoint.
            $detectedType = "serviceEndpoint";
        }

        return $detectedType;
    }
}