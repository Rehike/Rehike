<?php
namespace Rehike\Network\Internal;

use Rehike\Network\{
    IRequest,
    IResponse,
    ResponseHeaders,
};

use Rehike\Network\Exception\RequestFailedResponseCodeException;
use Rehike\Network\Enum\RequestErrorPolicy;
use Rehike\Network\Enum\NetworkResult;

use Exception;
use function json_decode;

/**
 * Represents a network response.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Response implements IResponse
{
    /**
     * A reference to the source request.
     */
    public IRequest $sourceRequest;

    /**
     * The response status.
     */
    public int $status = 0;

    /**
     * Result status of the network request itself.
     *
     * @var NetworkResult
     */
    public int $resultCode = NetworkResult::E_UNIMPL;

    /**
     * An array of HTTP headers sent back from the server with the
     * response.
     */
    public ResponseHeaders $headers;

    /**
     * The response as a string (byte array).
     */
    private string $content = "";

    public function __construct(
            Request $source,
            int $status, 
            string $content,
            array $headers
    )
    {
        if (
            RequestErrorPolicy::THROW == $source->onError &&
            $status < 200 &&
            $status > 399
        )
        {
            throw new RequestFailedResponseCodeException(
                "Request to $source->url failed with response code of $status."
            );
        }

        $this->sourceRequest = $source;
        $this->status = $status;
        $this->content = $content;
        $this->headers = new ResponseHeaders($headers);
    }

    /**
     * Get a text representation of the response.
     */
    public function getText(): string
    {
        return $this->content;
    }

    /**
     * Get the response decoded as JSON.
     */
    public function getJson(): object|array
    {
        if ($a = @json_decode($this->content))
        {
            return $a;
        }
        else
        {
            throw new Exception(
                "Response content is not valid JSON."
            );
        }
    }

    public function __toString(): string
    {
        return $this->getText();
    }
}
