<?php
namespace Rehike\ControllerV2;

use Rehike\RequestSession;
use Rehike\Ds\EasyAccessMap;

/**
 * Return accessible information from a request.
 * 
 * This is passed as the third argument of each controller
 * function. It provides various metadata, such as the request
 * method, path (as an array), and parameters (for GET/POST).
 * 
 * @author The Rehike Maintainers
 */
class RequestMetadata
{
    public string $method;

    /** @var string[] */
    public array $path;

    public string $rawPath;

    /** @var string[] */
    public EasyAccessMap $headers;

    public EasyAccessMap $params;

    public mixed $body;

    public function __construct()
    {
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->path = self::getPath();
        $this->rawPath = $_SERVER["REQUEST_URI"];
        $this->headers = self::getHeaders();
        $this->params = new EasyAccessMap($_GET);

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
     * @return string[]
     */
    protected static function getPath(): array
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
    protected static function getHeaders(): EasyAccessMap
    {
        return RequestSession::getRequestHeaders();
    }

    /**
     * Get the POST body, attempt to decode it, and then
     * return it.
     * 
     * @param string $contentType
     * @return mixed
     */
    protected static function getPostBody(string $contentType): mixed
    {
        return RequestSession::getPostBody($contentType);
    }
}