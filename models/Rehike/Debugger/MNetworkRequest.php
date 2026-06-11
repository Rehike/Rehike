<?php
declare(strict_types=1);
namespace Rehike\Model\Rehike\Debugger;

use Rehike\Debugger\LoggedRequestContext;

/**
 * Client side structure for network request debugging.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MNetworkRequest
{
    /**
     * The URL of the request.
     */
    public string $url;
    
    /**
     * The HTTP request method.
     */
    public string $method;
    
    /**
     * The time at which the request started.
     */
    public float $startTimeMs;
    
    /**
     * The time at which the request finished, or the duration for which the
     * server processed the request if the request was left unfinished.
     */
    public float $endTimeMs;
    
    /**
     * True if the request finished.
     */
    public bool $finished = false;
    
    /**
     * Map of request header names (normalised to all lowercase letters with hyphens)
     * to their values.
     * 
     * @var array<string, string>
     */
    public array $headers = [];
    
    /**
     * The POST body if specified.
     */
    public ?string $body = null;
    
    /**
     * Rehike NetworkCore result code.
     */
    public ?int $networkCoreResultCode = null;
    
    /**
     * The HTTP status of the response, if available.
     */
    public ?int $responseStatus = null;
    
    /**
     * Map of response header names (normalised to all lowercase letters with hyphens)
     * to their values.
     * 
     * @var array<string, string>
     */
    public array $responseHeaders = [];
    
    /**
     * The textual content of the response, if available.
     */
    public ?string $responseContent = null;
    
    public function __construct(LoggedRequestContext $context)
    {
        $this->url = $context->url;
        $this->method = $context->method;
        $this->startTimeMs = $context->startTimeUs * 1000;
        $this->endTimeMs = $context->endTimeUs * 1000;
        $this->finished = $context->hasFinished;
        
        if ($context->request)
        {
            $this->headers = self::normalizeHeaders($context->request->headers);
            
            // TODO(isabella): For some reason, the body is not a nullable property, but it
            // can remain unset. This is dangerous. Leymonaide plans to change this, but I
            // will wait for her to make that change rather than touch it myself.
            if (isset($context->request->body))
            {
                $this->body = $context->request->body;
            }
        }
        
        if ($context->response)
        {
            $this->networkCoreResultCode = (int)$context->response->resultCode; // int cast to shut up IDE...
            $this->responseStatus = $context->response->status;
            $this->responseHeaders = self::normalizeHeaders($context->response->headers);
            $this->responseContent = $context->response->getText();
        }
    }
    
    /**
     * Normalises HTTP headers into a common format for the sake of the client.
     * 
     * All header names are normalised to a lowercase encoding using hyphens to
     * separate words.
     */
    private static function normalizeHeaders(iterable $headers): array
    {
        $result = [];
        
        foreach ($headers as $name => $value)
        {
            $result[strtolower($name)] = $value;
        }
        
        return $result;
    }
}