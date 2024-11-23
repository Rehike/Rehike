<?php
namespace Rehike\Network\Exception;

use Exception;

/**
 * Thrown when a network request responds with a non-2xx code, indicating
 * that the server rejected it or failed.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class RequestFailedResponseCodeException extends Exception {}