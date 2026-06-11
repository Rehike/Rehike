<?php
declare(strict_types=1);
namespace Rehike\Debugger;

/**
 * Denotes the expected type of the response of a logged request.
 * 
 * @enum
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
final class LoggedRequestType
{
    public const Unknown = 0;
    public const JsonData = 1;
    public const Url = 2;
    public const Innertube = 3;
    public const YoutubeDataApiV3 = 4;
}