<?php
namespace YukisCoffee\CoffeeRequest;

/**
 * A simple requests (cURL) wrapper.
 * 
 * As of version 2.0, this can be used in an instance. As such, the base
 * CoffeeRequest class is reserved for a wrapper of this to allow for both
 * instantiated and static calls.
 * 
 * It can be moved back to its original name when static support is deprecated.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @version 2.0
 */
class CoffeeRequestInstance
{
    public static $resolve = [];

    // Property declarations
    public $requestsMaxAttempts = 50;
    public $defaultOptions =
        [
            "post" => false,
            "returnTransfer" => true,
            "encoding" => "gzip",
            "headers" => [] // ** Set by $defaultHeaders
        ];
    public $defaultHeaders = [];
    public $requestQueue = [];

    public function __construct($key = "")
    {
        if ("do not access this directly!" != $key)
            trigger_error("CoffeeRequestInstance should not be constructed directly. Use \"new CoffeeRequest()\" instead.", E_USER_ERROR);

        // Required behaviour
        $this->defaultHeaders += ["Cookie" => self::genCookieHeader()];
    }

    /**
     * Perform a single request.
     * 
     * @param string $url to request
     * @param string[] $options to use
     * @return mixed response
     */
    public function request($url, $options = [])
    {
        $this->addDefaultOptions($options);
        
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
        while (200 !== curl_getinfo($ch, \CURLINFO_HTTP_CODE) && $attempts < $this->requestsMaxAttempts);

        curl_close($ch);

        if (isset($response) && false !== $response)
        {
            return $response;
        }
    }

    /**
     * Add a request to the multi-request queue.
     * 
     * @param string $url to request
     * @param string[] $options to use
     * @param string|null $id (if null, will be a numeric ID)
     * @return void
     */
    public function queueRequest($url, $options = [], $id = null)
    {
        $this->addDefaultOptions($options);

        // Init curl
        $ch = curl_init($url);
        curl_setopt_array($ch, self::reqOpts2Curl($options));

        // Add to request queue
        if (is_null($id)) $id = count($this->requestQueue) - 1;
        $this->requestQueue += [$id => $ch];
    }

    /**
     * Run the request queue.
     * 
     * @return mixed[] responses
     */
    public function runQueue()
    {
        $mh = curl_multi_init();

        // Register all queued requests
        foreach ($this->requestQueue as $id => $handle)
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

        foreach ($this->requestQueue as $id => $handle)
        {
            $responses += [$id => curl_multi_getcontent($handle)];
            curl_multi_remove_handle($mh, $handle);
        }

        // Close curl and return
        curl_multi_close($mh);

        $this->clearRequestQueue();

        return $responses;
    }

    /**
     * Clear the request queue
     * 
     * @return void
     */
    public function clearRequestQueue()
    {
        $this->requestQueue = [];
    }

    /**
     * Add the default options to the options array.
     * 
     * @param string[] $options
     * @return void
     */
    protected function addDefaultOptions(&$options)
    {
        // PHP array addition for assocarrays ignores
        // value, meaning only unset keys are added.
        // So it's just one line:

        $options += $this->defaultOptions;

        // Also add headers
        if (isset($options["headers"])) 
            $options["headers"] += $this->defaultHeaders;
    }

    /**
     * Convert CoffeeRequest options to cURL-compatible ones.
     * 
     * @param string[] $options
     * @return string[]
     */
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

        $curlArr[\CURLOPT_RESOLVE] = self::$resolve;
        
        return $curlArr;
    }

    /**
     * Convert an associative array of CoffeeRequest headers to
     * cURL-compatible ones.
     * 
     * @param string[] $headers
     * @return string[]
     */
    public static function reqHeaders2Curl($headers)
    {
        $curlHeaders = [];
        
        foreach ($headers as $header => $value)
        {
            $curlHeaders[] = $header . ': '.  $value;
        }
        
        return $curlHeaders;
    }

    /**
     * Convert the PHP cookie array to a HTTP header string.
     * 
     * @return string
     */
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