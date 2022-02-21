<?php
namespace Rehike\Yt;

use \PlayerCore as PlayerCore;

class PlayerController
{
    // Previous structure used two variables
    // $playerCore: PlayerCore::main() response
    // $playerBasepos: $playerCore->basepos

    public static $playerCore;
    public static $playerResponse;
    public static $rawWatchNextResponse;

    public static function init()
    {
        self::$playerCore = PlayerCore::main();
    }

    public static function getPlayer()
    {
        return self::$playerCore;
    }

    public static function hasPlayerResponse()
    {
        return isset(self::$playerResponse);
    }
}