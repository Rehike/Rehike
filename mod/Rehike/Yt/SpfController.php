<?php
namespace Rehike\Yt;

use \Rehike\Yt;
use \Rehike\Yt\PlayerController;
use \SpfPhp\SpfPhp;

class SpfController
{
    public static $usingSpf = false;
    public static $spfIdListeners = [];
    public static $spfState = "";
    public static $spfUrl;
    public static $spfName;
    public static $spfResponse;

    const SPF_NAV = 'navigate';
    const SPF_NB = 'navigate-back';
    const SPF_NF = 'navigate-forward';
    const SPF_LOAD = 'load';

    public static function setSpfName($name)
    {
        self::$spfName = $name;
    }

    public static function isNavigateState($state) 
    {
        return 
            (
                self::SPF_NAV == $state ||
                self::SPF_NB == $state ||
                self::SPF_NF == $state
            );
    }

    public static function shouldUseSpf()
    {
        if (isset($_GET["spf"]))
        {
            return self::isNavigateState($_GET["spf"]) || 
                self::SPF_LOAD == $_GET["spf"];
        }
    }

    public static function registerIdListeners($listeners)
    {
        self::$spfIdListeners = $listeners;
    }

    public static function initUseSpf()
    {
        self::$usingSpf = true;
        self::$spfState = $_GET["spf"];
        self::$spfUrl = preg_replace('/.spf='.$_GET['spf'].'/', '', $_SERVER['REQUEST_URI']);
    }

    public static function renderSpf($htmlBuffer) {
        Yt::useJSON();

        $spfResponse = @SpfPhp::build(
            $htmlBuffer,
            self::$spfIdListeners,
            (object) [
                'skipSerialisation' => true
            ]
        );

        if (isset(self::$spfUrl)) $spfResponse->url = self::$spfUrl;
        if (isset(self::$spfName)) $spfResponse->name = self::$spfName;

        if (PlayerController::hasPlayerResponse())
        {
            $spfResponse->data = 
                (object) ['swfcfg' => 
                    (object) ['args' => 
                        (object) [
                            'raw_player_response' => null,
                            'raw_watch_next_response' => null
                        ]
                    ]
                ];
            $spfResponse->data->swfcfg->args->raw_player_response = PlayerController::$playerResponse;
            $spfResponse->data->swfcfg->args->raw_watch_next_response = json_decode(PlayerController::$rawWatchNextResponse);
        }

        return json_encode($spfResponse);
    }
}