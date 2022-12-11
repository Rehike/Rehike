<?php
namespace YukisCoffee\CoffeeRequest\Handler\Curl;

use YukisCoffee\CoffeeRequest\Network\Request;

use CurlHandle;

/**
 * A structure that stores a cURL handle and the original Request object
 * for easy internal use.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class RequestWrapper
{
    /**
     * The request itself, which may feature additional metadata
     * not used by cURL but used by CoffeeRequest.
     */
    public Request $instance;

    /**
     * The cURL handle opened for the request.
     * 
     * @var CurlHandle|resource
     */
    public $handle;

    /**
     * An array of response headers.
     * 
     * This is a hacky solution, but it's required in order to work around
     * CURLOPT_HEADERFUNCTION being declared as part of the request. The
     * data store here will be coalesced into the Response later.
     * 
     * @var string[]
     */
    public array $responseHeaders = [];

    public function __construct(Request $r, &$h)
    {
        $this->instance = $r;
        $this->handle  = &$h;
    }
}