<?php
namespace Rehike\SignInV2\Builder;

use Rehike\SignInV2\Info\SessionInfo;

/**
 * Builds a session info object using data that is collected elsewhere.
 * 
 * The builder is not a public interface, so it can be considered less volatile
 * than the final class. Temporary state can be stored here.
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
class SessionInfoBuilder
{
    private string $displayName;
    private string $emailAddress;
    
    private int $sessionErrors;

    public function build(): SessionInfo
    {
        return new SessionInfo(
            isSignedIn: false
        );
    }

    public function pushSessionError(int $error): void
    {
        $this->sessionErrors |= $error;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName ?? null;
    }

    public function setDisplayName(string $newName): void
    {
        $this->displayName = $newName;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress ?? null;
    }

    public function setEmailAddress(string $newEmail): void
    {
        $this->emailAddress = $newEmail;
    }
}