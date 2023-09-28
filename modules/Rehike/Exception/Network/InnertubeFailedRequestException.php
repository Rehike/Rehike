<?php
namespace Rehike\Exception\Network;

use Rehike\Exception\AbstractException;
use YukisCoffee\CoffeeRequest\Network\Response;

use Exception;

// BUG(ev): cannot change call signature of __construct due to ICoffeeRequest
// (?!??!?!?!?!??!?!?!!?!?!??!?!?!?!)
// taniko FUCK YOU
class InnertubeFailedRequestException extends Exception
{
    public Response $failedResponse;

    public function __construct(
            Response $failedResponse
    )
    {
        $this->failedResponse = $failedResponse;
    }
}