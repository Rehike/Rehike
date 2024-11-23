<?php
namespace Rehike\Network;

use Rehike\Network\Enum\RedirectPolicy;
use Rehike\Network\Enum\RequestErrorPolicy;

/**
 * Interface for objects that act like network requests.
 * 
 * These currently only exist to create a custom class which custom
 * response-like classes. It's currently not possible to use your custom
 * request class to make a network request.
 * 
 * @property string $method  The request method to send.
 * @property string $url  The URL to request.
 * @property string[] $headers  An associative array of HTTP headers to send
 *                              with the request.
 * @property string $body  If specified, the POST body to be sent.
 * @property string $preferredEncoding  If specified, the preferred encoding to
 *                                      request with.
 * @property RedirectPolicy $redirectPolicy  If specified, the redirect policy
 *                                           to use with the request handler.
 * @property RequestErrorPolicy $onError  If specified, sets the error policy
 *                                        to use.
 * 
 *                                        If it's throw, then any request that
 *                                        isn't a 2xx status will throw an
 *                                        exception. if it's ignore, then the
 *                                        response is treated like normal.
 * @property string $userAgent  If specified, sets the user agent to be reported
 *                              for the request. If not specified, then the
 *                              behavior is left to the implementer. 
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
interface IRequest
{
}