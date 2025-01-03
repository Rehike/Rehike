<?php
namespace Rehike\Network;

use Rehike\Network\Enum\RequestErrorPolicy;
use Rehike\Network\Enum\NetworkResult;

/**
 * Interface for objects which act like a network response.
 * 
 * @property IRequest $sourceRequest  A reference to the source request.
 * @property int $status  The response HTTP status.
 * @property NetworkResult $resultCode  Result status of this network request
 *                                      itself.
 * @property ResponseHeaders $headers  An array of HTTP headers sent back from
 *                                     the server with the response.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
interface IResponse
{
    /**
     * Get a text representation of the response.
     */
    public function getText(): string;

    /**
     * Get the response decoded as JSON.
     */
    public function getJson(): object|array;

    public function __toString(): string;
}