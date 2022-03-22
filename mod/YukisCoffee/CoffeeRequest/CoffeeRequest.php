<?php
namespace YukisCoffee\CoffeeRequest;

class CoffeeRequest
{
    // Version 2.0 change:
    // allow instantiation!
    // "Static method" calls now redirect to
    // a static variable set to myself.
    public static $_self;

    public static function __callStatic($name, $args)
    {
        return self::$_self->{$name}(...$args);
    }

    public static function _resetStatic()
    {
        self::$_self = new static();
    }

    public function __call($name, $args)
    {
        // Workaround since methods must be invisible
        // statically (protected or private) for the
        // hack to work.
        return $this->{$name}(...$args);
    }


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

    public function __construct()
    {
        $this->defaultHeaders += ["Cookie" => self::genCookieHeader()];
    }

    protected function request($url, $options = [])
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

    protected function queueRequest($url, $options = [], $id = null)
    {
        $this->addDefaultOptions($options);

        // Init curl
        $ch = curl_init($url);
        curl_setopt_array($ch, self::reqOpts2Curl($options));

        // Add to request queue
        if (is_null($id)) $id = count($this->requestQueue) - 1;
        $this->requestQueue += [$id => $ch];
    }

    protected function runQueue()
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

    protected function clearRequestQueue()
    {
        $this->requestQueue = [];
    }

    protected function addDefaultOptions(&$options)
    {
        // PHP array addition for assocarrays ignores
        // value, meaning only unset keys are added.
        // So it's just one line:

        $options += $this->defaultOptions;
        if (isset($options["headers"])) $options["headers"] += $this->defaultHeaders;
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

CoffeeRequest::$_self = new CoffeeRequest();