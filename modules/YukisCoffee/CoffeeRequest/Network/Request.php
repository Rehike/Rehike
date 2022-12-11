<?php
namespace YukisCoffee\CoffeeRequest\Network;

use YukisCoffee\CoffeeRequest\CoffeeRequest;
use YukisCoffee\CoffeeRequest\Deferred;
use YukisCoffee\CoffeeRequest\Enum\RedirectPolicy;
use YukisCoffee\CoffeeRequest\Enum\RequestErrorPolicy;
use YukisCoffee\CoffeeRequest\Network\Response;
use YukisCoffee\CoffeeRequest\Util\Nameserver;
use YukisCoffee\CoffeeRequest\Util\NameserverInfo;

/**
 * Represents a network request.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class Request
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
        CoffeeRequest::reportFinishedRequest();
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