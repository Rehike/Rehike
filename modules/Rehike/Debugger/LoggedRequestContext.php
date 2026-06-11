<?php
declare(strict_types=1);
namespace Rehike\Debugger;

use Rehike\Network\Internal\Request;
use Rehike\Network\Internal\Response;

/**
 * Represents the context for a single logged request.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class LoggedRequestContext
{
    /**
     * A reference to the request.
     * 
     * This is only set if the condensed debugger is not used, otherwise the
     * request is allowed to be garbage collected to avoid using too much
     * memory, and so a reference will not be held.
     */
    public ?Request $request;
    
    /**
     * A reference to the response, if it is available.
     * 
     * This is only set if the condensed debugger is not used, otherwise the
     * request is allowed to be garbage collected to avoid using too much
     * memory, and so a reference will not be held.
     */
    public ?Response $response;
    
    /**
     * The expected type of the logged response.
     * 
     * @var key-of<LoggedRequestType>
     */
    public int $type = LoggedRequestType::Unknown;
    
    /**
     * The URL of the request.
     */
    public string $url;
    
    /**
     * The request method used for the request.
     */
    public string $method;
    
    /**
     * The start time of the request in microseconds.
     */
    public float $startTimeUs;
    
    /**
     * The end time of the request in microseconds.
     */
    public float $endTimeUs;
    
    /**
     * Flag denoting if the request has finished or not.
     */
    public bool $hasFinished = false;
    
    /**
     * The InnerTube client name, if this is an InnerTube request context.
     */
    public ?string $clientName = null;
    
    /**
     * The InnerTube client version, if this is an InnerTube request context.
     */
    public ?string $clientVersion = null;
    
    /**
     * Whether or not to ignore errors, if this is an InnerTube request context.
     */
    public ?bool $ignoreErrors = null;
    
    /**
     * Whether or not the use of authentication is desired, if this is an InnerTube request
     * context.
     */
    public ?bool $useAuthentication = null;
    
    public function __construct(Request $request)
    {
        $this->url = $request->url;
        $this->method = $request->method;
        $this->startTimeUs = $request->startTime;
        $this->endTimeUs = $request->completionTime ?? 0;
        
        if (!Debugger::isCondensed())
        {
            $this->request = $request;
        }
    }
    
    /**
     * Finalises the information as the response is received.
     * 
     * This takes the request as a parameter as the property may not exist,
     * and we want to be able to receive this information at any time.
     */
    public function finalize(Request $request, Response $response): void
    {
        $this->hasFinished = true;
        $this->endTimeUs = $request->completionTime;
        
        if (!Debugger::isCondensed())
        {
            $this->response = $response;
        }
    }
}