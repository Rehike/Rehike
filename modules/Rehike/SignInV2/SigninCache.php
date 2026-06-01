<?php
declare(strict_types=1);
namespace Rehike\SignInV2;

/**
 * Interface for the sign in cache format.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
final class SigninCache
{
    /**
     * @internal
     */
    public function __construct(
        public int $version,
        public int $expire,
        public string $sessionId,
        public object $switcherResponse,
        public ?string $currentUcid,
    )
    {
    }
}