<?php
namespace Rehike\SignInV2\Builder;

use Rehike\SignInV2\Info\SessionInfo;

/**
 * Builds a session info object using data that is collected elsewhere.
 * 
 * The builder is not a public interface, so it can be considered less volatile
 * than the final class. Temporary state can be stored here.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class SessionInfoBuilder implements IBuilder
{
    public bool $isSignedIn = false;
    
    public BuilderCollection $googleAccounts;
    
    public ?YtChannelAccountInfoBuilder $activeChannelBuilder = null;
    
    //public BuilderCollection $otherAccountBuilders;
    
    public string $datasyncId;
    
    public int $sessionErrors = 0;
    
    public function __construct()
    {
        $this->googleAccounts = new BuilderCollection(GoogleAccountInfoBuilder::class);
        //$this->otherAccountBuilders = new BuilderCollection(YtChannelAccountInfoBuilder::class);
    }

    public function build(): SessionInfo
    {
        return new SessionInfo($this);
    }

    public function pushSessionError(int $error): void
    {
        $this->sessionErrors |= $error;
    }
}