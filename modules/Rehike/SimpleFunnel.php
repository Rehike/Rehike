<?php
/**
 * A simple tool to funnel requests from a certain domain,
 * while ignoring any proxies active
 * 
 * @author Aubrey P. <aubyomori@gmail.com>
 */
namespace Rehike;

class SimpleFunnel {
    /**
     * Hostname for funnelCurrentPage
     */
    public static $hostname = "www.youtube.com";

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

        $headers = [];
        foreach ($opts["headers"] as $key => $val) {
            if (!in_array($key, ["Accept", "Accept-Encoding", "Host", "Origin", "Referer"])) {
                $headers[] = "$key: $val";
            }
        }

        $headers["Origin"] = "https://" . $opts["host"];
        $headers["Referer"] = "https://" . $opts["host"] . $opts["uri"];

        $url = "https://" . $opts["host"] . $opts["uri"];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $opts["method"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => $opts["useragent"],
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $opts["body"],
            CURLOPT_ENCODING => ""
        ]);

        $response = (object) [];
        $response -> body = curl_exec($ch);
        $response -> contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $response -> status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        return $response;
    }

    /**
     * Output a funnel response onto the page.
     * 
     * @param object $funnelData
     */
    public static function output(object $funnelData): void {
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
        header("Content-Type: " . $funnelData -> contentType);
        echo($funnelData -> body);
        die();
    }
    
    /**
     * Funnel a page with the current data.
     * 
     * @param  bool $output  Whether or not to output the page
     * @return object|void
     */
    public static function funnelCurrentPage(bool $output = false) {
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
        } else {
            return $response;
        }
    }
}