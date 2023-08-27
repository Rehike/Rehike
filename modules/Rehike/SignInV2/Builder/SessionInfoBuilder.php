<?php
namespace Rehike\SignInV2\Builder;

use Rehike\SignInV2\Info\SessionInfo;
use Rehike\SignInV2\Exception\BuilderException;

/**
 * Builds a session info object using data that is collected elsewhere.
 * 
 * The builder is not a public interface, so it can be considered less volatile
 * than the final class. Temporary state can be stored here.
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class SessionInfoBuilder
{
    private array $googleAccounts = [];
    
    private int $sessionErrors;

    public function build(): SessionInfo
    {
        $googleAccounts = $this->recursiveBuildGoogleAccounts();

        return new SessionInfo(
            isSignedIn: false
        );
    }

    public function pushSessionError(int $error): void
    {
        $this->sessionErrors |= $error;
    }

    public function insertGoogleAccount(): GoogleAccountInfoBuilder
    {
        $instance = new GoogleAccountInfoBuilder($this);
        $this->googleAccounts[] = $instance;
        return $instance;
    }

    private function recursiveBuildGoogleAccounts(): array
    {
        $result = [];

        foreach ($this->googleAccounts as $i => $acc)
        {
            try
            {
                $result[] = $acc->build();
            }
            catch (BuilderException $e)
            {
                // TODO (kirasicecreamm): Better global error handling?
                trigger_error(
                    "Failed to build information class for Google Account at " .
                    "index $i.",
                    E_USER_WARNING
                );
            }
        }

        return $result;
    }
}