<?php

namespace Rehike\ConfigManager;

use Rehike\Exception\AbstractException;

use Throwable;

/**
 * Exception thrown when the config file cannot be loaded.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class LoadConfigException extends AbstractException
{
    public const REASON_NONE = 0;
    public const REASON_COULD_NOT_OPEN_FILE_HANDLE = 1;
    public const REASON_PARSE_FAILURE = 2;
    
    protected int $failureReason = 0;
    
    /**
     * @param int $reason  One of the REASON_ constants of this class.
     */
    public function __construct(int $reason, string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->failureReason = $reason;
    }
    
    public function getReason(): int
    {
        return $this->failureReason;
    }
}