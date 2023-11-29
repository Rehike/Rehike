<?php
namespace Rehike;

use Rehike\Exception\Network\InnertubeFailedRequestException;
use Rehike\Signin\AuthManager;

use YukisCoffee\CoffeeRequest\CoffeeRequest;
use YukisCoffee\CoffeeRequest\Promise;
use YukisCoffee\CoffeeRequest\Network\Request;
use YukisCoffee\CoffeeRequest\Network\Response;

/**
 * Implements a network manager for Rehike.
 * 
 * @version 2.0
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Network
{
    protected const INNERTUBE_API_HOST = "https://www.youtube.com";
    protected const INNERTUBE_API_KEY = "AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8";
    
    protected const V3_API_HOST = "https://www.googleapis.com";
    protected const V3_API_KEY = "AIzaSyAa8yy0GdcGPHdtD083HiGGx_S0vMPScDM";

    protected const DNS_OVERRIDE_HOST = "1.1.1.1";

    /**
     * Contains headers meant to be sent only to InnerTube requests.
     * 
     * For example, authentication headers.
     * 
     * @var string[]
     */
    protected static array $innertubeHeaders = [];

    // Disable instances
    private function __construct() {}

    /**
     * Make a generic URL request.
     * 
     * @return Promise<Response>
     */
    public static function urlRequest(string $url, array $opts = []): Promise/*<Response>*/
    {
        return CoffeeRequest::request($url, $opts);
    }

    /**
     * Make a first-party (youtube.com) URL request.
     * 
     * Unlike the standard function, this will forward the user's YouTube authentication data.
     * Do not use this function for foreign requests.
     * 
     * This is used occassionally for non-InnerTube APIs that still need access to the user's
     * account, i.e. /getAccountSwitcherEndpoint.
     * 
     * @return Promise<Response>
     */
    public static function urlRequestFirstParty(string $url, array $opts = []): Promise/*<Response>*/
    {
        if (isset($opts["headers"]))
        {
            $opts["headers"] += self::$innertubeHeaders;
        }
        else
        {
            $opts["headers"] = self::$innertubeHeaders;
        }

        return self::urlRequest($url, $opts);
    }

    /**
     * Make a InnerTube request.
     * 
     * @return Promise<Response>
     */
    public static function innertubeRequest(
        string $action, 
        array $body = [],
        string $clientName = "WEB", 
        string $clientVersion = "2.20230331.00.00",
        bool $ignoreErrors = false,
        bool $useAuthentication = true
    ): Promise/*<Response>*/
    {
        $host = self::INNERTUBE_API_HOST;
        $key  = self::INNERTUBE_API_KEY;

        // Fucking cursed
        $body = (object)($body + (array)InnertubeContext::generate(
            $clientName, $clientVersion
        ));

        $requestHeaders = [
            "Content-Type" => "application/json",
            "X-Goog-Visitor-Id" => InnertubeContext::genVisitorData(ContextManager::$visitorData)
        ];

        if ($useAuthentication)
        {
            $requestHeaders += self::$innertubeHeaders;
        }

        return new Promise(function ($resolve, $reject)
            use ($action, 
                 $body, 
                 $clientName, 
                 $clientVersion,
                 $host,
                 $key,
                 $ignoreErrors,
                 $requestHeaders)
        {
            CoffeeRequest::request(
                "{$host}/youtubei/v1/{$action}?key={$key}",
                [
                    "headers" => $requestHeaders,
                    "method" => "POST",
                    "body" => json_encode($body),
                    "onError" => "ignore",
                    "dnsOverride" => self::DNS_OVERRIDE_HOST
                ]
            )->then(function ($response) use ($resolve, $reject, $ignoreErrors) {
                if ( (200 == $response->status) || (true == $ignoreErrors) )
                {
                    $resolve($response);
                }
                else
                {
                    $reject(new InnertubeFailedRequestException(
                        $response
                    ));
                }
            });
        });
    }

    /**
     * Make a fake InnerTube request. This is used for developer testing purposes.
     * 
     * This will return data from a local JSON file as if it's a true InnerTube request. This
     * allows Rehike developers to test InnerTube dumps for debugging purposes.
     * 
     * The signature is kept about the same as innertubeRequest, so that it's easy to swap things
     * out during development purposes.
     * 
     * In addition, if $localFilePath is "error", then an InnerTube error will be forced. This
     * may be used to test error handling.
     * 
     * @return Promise<Response>
     */
    public static function innertubeRequestFake(
        string $localFilePath,
        string $action, 
        array $body = [],
        string $clientName = "WEB", 
        string $clientVersion = "2.20230331.00.00",
        bool $ignoreErrors = false,
        bool $useAuthentication = true
    ): Promise/*<Response>*/
    {
        return new Promise(function ($resolve, $reject)
            use ($action, 
                 $body, 
                 $clientName, 
                 $clientVersion,
                 $ignoreErrors,
                 $localFilePath)
        {
            $fakeRequestInstance = new class extends Request {
                final public function __construct() {}
            };

            if ($localFilePath == "error")
            {
                $reject(new InnertubeFailedRequestException(
                    new Response(
                        $fakeRequestInstance,
                        400,
                        "fake innertube error",
                        []
                    )
                ));
                return;
            }

            $fileContents = FileSystem::getFileContents($localFilePath);

            $resolve(new Response(
                $fakeRequestInstance,
                200,
                $fileContents,
                []
            ));
        });
    }

    /**
     * Request the public YouTube Data API v3.
     * 
     * This uses a unique key, and as such, doesn't have any limitations.
     * 
     * @author Aubrey Pankow <aubyomori@gmail.com>
     * @return Promise<Response>
     */
    public static function dataApiRequest(
        string $action, 
        array $params, 
        bool $post = false
    ): Promise/*<Response>*/
    {
        $host = self::V3_API_HOST;
        $key = self::V3_API_KEY;

        $urlParams = "";

        if (!$post) {
            foreach($params as $name => $value) {
                $urlParams .= "&{$name}={$value}";
            }
        }

        $headers = [
            "X-Origin" => "https://explorer.apis.google.com",
            "X-Referer" => "https://explorer.apis.google.com",
            "X-Requested-With" => "XMLHttpRequest",
            "X-Client-Data" => "CKe1yQEIkrbJAQiktskBCKmdygEIoO/KAQiSocsBCIWgzQEI+LHNAQjatM0BCNy9zQEIu77NAQj+v80BCOfBzQEIssPNAQjuxM0BCI3FzQEIwcXNARiNp80B",
            "X-Clientdetails" => "appVersion=5.0%20(Windows%20NT%2010.0%3B%20Win64%3B%20x64)%20AppleWebKit%2F537.36%20(KHTML%2C%20like%20Gecko)%20Chrome%2F115.0.0.0%20Safari%2F537.36&platform=Win32&userAgent=Mozilla%2F5.0%20(Windows%20NT%2010.0%3B%20Win64%3B%20x64)%20AppleWebKit%2F537.36%20(KHTML%2C%20like%20Gecko)%20Chrome%2F115.0.0.0%20Safari%2F537.36",
            "X-Javascript-User-Agent" => "apix/3.0.0 google-api-javascript-client/1.1.0",
            "Referer" => "https://content-youtube.googleapis.com/static/proxy.html?usegapi=1&jsh=m%3B%2F_%2Fscs%2Fabc-static%2F_%2Fjs%2Fk%3Dgapi.lb.en.5o5-TAFr18s.O%2Fd%3D1%2Frs%3DAHpOoo_qgszOsFrBH7bZ1Rmfwa9Mc03wLQ%2Fm%3D__features__",
            "Accept" => "application/json",
            "User-Agent" => $_SERVER["HTTP_USER_AGENT"] 
                ?? "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0"
        ];

        if ($post) {
            $headers += [
                "Content-Type" => "application/json"
            ];
        }

        $body = [
            "headers" => $headers
        ];

        if ($post) {
            $body += [
                "body" => $params,
                "method" => "POST"
            ];
        } else {
            $body += [
                "method" => "GET"
            ];
        }

        return CoffeeRequest::request(
            "{$host}/youtube/v3/{$action}?key={$key}{$urlParams}",
            $body
        );
    }

    /**
     * Get the default options for any non-InnerTube YouTube request.
     * 
     * These are merged with other user options to provide a desired
     * outcome.
     */
    public static function getDefaultYoutubeOpts(): array
    {
        return [
            "dnsOverride" => self::DNS_OVERRIDE_HOST,
            "headers" => [
                "Cookie" => self::getCurrentRequestCookie()
            ]
        ];
    }

    /**
     * Run all requests made.
     */
    public static function run(): void
    {
        CoffeeRequest::run();
    }

    /**
     * Called by the auth service in order to request the network
     * manager use it.
     * 
     * @internal
     */
    public static function useAuthService(): void
    {
        if (AuthManager::shouldAuth())
        {
            self::$innertubeHeaders += [
                "Authorization" => AuthManager::getAuthHeader(),
                "Origin" => "https://www.youtube.com",
                "Host" => "www.youtube.com",
                "Cookie" => self::getCurrentRequestCookie()
            ];
        }
    }

    /**
     * Called by the auth service requesting the network manager to use
     * its GAIA ID.
     * 
     * @internal
     */
    public static function useAuthGaiaId(): void
    {
        $gaiaId = AuthManager::getGaiaId();
            
        /*
         * No GAIA ID is reported for channels associated with the Google
         * account itself. Only brand accounts must account for the distinction.
         */
        if ("" != $gaiaId)
        {
            self::$innertubeHeaders += [
                /*
                 * TODO(dcooper): Invalid AuthUser use.
                 * 
                 * AuthUser is used to switch between Google accounts (i.e.
                 * Gmail addresses themselves) and should not be hardcoded as
                 * zero as this will result in the wrong account being used by
                 * Rehike.
                 */
                "X-Goog-AuthUser" => "0",
                "X-Goog-PageId" => $gaiaId
            ];
        }
    }

    /**
     * Convert the PHP cookie array to a HTTP header string.
     */
    protected static function getCurrentRequestCookie(): string
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