<?php
namespace Rehike\SignInV2\Cache;

/**
 * Reads cache objects.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
final class CacheReader extends CacheReaderImpl
{
    /**
     * Creates a cache reader for the specified object.
     */
    public static function createReaderForObject(object $obj)
    {
        return new self($obj);
    }
}