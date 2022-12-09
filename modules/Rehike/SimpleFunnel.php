<?php
namespace Rehike;

/**
 * A simple tool to funnel requests from a certain domain,
 * while ignoring any proxies active
 * 
 * @author Aubrey P. <aubyomori@gmail.com>
 */
class SimpleFunnel {
    /**
     * Hostname for funnelCurrentPage
     * 
     * @var string
     */
    public static $hostname = "www.youtube.com";

    /**
     * Remove these request headers
     * LOWERCASE ONLY
     * 
     * @var string[]
     */
    public static $illegalRequestHeaders = [
        "accept",
        "accept-encoding",
        "host",
        "origin",
        "referer"
    ];

    /**
     * Remove these response headers
     * LOWERCASE ONLY
     * 
     * @var string[]
     */
    public static $illegalResponseHeaders = [
        "content-encoding",
        "content-length"
    ];

    /**
     * Funnel a response through.
     * 
     * @param array $opts  Options such as headers and request method
     * @return object
     */
    public static function funnel(array $opts): object {
        // Required fields
        if (!isset($opts["host"])) return (object) [
            "error" => "No hostname specified"
        ];
        if (!isset($opts["uri"])) return (object) [
            "error" => "No URI specified"
        ];

        // Default options
        $opts += [
            "method" => "GET",
            "useragent" => "SimpleFunnel/1.0",
            "body" => "",
            "headers" => []
        ];

        // Parse headers
        $headers = [];
        foreach ($opts["headers"] as $name => $value) {
            if (!in_array(strtolower($name), self::$illegalRequestHeaders)) {
                $headers[] = "$name: $value";
            }
        }

        // Set origin and referer to prevent CORS issues
        $headers["Origin"] = "https://" . $opts["host"];
        $headers["Referer"] = "https://" . $opts["host"] . $opts["uri"];

        // Set up cURL and perform the request
        $url = "https://" . $opts["host"] . $opts["uri"];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $opts["method"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_VERBOSE => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => $opts["useragent"],
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $opts["body"],
            CURLOPT_ENCODING => "",
            CURLOPT_RESOLVE => \YukisCoffee\CoffeeRequest\CoffeeRequestInstance::$resolve,
            CURLOPT_HEADERFUNCTION =>
            // This function allows us to get the headers easily
            function($curl, $header) use (&$headers) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    return $len;
            
                $headers[trim($header[0])][] = trim($header[1]);
                
                return $len;
            }
        ]);

        // Initialize array for the header function defined above
        $headers = [];

        // Set up response and add body
        $response = (object) [];
        $response -> body = curl_exec($ch);

        // Remove illegal response headers
        foreach ($headers as $name => $value) {
            if (in_array(strtolower($name), self::$illegalResponseHeaders)) {
                unset($headers[$name]);
            }
        }

        // Add headers and HTTP status
        $response -> headers = $headers;
        $response -> status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        return $response;
    }

    /**
     * Output a funnel response onto the page.
     * 
     * @param object $funnelData
     */
    public static function output(object $funnelData): void {
        // Output any errors
        if (isset($funnelData -> error)) {
            http_response_code(500);
            echo("
            <title>SimpleFunnel Error</title>
            <style>body>*{margin:8px 0}</style>
            <h2>An error has occured in SimpleFunnel</h2>
            <p><b>Error</b>: " . $funnelData -> error . "</p>
            <small><i>Please report this to the GitHub.</i></small>
            ");
            return;
        }

        if (!isset($funnelData -> body)) return;

        http_response_code($funnelData -> status);

        // Set headers
        foreach($funnelData -> headers as $name => $value) {
            // Hack because the header function fucking sucks
            $val = $value[0];
            header("$name: $val");
        }
        echo($funnelData -> body);
        die();
    }
    
    /**
     * Funnel a page with the current data.
     * 
     * @param  bool $output  Whether or not to output the page
     * @return object|void
     */
    public static function funnelCurrentPage(bool $output = false): ?object {
        $response = self::funnel([
            "method" => $_SERVER["REQUEST_METHOD"],
            "host" => self::$hostname,
            "uri" => $_SERVER["REQUEST_URI"],
            "useragent" => $_SERVER["HTTP_USER_AGENT"],
            "body" => file_get_contents("php://input"),
            "headers" => getallheaders()
        ]);

        if ($output) {
            self::output($response);
            return null;
        } else {
            return $response;
        }
    }
}