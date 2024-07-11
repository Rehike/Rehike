<?php
namespace Rehike\Helper;

use Rehike\Exception\Network\InnertubeFailedRequestException;
use Rehike\Network;
use YukisCoffee\CoffeeRequest\Promise;
use Rehike\Signin\API as SignIn;
use YukisCoffee\CoffeeRequest\Exception\GeneralException;

use function Rehike\Async\async;

/**
 * General utilties for channels.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ChannelUtils
{
    /**
     * Get a channel's UCID from an internal request URL.
     * 
     * @return Promise<?string>
     */
    public static function getUcid($request): Promise/*<?string>*/
    {
        return async(function() use (&$request) {
            if (in_array($request->path[0], ["channel", "user", "c"]))
            {
                switch($request->path[0])
                {
                    case "channel":
                        $ucid = $request->path[1] ?? "";
                        if (substr($ucid, 0, 2) == "UC")
                        {
                            return $ucid;
                        }
                        else
                        {
                            return "";
                        }
                        break;
                    case "user":
                    case "c":
                        return yield self::getUcidFromUrl(explode("?", $_SERVER["REQUEST_URI"])[0]);
                        break;
                }
            }

            return yield self::getUcidFromUrl(explode("?", $_SERVER["REQUEST_URI"])[0]);
        });
    }

    // If user is signed in and channel owner, get data for the
    // secondary channel header.
    public static function getOwnerData(string $ucid): ?object
    {
        return async(function() use ($ucid) {
            $ownerData = null;
            if (SignIn::isSignedIn())
            {
                $info = SignIn::getInfo();
                if (@$info["ucid"] == $ucid)
                {
                    $analytics = yield Network::innertubeRequest(
                        action: "analytics_data/get_screen",
                        body: [
                            "desktopState" => [
                                "tabId" => "ANALYTICS_TAB_ID_OVERVIEW"
                            ],
                            "fetchingType" => "FETCHING_TYPE_FOREGROUND",
                            "screenConfig" => [
                                "currency" => "USD", // Irrelevant, don't change this
                                "entity" => [
                                    "channelId" => $ucid
                                ],
                                "timePeriod" => [
                                    "timePeriodType" => "ANALYTICS_TIME_PERIOD_TYPE_LIFETIME"
                                ],
                                "timeZoneOffsetSecs" => -18000 // This shouldn't matter, so again, don't change
                            ]
                        ],
                        ignoreErrors: true
                    );

                    try
                    {
                        $adata = $analytics->getJson();
                    }
                    catch (GeneralException $e) 
                    {
                        return null;
                    }

                    if (isset($adata->cards))
                    {
                        $ownerData = (object) [];
                        foreach ($adata->cards as $card)
                        {
                            // Views
                            if ($a = @$card->keyMetricCardData->keyMetricTabs)
                            foreach ($a as $tabA)
                            {
                                if ($b = @$tabA->primaryContent)
                                if ($b->metric == "VIEWS")
                                {
                                    $ownerData->views = $b->total;
                                }
                            }
                            else if ($a = @$card->latestActivityCardData->lifetimeSubsData->metricColumns[0]->counts->values[0])
                            {
                                $ownerData->subscribers = $a;
                            }
                        }
                    }
                }
            }
            return $ownerData;
        });
    }

    /**
     * Get a channel's UCID from a URL.
     * 
     * @return Promise<?string>
     */
    private static function getUcidFromUrl(string $url): Promise/*<?string>*/
    {
        return async(function() use (&$url) {
            $response = (yield Network::innertubeRequest(
                action: "navigation/resolve_url",
                body: [ "url" => "https://www.youtube.com" . $url ],
                ignoreErrors: true // Required for the 404 page instead of uncaught exception.
            ))->getJson();
    
            if (isset($response->endpoint->browseEndpoint->browseId))
            {
                return $response->endpoint->browseEndpoint->browseId;
            }
            // For some handles, resolve_url returns a classic channel
            // URL (e.g. https://www.youtube.com/jawed). For every case
            // that this happens, you can just make another resolve_url
            // request, and it will actually give you the UCID of the
            // channel.
            else if (isset($response->endpoint->urlEndpoint->url))
            {
                $response2 = (yield Network::innertubeRequest(
                    action: "navigation/resolve_url",
                    body: [ "url" => $response->endpoint->urlEndpoint->url ],
                    ignoreErrors: true
                ))->getJson();
    
                return $response2->endpoint->browseEndpoint->browseId ?? null;
            }
    
            return null;
        });
    }
}