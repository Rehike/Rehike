<?php
namespace Rehike\Network\Handler;

use Rehike\Attributes\Override;

use Rehike\Network\{
    Enum\NetworkResult,
    Handler\NetworkHandler,
    Handler\Curl\EventLoopRunner,
    Handler\Curl\RequestTransformer,
    Handler\Curl\RequestWrapper,
    Internal\Request,
    Internal\Response,
    IRequest,
    IResponse,
};

/**
 * Implements a cURL-compatible network handler for the network library.
 * 
 * The cURL handler uses curl_multi integration with PHP in order to
 * perform a network request. This already operates asynchronously, but
 * most PHP-land uses are blocking.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class CurlHandler extends NetworkHandler
{
    // This must be implemented through a hack as it has conditional
    // behaviour depending on the PHP version.
    use EventLoopRunner;

    /** 
     * Stores all active requests.
     * 
     * @var RequestWrapper[] 
     */
    private array $requests = [];

    #[Override]
    public function addRequest(IRequest $request): void
    {
        $this->requests[] = $this->convertRequest($request);
    }

    #[Override]
    public function clearRequests(): void
    {
        $this->requests = [];
    }

    /**
     * Convert a Request to a cURL handle.
     */
    protected function convertRequest(IRequest $request): RequestWrapper
    {
        return RequestTransformer::convert($request);
    }

    /**
     * Convert a cURL response to our own Response object.
     */
    protected function makeResponse(
            int $curlCode,
            int $status,
            string $raw,
            RequestWrapper $wrapper,
    ): IResponse
    {
        $result = new Response(
            $wrapper->instance,
            $status,
            $raw,
            $wrapper->responseHeaders
        );
        $result->resultCode = $this->makeResultCode($curlCode);
        return $result;
    }

    /**
     * Convert a cURL status code to a NetworkResult code.
     */
    protected function makeResultCode(int $curlCode): int
    {
        switch ($curlCode)
        {
            case 0: // CURLE_OK
                return NetworkResult::SUCCESS;
            case 3: // CURL_URL_MALFORMAT
                return NetworkResult::E_MALFORMED_URL;
            case 5: // CURL_COULDNT_RESOLVE_PROXY
                return NetworkResult::E_COULDNT_RESOLVE_PROXY;
            case 6: // CURL_COULDNT_RESOLVE_HOST
                return NetworkResult::E_COULDNT_RESOLVE_HOST;
            case 7: // CURL_COULDNT_CONNECT
                return NetworkResult::E_COULDNT_CONNECT;
        }

        return NetworkResult::E_FAILED;
    }

    /**
     * Resolve a Request with a Response.
     */
    protected function sendResponse(Request $request, IResponse $response): void
    {
        $request->resolve($response);
    }
}