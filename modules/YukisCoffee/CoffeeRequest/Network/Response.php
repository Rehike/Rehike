<?php
namespace YukisCoffee\CoffeeRequest\Network;

use YukisCoffee\CoffeeRequest\Exception\GeneralException;
use YukisCoffee\CoffeeRequest\Exception\RequestFailedResponseCodeException;
use YukisCoffee\CoffeeRequest\Enum\RequestErrorPolicy;

use function json_decode;

/**
 * Represents a network response.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class Response
{
    /**
     * A reference to the source request.
     */
    public Request $sourceRequest;

    /**
     * The response status.
     */
    public int $status = 0;

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
    public function getJson(): object
    {
        /*
         * TODO (kirasicecreamm): Slow validation method.
         * 
         * This should be cleaned up eventually and replaced with a more 
         * efficient one. Of keen interest is the json_validate() function, 
         * which is slated to be released in PHP 8.3.
         * 
         * As a bleeding edge feature, it may be implemented as an
         * alternative path for use in the target language runtime only,
         * and the slower method used here will be kept for previous
         * versions.
         * 
         * https://wiki.php.net/rfc/json_validate
         */ 
        if ($a = @json_decode($this->content))
        {
            return $a;
        }
        else
        {
            throw new GeneralException(
                "Response content is not valid JSON."
            );
        }
    }

    public function __toString(): string
    {
        return $this->getText();
    }
}