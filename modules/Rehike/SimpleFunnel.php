<?php
namespace Rehike;

use Rehike\Async\Promise;

use Rehike\Network\NetworkCore;
use Rehike\Network\IRequest;
use Rehike\Network\IResponse;
use Rehike\Network\ResponseHeaders;

/**
 * A simple tool to funnel requests from a certain domain, while ignoring any
 * proxies active
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 */
class SimpleFunnel
{
    /**
     * Hostname for funnelCurrentPage.
     * 
     * @var string
     */
    private static $hostname = "www.youtube.com";

    /**
     * Remove these request headers.
     * LOWERCASE ONLY
     * 
     * @var string[]
     */
    public static $illegalRequestHeaders = [
        "accept",
        "accept-encoding",
        "host",
        //"origin",
        //"referer"
    ];

    /**
     * Remove these response headers.
     * LOWERCASE ONLY
     * 
     * @internal
     * @var string[]
     */
    public static $illegalResponseHeaders = [
        "content-encoding",
        "content-length",
        "transfer-encoding" // broke linux for months lol
    ];

    /**
     * Funnel a response through.
     * 
     * @param array $opts  Options such as headers and request method
     * @return Promise<SimpleFunnelResponse>
     */
    public static function funnel(array $opts): Promise/*<SimpleFunnelResponse>*/
    {
        // Required fields
        if (!isset($opts["host"])) 
            self::error("No hostname specified");

        if (!isset($opts["uri"]))
            self::error("No URI specified");

        // Default options
        $opts += [
            "method" => "GET",
            "useragent" => "SimpleFunnel/1.0",
            "body" => "",
            "headers" => []
        ];

        $headers = [];

        foreach ($opts["headers"] as $key => $val)
        {
            if (!in_array(strtolower($key), self::$illegalRequestHeaders))
            {
                $headers[$key] = $val;
            }
        }

        $headers["Host"] = $opts["host"];
        // $headers["Origin"] = "https://" . $opts["host"];
        // $headers["Referer"] = "https://" . $opts["host"] . $opts["uri"];

        // Set up cURL and perform the request
        $url = "https://" . $opts["host"] . $opts["uri"];

        // Set up the request.
        $params = [
            "method" => $opts["method"],
            "headers" => $headers,
            "redirect" => "manual",
        ];

        if ("POST" == $params["method"])
        {
            $params["body"] = $opts["body"];
        }

        $wrappedResponse = new Promise/*<Response>*/;

        $request = NetworkCore::request($url, $params);

        $request->then(function($response) use ($wrappedResponse) {
            $wrappedResponse->resolve(SimpleFunnelResponse::fromResponse($response));
        });

        NetworkCore::run();

        return $wrappedResponse;
    }

    /**
     * Convert a list of response headers to HTTP-compatible ones.
     */
    public static function responseHeadersToHttp(
            ResponseHeaders $headers, 
            bool $ignoreIllegal = true
    ): array
    {
        $out = [];

        foreach ($headers as $name => $value)
        {
            if (is_array($value))
            {
                foreach ($value as $childValue)
                {
                    $out[] = $name . ": " . $childValue;
                }
            }
            else
            {
                $out[] = $name . ": " . $value;
            }
        }

        return $out;
    }

    /**
     * Output an error.
     */
    private static function error(string $message): void
    {
        http_response_code(500);
        echo("
        <title>SimpleFunnel Error</title>
        <style>body>*{margin:8px 0}</style>
        <h2>An error has occured in SimpleFunnel</h2>
        <p><b>Error</b>: " . $message . "</p>
        <small><i>Please report this to the GitHub.</i></small>
        ");
        return;
    }
    
    /**
     * Funnel a page with the current data.
     * 
     * @return Promise<SimpleFunnelResponse>
     */
    public static function funnelCurrentPage(): Promise/*<SimpleFunnelResponse>*/
    {
        return self::funnel([
            "method" => $_SERVER["REQUEST_METHOD"],
            "host" => self::$hostname,
            "uri" => $_SERVER["REQUEST_URI"],
            "useragent" => $_SERVER["HTTP_USER_AGENT"],
            "body" => file_get_contents("php://input"),
            "headers" => getallheaders()
        ]);
    }
}

/**
 * A custom class that represents a SimpleFunnel response.
 * 
 * @author The Rehike Maintainers
 */
class SimpleFunnelResponse extends \Rehike\Network\Internal\Response
{
    public static function fromResponse(IResponse $response): self
    {
        return new self(
            source: $response->sourceRequest,
            status: $response->status,
            content: $response->getText(),
            headers: self::processResponseHeaders($response->headers)
        );
    }

    /**
     * Output the response of the page.
     */
    public function output(): void
    {
        http_response_code($this->status);

        foreach (SimpleFunnel::responseHeadersToHttp($this->headers) as $httpHeader)
        {
            header($httpHeader, false);
        }
        
        echo($this->getText());
        exit();
    }

    /**
     * Process the response headers and remove illegal headers.
     */
    private static function processResponseHeaders(ResponseHeaders $headers): array
    {
        $result = [];

        foreach ($headers as $name => $value)
        {
            if (!in_array(strtolower($name), SimpleFunnel::$illegalResponseHeaders))
            {
                $result[$name] = $value;
            }
        }

        return $result;
    }
}