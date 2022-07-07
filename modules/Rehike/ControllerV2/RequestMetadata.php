<?php
namespace Rehike\ControllerV2;

use Rehike\DataArray;

/**
 * Return accessible information from a request.
 * 
 * This is passed as the third argument of each controller
 * function. It provides various metadata, such as the request
 * method, path (as an array), and parameters (for GET/POST).
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class RequestMetadata
{
    /** @var string */
    public $method;

    /** @var string[] */
    public $path;

    /** @var string */
    public $rawPath;

    /** @var string[] */
    public $headers;

    /** @var string[]|mixed */
    public $params;

    /** @var mixed|null */
    public $body;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = self::getPath();
        $this->rawPath = $_SERVER["REQUEST_URI"];
        $this->headers = self::getHeaders();
        $this->params = new DataArray($_GET);

        // Body will only be available for POST requests
        if ("POST" == $this->method)
        {
            $this->body = self::getPostBody($this->headers["Content-Type"] ?? "");
        }
        else
        {
            unset($this->body);
        }
    }

    /**
     * Get the path name from the request and return it
     * as a split array.
     * 
     * @return string
     */
    protected static function getPath()
    {
        // Split the path first by "?" to remove params
        $path = explode("?", $_SERVER["REQUEST_URI"])[0];

        // Then split it by "/"
        $path = explode("/", $path);

        // If the first item is empty, remove it
        if ("" == $path[0])
        {
            array_splice($path, 0, 1);
        }

        return $path;
    }

    /**
     * Get all requested headers.
     * 
     * @return string[]
     */
    protected static function getHeaders()
    {
        // Apache (only) has apache_request_headers
        // function:
        if (function_exists("\apache_request_headers"))
        {
            $headers = [];

            // Convert all elements to lowercase
            foreach (\apache_request_headers() as $key => $value)
            {
                $headers += [strtolower($key) => $value];
            }


            /**
             * Friendly API for accessing HTTP headers.
             * 
             * This converts header names to camelCase at
             * access time, allowing them to be accessed with
             * simple variable names. It can also be accessed like an
             * array.
             * 
             * For example:
             * 
             *      Content-Type == contentType
             */
            return new DataArray($headers);
        }
        else
        {
            // If the above function doesn't exist,
            // iterate $_SERVER and collect all HTTP_
            // variables.
            $headers = [];

            foreach ($_SERVER as $key => $value)
            {
                if ("HTTP_" != substr($key, 0, 5)) continue;

                // Not so hellish anymore now that I've stopped caring about
                // the case.
                $newKey = str_replace('_', '-', strtolower(substr($key, 5)));

                $headers += [$newKey => $value];
            }

            return new DataArray($headers);
        }
    }

    /**
     * Get the POST body, attempt to decode it, and then
     * return it.
     * 
     * @param string $contentType
     * @return mixed
     */
    protected static function getPostBody($contentType)
    {
        // $_POST is only used by x-www-form-urlencoded
        // and multipart/form content types. If this
        // is available, return it.
        if (!empty($_POST))
        {
            return $_POST;
        }

        // Otherwise, the input needs to be retrieved and
        // decoded manually.
        $body = @file_get_contents("php://input");

        if (false == $body) return null; // Validate

        // Switch on the content type and attempt to
        // automatically parse contents.
        switch ($contentType)
        {
            case "application/json":
                $obj = @json_decode($body);

                if (null != $obj) return $obj;
                else return $body;

                break;
        }

        // Otherwise return the raw input.
        return $body;
    }
}