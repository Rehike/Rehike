<?php
namespace Rehike\SignInV2;

/**
 * Defines enumerations for types of session errors. These are used as a
 * bitmask.
 * 
 * @enum
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class SessionErrors
{
    public const SUCCESS = 0;
    public const FAILED_REQUEST = 1 << 0;
    public const CANCELLED_BUILD = 1 << 1;
}