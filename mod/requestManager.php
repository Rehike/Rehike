<?php

namespace RequestManager;

class RequestManager {
    public \CurlHandle $curlHandle;
    public \CurlMultiHandle $curlMh;

    public function curlSingleRequest($handle) {
        $ch = $this->curlHandle;
    }

    public function curlMultiRequest(array $handles): array {
        $mh = $this->curlMh;
        $rawResponses = [];
        $handlesCount = count($handles);

        for ($i = 0; $i < $handlesCount; $i++) {
            curl_multi_add_handle($mh, $handles[$i]);
        }

        do {
            $status = curl_multi_exec($mh, $active);
            if ($active) {
                curl_multi_select($mh);
            }
        } while ($active && $status == CURLM_OK);

        for ($i = 0; $i < $handlesCount; $i++) {
            $rawResponses[$i] = curl_multi_getcontent($handles[$i]);
            curl_multi_remove_handle($mh, $handles[$i]);
        }

        return $rawResponses;
    }

    public function request() {

    }

    public function resetCurl(): void {
        // in the event of a curl error, kill our handles and restart
        curl_close($this->curlHandle);
        curl_multi_close($this->curlMh);
        $this->curlHandle = curl_init();
        $this->curlMh = curl_multi_init();
    }

    public function __construct() {
        /*
         * Initialise cURL
         * 
         * For optimisation purposes, the same cURL handle will be reused
         * for all requests.
         */
        $this->curlHandle = curl_init();
        $this->curlMh = curl_multi_init();
    }
}

/**
 * Wrapper class for responses
 */
class Response {
    private $raw;
    
    public function raw() {
        return $this->raw;
    }

    public function json() {
        return json_decode($this->raw);
    }
}