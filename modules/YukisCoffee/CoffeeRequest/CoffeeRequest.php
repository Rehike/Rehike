<?php
namespace YukisCoffee\CoffeeRequest;

class CoffeeRequest
{
    public static $requestsMaxAttempts = 50;
    public static $defaultOptions =
        [
            "post" => false,
            "returnTransfer" => true,
            "encoding" => "gzip",
            "headers" => [] // ** Set by $defaultHeaders
        ];
    public static $defaultHeaders = [];
    public static $requestQueue = [];

    public static function _init()
    {
        self::$defaultHeaders += ["Cookie" => self::genCookieHeader()];
    }

    public static function request($url, $options = [])
    {
        self::addDefaultOptions($options);
        
        // Init curl
        $ch = curl_init($url);
        curl_setopt_array($ch, self::reqOpts2Curl($options));

        // Do request
        $attempts = 0;
        do
        {
            $response = curl_exec($ch);
            $attempts++;
        }
        while (200 !== curl_getinfo($ch, \CURLINFO_HTTP_CODE) && $attempts < self::$requestsMaxAttempts);

        curl_close($ch);

        if (isset($response) && false !== $response)
        {
            return $response;
        }
    }

    public static function queueRequest($url, $options = [], $id = null)
    {
        self::addDefaultOptions($options);

        // Init curl
        $ch = curl_init($url);
        curl_setopt_array($ch, self::reqOpts2Curl($options));

        // Add to request queue
        if (is_null($id)) $id = count(self::$requestQueue) - 1;
        self::$requestQueue += [$id => $ch];
    }

    public static function runQueue()
    {
        $mh = curl_multi_init();

        // Register all queued requests
        foreach (self::$requestQueue as $id => $handle)
        {
            curl_multi_add_handle($mh, $handle);
        }

        // Do requests
        do
        {
            $status = curl_multi_exec($mh, $active);
            if ($active) curl_multi_select($mh);
        }
        while ($active && \CURLM_OK == $status);

        // Form response assocarray by id and close handle
        $responses = [];

        foreach (self::$requestQueue as $id => $handle)
        {
            $responses += [$id => curl_multi_getcontent($handle)];
            curl_multi_remove_handle($mh, $handle);
        }

        // Close curl and return
        curl_multi_close($mh);

        self::clearRequestQueue();

        return $responses;
    }

    public static function clearRequestQueue()
    {
        self::$requestQueue = [];
    }

    public static function addDefaultOptions(&$options)
    {
        // PHP array addition for assocarrays ignores
        // value, meaning only unset keys are added.
        // So it's just one line:

        $options += self::$defaultOptions;
        if (isset($options["headers"])) $options["headers"] += self::$defaultHeaders;
    }

    public static function reqOpts2Curl($options)
    {
        $curlArr = [];

        foreach ($options as $option => $value)
        {
            switch ($option)
            {
                // Map option names to CURLOPT names
                case "post": 
                    $curlOpt = \CURLOPT_POST; 
                    break;
                case "returnTransfer": 
                    $curlOpt = \CURLOPT_RETURNTRANSFER; 
                    break;
                case "encoding":
                    $curlOpt = \CURLOPT_ENCODING;
                    break;
                case "body":
                    $curlOpt = \CURLOPT_POSTFIELDS;
                    break;
                case "headers":
                    $curlOpt = \CURLOPT_HTTPHEADER;
                    $value = self::reqHeaders2Curl($value);
                    break;
                case "overrideResolve":
                    $curlOpt = \CURLOPT_RESOLVE;
                    break;
                default: continue 2;
            }
            
            $curlArr[$curlOpt] = $value;
        }
        
        return $curlArr;
    }

    public static function reqHeaders2Curl($headers)
    {
        $curlHeaders = [];
        
        foreach ($headers as $header => $value)
        {
            $curlHeaders[] = $header . ': '.  $value;
        }
        
        return $curlHeaders;
    }

    public static function genCookieHeader()
    {
        if (empty($_COOKIE)) return "";
        
        $cookies = "";
        
        // Stringify cookies into HTTP format.

        foreach ($_COOKIE as $cookie => $value)
        {
            $cookies .= $cookie . '=' . $value . '; ';
        }
        
        return $cookies;
    }
}

// To work around PHP limitations,
// immediately call init method to set some
// dynamic defaults. This is because default
// value of a static variable is compile time
// so static.

CoffeeRequest::_init();