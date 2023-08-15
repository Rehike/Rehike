<?php
namespace Rehike\SignInV2;

use Rehike\SignInV2\{
    Builder\SessionInfoBuilder,
    Enum\SessionErrors,
    Exception\FailedSwitcherRequestException,
    Info\SessionInfo,
    Parser\SwitcherParser
};

use Rehike\Network;

use Rehike\Async\Promise;
use function Rehike\Async\async;

/**
 * Manages Google Account (GAIA) authentication and initialization.
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class GaiaAuthManager
{
    /**
     * Stores the user's "SAPISID" session cookie, which plays a crucial role
     * in authentication.
     * 
     * If the cookie is unavailable, then this will be unset.
     */
    private static string $sapisid;
    
    private static string $loginInfoCookie;

    public static function __initStatic(): void
    {
        if (isset($_COOKIE) && isset($_COOKIE["SAPISID"]) && isset($_COOKIE["LOGIN_INFO"]))
        {
            self::$sapisid = $_COOKIE["SAPISID"];
            self::$loginInfoCookie = $_COOKIE["LOGIN_INFO"];
        }
    }

    /**
     * Checks if GAIA authentication should be attempted.
     * 
     * If this function returns false, all GAIA-related behavior should be
     * ignored.
     */
    public static function shouldAttemptAuth(): bool
    {
        return isset(self::$sapisid);
    }

    /**
     * Generates a new SAPISIDHASH for the current timestamp, which is used in
     * the HTTP Authorization header.
     */
    public static function generateSapisidHash(string $origin = "https://www.youtube.com"): string
    {
        $sapisid = self::$sapisid;

        $time = time();
        $sha1 = sha1("{$time} {$sapisid} {$origin}");

        return "SAPISIDHASH {$time}_{$sha1}";
    }

    /**
     * Newly requests session data from YouTube's servers.
     * 
     * For performance reasons, the result will be cached and reused for some
     * time after the initial request.
     * 
     * @return Promise<SessionInfo>
     */
    public static function getFreshInfoFromRemote(): Promise/*<SessionInfo>*/
    {
        return async(function() {
            $infoBuilder = new SessionInfoBuilder();

            try
            {
                $accSwitcher = yield self::requestAccountSwitcherData();

                $switcherParser = new SwitcherParser($accSwitcher);
                $switcherParser->outputToBuilder($infoBuilder);

                return $infoBuilder->build();
            }
            catch (FailedSwitcherRequestException $e)
            {
                $infoBuilder->pushSessionError(SessionErrors::FAILED_REQUEST);
                return $infoBuilder->build();
            }
        });
    }

    /**
     * Requests account switcher data from the server for parsing.'
     * 
     * As this is not a standard InnerTube endpoint, it features a JSON buster
     * that must be removed or it will break the JSON parser.
     * 
     * @return Promise<object>
     */
    private static function requestAccountSwitcherData(): Promise/*<object>*/
    {
        return async(function() {
            $response = yield Network::urlRequestFirstParty(
                "https://www.youtube.com/getAccountSwitcherEndpoint",
                Network::getDefaultYoutubeOpts()
            );

            $object = json_decode(
                substr($response->getText(), 4)
            );

            if ($response->status != 200 || !is_object($object))
            {
                throw new FailedSwitcherRequestException(
                    "Failed to request account switcher data."
                );
            }

            return $object;
        });
    }
}