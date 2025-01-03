<?php
namespace Rehike\Network\Internal;

use Rehike\Network\{
    IRequest,
    IResponse,
    NetworkCore,
};

use Rehike\Network\Enum\RedirectPolicy;
use Rehike\Network\Enum\RequestErrorPolicy;

use Rehike\Async\Deferred;

/**
 * Represents a network request.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Request implements IRequest
{
    use Deferred/*<Response>*/ { 
        getPromise as public;
        resolve as private resolvePromise;
    }

    /**
     * The request method to send.
     */
    public string $method = "GET";

    /**
     * The URL to request.
     */
    public string $url = "";

    /**
     * An associative array of headers.
     * 
     * @var string[]
     */
    public array $headers = [];

    /**
     * If specified, the POST body to be sent.
     */
    public string $body;

    /**
     * If specified, the preferred encoding to request with.
     * 
     * This option should be used instead of the Accept-Encoding header,
     * as it can better tell the request handler what to do.
     */
    public string $preferredEncoding;

    /** 
     * If specified, the redirect policy to use with the Request handler.
     * 
     * @var RedirectPolicy
     */
    public int $redirectPolicy = RedirectPolicy::FOLLOW;

    /**
     * If specified, sets the error policy to use.
     * 
     * If it's throw, then any request that isn't a 2xx status will
     * throw an exception. If it's ignore, then the response is treated
     * like normal.
     * 
     * @var RequestErrorPolicy
     */
    public int $onError = RequestErrorPolicy::THROW;

    /**
     * If specified, sets the user agent of the request.
     * 
     * If not specified, the user agent of the browser requesting the current
     * page will be used instead.
     */
    public string $userAgent = "";

    public function __construct(string $url, array $opts)
    {
        $this->initPromise();

        // Unset unused-by-default properties:
        unset($this->body);
        unset($this->preferredEncoding);

        $this->url = $url;
        $this->handleOptions($opts);
    }

    /**
     * Resolve the request with a Response.
     * 
     * @internal
     */
    public function resolve(Response $response): void
    {
        NetworkCore::reportFinishedRequest();
        $this->resolvePromise($response);
    }

    /**
     * Handle the array of options provided to construct the Request.
     */
    private function handleOptions(array $opts): void
    {
        foreach ($opts as $name => $value) switch ($name)
        {
            case "method":
                $this->method = $value;
                break;
            case "headers":
                $this->headers = $value;
                break;
            case "redirect":
                $this->redirectPolicy = self::handleRedirectOpt($value);
                break;
            case "body":
                $this->body = $value;
                break;
            case "preferredEncoding":
                $this->preferredEncoding = $value;
                break;
            case "onError":
                $this->onError = self::handleOnErrorOpt($value);
                break;
            case "userAgent":
                $this->userAgent = $value;
                break;
        }
    }

    /**
     * Handle the redirect option.
     * 
     * @param RedirectPolicy|string $value
     * 
     * @return RedirectPolicy
     */
    private function handleRedirectOpt($value): int
    {
        if (!is_string($value))
        {
            return $value;
        }

        switch (strtolower((string)$value))
        {
            case "follow":
                return RedirectPolicy::FOLLOW;
                break;
            case "manual":
                return RedirectPolicy::MANUAL;
                break;
        }
    }

    /**
     * Handle the redirect option.
     * 
     * @param RequestErrorPolicy|string $value
     * 
     * @return RedirectPolicy
     */
    private function handleOnErrorOpt($value): int
    {
        if (!is_string($value))
        {
            return $value;
        }

        switch (strtolower((string)$value))
        {
            case "throw":
                return RequestErrorPolicy::THROW;
                break;
            case "ignore":
                return RequestErrorPolicy::IGNORE;
                break;
        }
    }
}