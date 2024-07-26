<?php
namespace Rehike\Player;

require_once "Constants.php";

use Rehike\Player\{
    Exception\CacherException,
    Exception\UpdaterException
};

/**
 * A portable library for retrieving and using the YouTube player
 * in PHP.
 * 
 * PlayerCore makes getting the YouTube player and using it easy!
 * 
 * However, it only serves as a wrapper for getting the player
 * application itself. You still need all that fun frontend configuration
 * (luckily, if you're making a custom YouTube frontend like Rehike, that's
 * already covered!)
 * 
 * This also can't easily be used for older versions of the YouTube
 * player. That is, any old version at all. YouTube has security
 * mechanisms in place to limit access to their streams, including
 * signatures and encryption. PlayerCore currently cannot extract
 * the decryption algorithm from the player JS, so the use of older
 * players is limited.
 * 
 * Still, this serves as a vital component of the Rehike project and it's
 * made open to the public for anyone to use, whether that be for similar
 * or completely different purposes.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 * 
 * @license Mozilla Public License 2.0
 * @version 3.2
 */
class PlayerCore extends Configurable
{
    static string $playerJsRegex = "#/s/player/[a-zA-Z0-9/\-_.]*base.js#";
    static string $playerCssRegex = "#/s/player/[a-zA-Z0-9/\-_.]*(www-player|www-player-webp|player-rtl).css#";
    static string $embedJsRegex = "#/s/player/[a-zA-Z0-9/\-_.]*www-embed-player.js#";
    static string $stsRegex = "/signatureTimestamp:?\s*([0-9]*)/";

    static string $cacheDestDir = "cache";
    static string $cacheDestName = "player_cache"; // .json
    static int $cacheMaxTime = 18000; // 5 hours in seconds

    /**
     * Set configuration from an array.
     * 
     * The format is a simple associative array with
     * key => value pairs representing the static properties
     * of this class.
     * 
     * The base Configurable behaviour will handle parsing this
     * data and updating the static properties to be this way,
     * otherwise they are handled as constants by the PHP
     * compiler.
     * 
     * @param string[] $array
     * @return void
     */
    public static function configure(array $array): void
    {
        self::configFromArray($array);
    }

    /**
     * The main function: get the necessary player information!
     * 
     * @return PlayerInfo
     */
    public static function getInfo(): PlayerInfo
    {
        // Try getting information from the cacher:
        try
        {
            return PlayerInfo::from(Cacher::get());
        }
        catch (CacherException $e)
        {
            /*
             * If the cache is unavailable (doesn't exist,
             * expired, etc.) then request the data from
             * YouTube's servers.
             *
             * This function does also throw an exception, but
             * it should be treated as fatal since there's nothing
             * else to do here.
             */
            $remoteInfo = PlayerUpdater::requestPlayerInfo();

            // Attempt to write this to cache:
            // (and if it fails, do nothing)
            try
            {
                Cacher::write($remoteInfo);
            }
            catch (CacherException $e) {}

            // If everything went right, then return the remote info:
            return PlayerInfo::from($remoteInfo);
        }
    }
}