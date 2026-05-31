<?php
declare(strict_types=1);
namespace Rehike\SignInV2\CacheNew;

/**
 * Interface for the sign in cache format.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
final class SigninCache
{
    public int $version;
    public int $expire;
    public string $sessionId;
    public object $switcherResponse;
    public ?string $currentUcid;
    
    /**
     * @internal
     */
    public function __construct(
        int $version,
        int $expire,
        string $sessionId,
        object $switcherResponse,
        ?string $currentUcid,
    )
    {
        $this->version = $version;
        $this->expire = $expire;
        $this->sessionId = $sessionId;
        $this->switcherResponse = $switcherResponse;
        $this->currentUcid = $currentUcid;
    }
}