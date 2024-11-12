<?php
namespace Rehike\Network\Enum;

/**
 * Network result status codes.
 *
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class NetworkResult
{
    public const SUCCESS = 0;
    public const E_FAILED = 1;
    public const E_MALFORMED_URL = 2;
    public const E_COULDNT_RESOLVE_PROXY = 3;
    public const E_COULDNT_RESOLVE_HOST = 4;
    public const E_COULDNT_CONNECT = 5;
    public const E_UNIMPL = 6;
}