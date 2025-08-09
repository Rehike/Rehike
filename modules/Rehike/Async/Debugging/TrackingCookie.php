<?php
namespace Rehike\Async\Debugging;

use Exception;
use Stringable;
use Rehike\Logging\ExceptionLogger;

/**
 * Simple tracking cookie for debugging purposes.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class TrackingCookie implements Stringable
{
    private string $category;
    private int $uid;
    private string $creationTrace;
    
    private static array $s_categoryTracker = [];
    
    /**
     * Create a tracking cookie with a category (i.e. __CLASS__).
     */
    public function __construct(string $category)
    {
        $this->category = $category;
        
        $creationException = new Exception();
        
        if (class_exists(ExceptionLogger::class))
        {
            $this->creationTrace = ExceptionLogger::getFormattedException($creationException)
                ->getRawText();
        }
        else
        {
            // Rehike ExceptionLogger unavailable.
            $this->creationTrace = $creationException->getTraceAsString();
        }
        
        if (isset(self::$s_categoryTracker[$category]))
        {
            self::$s_categoryTracker[$category] += 1;
        }
        else
        {
            self::$s_categoryTracker[$category] = 0;
        }
        
        $this->uid = self::$s_categoryTracker[$category];
    }
    
    public function __toString(): string
    {
        return md5("$this->category-$this->uid");
    }
    
    public function getCreationTrace(): string
    {
        return $this->creationTrace;
    }
}