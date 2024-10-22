<?php
namespace Rehike\Exception\Network;

use Rehike\Exception\AbstractException;
use Rehike\Network\IResponse;

use Exception;

class InnertubeFailedRequestException extends Exception
{
    public IResponse $failedResponse;

    public function __construct(
            IResponse $failedResponse
    )
    {
        $this->failedResponse = $failedResponse;
    }
}