<?php
namespace YukisCoffee\CoffeeRequest\Handler\Curl;

use YukisCoffee\CoffeeRequest\Enum\RedirectPolicy;
use YukisCoffee\CoffeeRequest\Network\Request;
use YukisCoffee\CoffeeRequest\Util\NameserverInfo;
use YukisCoffee\CoffeeRequest\CoffeeRequest;

// cURL imports:
use CurlHandle;

use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_ENCODING;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_HEADERFUNCTION;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_RESOLVE;
use function curl_init;
use function curl_setopt_array;

/**
 * Utility class for transforming a Request object into a
 * cURL handler.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class RequestTransformer
{
    public const CURL_SUPPORTED_ENCODINGS = [
        "gzip",
        "identity",
        "deflate"
    ];

    private function __construct() {}

    /**
     * Convert a CoffeeRequest Request object to a cURL handle.
     */
    public static function convert(Request $request): RequestWrapper
    {
        $ch = curl_init($request->url);
        $wrapper = new RequestWrapper($request, $ch);

        curl_setopt_array($ch, self::convertOptions($wrapper));

        return $wrapper;
    }

    /**
     * Convert the request options used by Request objects into a
     * cURL option array.
     * 
     * @return mixed[]
     */
    public static function convertOptions(RequestWrapper $wrapper): array
    {
        $request = $wrapper->instance;

        $target = [
            // You almost never want this false, especially for this
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADERFUNCTION => self::getHeaderFunction($wrapper)
        ];

        // Encoding:
        $target[CURLOPT_ENCODING] = isset($request->preferredEncoding)
            ? self::filterEncoding($request->preferredEncoding)
            : ""
        ;

        // Method:
        $target[CURLOPT_CUSTOMREQUEST] = $request->method;

        // Headers:
        $target[CURLOPT_HTTPHEADER] = self::convertHeaders($request->headers);

        // Post body:
        if ("POST" == $request->method && isset($request->body))
        {
            $target[CURLOPT_POSTFIELDS] = $request->body;
        }

        // Redirect:
        if (isset($request->redirectPolicy)) switch ($request->redirectPolicy)
        {
            case RedirectPolicy::FOLLOW:
                $target[CURLOPT_FOLLOWLOCATION] = true;
                break;
            case RedirectPolicy::MANUAL:
                $target[CURLOPT_FOLLOWLOCATION] = false;
                break;
        }

        // Misc:
        $target[CURLOPT_RESOLVE] = CoffeeRequest::getResolve();

        return $target;
    }

    /**
     * Filter the preferred encoding provided by the Request object
     * and ignore it if it's not supported by cURL.
     */
    public static function filterEncoding(string $encoding): string
    {
        if (in_array($encoding, self::CURL_SUPPORTED_ENCODINGS))
        {
            return $encoding;
        }

        return "";
    }

    /**
     * Convert a CoffeeRequest headers associative array to a cURL-
     * compatible iterated array.
     * 
     * @param string[]
     * @return string[]
     */
    public static function convertHeaders(array $headers): array
    {
        $curlHeaders = [];

        foreach ($headers as $header => $value)
        {
            $curlHeaders[] = "{$header}: {$value}";
        }

        return $curlHeaders;
    }

    /**
     * Returns a unique anonymous function bound to a Request's wrapper.
     * 
     * CURLOPT_HEADERFUNCTION is called back for each header received during
     * response. These must be coalesced into the Response later.
     * 
     * @see https://stackoverflow.com/a/41135574
     */
    public static function getHeaderFunction(RequestWrapper $w): callable
    {
        return function($ch, $header) use ($w) {
            $headers = &$w->responseHeaders;

            $len = strlen($header);
            $header = explode(":", $header, 2);

            // Ignore invalid headers
            if (count($header) < 2)
            {
                return $len;
            }

            $normalizedName = strtolower(trim($header[0]));

            if (!isset($headers[$normalizedName]))
            {
                $headers[$normalizedName] = trim($header[1]);
            }
            else if (!is_array($headers[$normalizedName]))
            {
                $headers[$normalizedName] = [$headers[$normalizedName]];
                $headers[$normalizedName][] = trim($header[1]);
            }
            else
            {
                $headers[$normalizedName][] = trim($header[1]);
            }

            return $len;
        };
    }
}