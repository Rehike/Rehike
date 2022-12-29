<?php
namespace Rehike\Util;

use Rehike\Network;
use YukisCoffee\CoffeeRequest\Promise;

/**
 * General utilties for channels.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ChannelUtils {
    /**
     * Get a channel's UCID from an internal request URL.
     * 
     * @return Promise<string>
     */
    public static function getUcid($request): Promise/*<string>*/ {
        if (in_array($request->path[0], ["channel", "user", "c"])) {
            switch($request->path[0]) {
                case "channel":
                    return new Promise(function ($resolve) use ($request) {
                        $ucid = $request->path[1] ?? "";

                        if (substr($ucid, 0, 2) == "UC")
                        {
                            $resolve($ucid);
                        }
                        else
                        {
                            $resolve("");
                        }
                    });
                    break;
                case "user":
                case "c":
                    return self::handleNameUrl(
                        $request->path[0] . "/" . $request->path[1]
                    );
                    break;
            }
        } else {
            return self::handleNameUrl($request->path[0]);
        }

        return "";
    }

    /**
     * Handle a named URL (anything other than UCID really).
     * 
     * This queries InnerTube to get the UCID for the URL. Otherwise an
     * empty string will be returned.
     * 
     * @return Promise<string>
     */
    public static function handleNameUrl(string $uri): Promise/*<string>*/
    {
        return new Promise(function ($resolve, $reject) use ($uri) {
            Network::innertubeRequest(
                action: "navigation/resolve_url",
                body: [
                    "url" => "https://www.youtube.com/" . $uri
                ]
            )->then(function ($response) use ($resolve) {
                $ytdata = $response->getJson();

                if (isset($ytdata->endpoint->browseEndpoint->browseId)) {
                    $resolve($ytdata->endpoint->browseEndpoint->browseId);
                } else {
                    $resolve("");
                }
            });
        });
    }
}