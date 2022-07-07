<?php
namespace Rehike\Controller\Special;

return new class {
    public function get(&$yt, &$template, $request)
    {
        /**
         * Handle HTTP headers
         */
        $header = [];

        // Push header array
        $header[] = "Host: www.youtube.com";

        // Push the rest of the headers
        foreach (getallheaders() as $name => $value)
        {
            if (!in_array($name, ["Host", "Accept-Encoding", "Cookie"], true)) 
                $header[] = "{$name}: {$value}";

            // Light theme hack
            if ("Cookie" == $name)
            {
                if (preg_match("/f6=[0-9]+/", $value))
                {
                    $header["Cookie"] = preg_replace("/f6=[0-9]+/", "f6=40080000", $value);
                }
                else if (preg_match("/PREF=/", $value))
                {
                    $header["Cookie"] = preg_replace("/PREF=/", "PREF=f6=40080000&", $value);
                }
                else
                {
                    $header["Cookie"] = $value . "; PREF=f6=40080000";
                }
            }
        }

        if (!isset($header["Cookie"]))
        {
            $header["Cookie"] = "PREF=f6=40080000";
        }

        /**
         * cURL array declaration
         */
        $curlParams = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_ENCODING => "" // Automatically determine
        ];

        /**
         * Perform request
         */
        $ch = curl_init("https://www.youtube.com" . $_SERVER["REQUEST_URI"]);
        curl_setopt_array($ch, $curlParams);
        $responseBody = curl_exec($ch);

        curl_close($ch);

        echo $responseBody;
    }
};