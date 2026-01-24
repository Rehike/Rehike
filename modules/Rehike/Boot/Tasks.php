<?php
namespace Rehike\Boot;

use Rehike\{
    ConfigDefinitions,
    ContextManager,
    TemplateManager,
    YtApp,
    Network,
    Network\NetworkCore,
    Async\Concurrency,
    Async\Promise,
    ControllerV2\Core as ControllerV2,
    Player\PlayerCore,
    TemplateUtilsDelegate\RehikeUtilsDelegate,
    ResourceConstantsStore,
    ConfigManager\Config,
    ConfigManager\LoadConfigException,
    Util\Nameserver\Nameserver,
    Util\Base64Url,
    i18n\BootServices as i18nBoot,
    i18n\i18n,
    ErrorHandler\ErrorHandler,
    InnertubeContext
};
use Rehike\Util\ExperimentFlagManager;
use Rehike\SignInV2\SignIn;

/**
 * Implements boot tasks for Rehike.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
final class Tasks
{
    public static function initNetwork(): void
    {
        $desiredDns = Config::getConfigProp("advanced.dnsAddress")
            ?? "1.1.1.1";

        NetworkCore::setResolve([
            Nameserver::get("www.youtube.com", $desiredDns, 443)->serialize()
        ]);
    }

    public static function initResourceConstants(): void
    {
        ResourceConstantsStore::init();
    }

    public static function initConfigManager(): void
    {
        Config::registerConfigDefinitions(
            ConfigDefinitions::getConfigDefinitions()
        );
        
        try
        {
            Config::loadConfig();
            ConfigDefinitions::migrateOldOptions();
        }
        catch (LoadConfigException $e)
        {
            $reason = $e->getReason();
            
            if ($reason == LoadConfigException::REASON_COULD_NOT_OPEN_FILE_HANDLE)
            {
                // macOS and Linux may have default permissions on the htdocs
                // folder which don't permit the PHP script to write files. In
                // this case, we have no choice other than to display an error
                // message to the user telling them to change their permissions.
                ErrorHandler::reportFailedToWriteConfig();
            }
        }

        // Apply early configuration properties for other modules:
        if (Config::getConfigProp("advanced.developer.ignoreUnresolvedPromises"))
        {
            \Rehike\Async\Promise\PromiseResolutionTracker::disable();
        }
    }

    public static function setupTemplateManager(): void
    {
        $utilsDelegate = new RehikeUtilsDelegate();
        $constants = ResourceConstantsStore::get();

        TemplateManager::addGlobal("rehike", $utilsDelegate);
        TemplateManager::addGlobal("ytConstants", $constants);
        TemplateManager::addGlobal("PIXEL", $constants->pixelGif);
    }

    public static function setupI18n(): void
    {
        // i18n v2
        i18nBoot::boot();

        // Also expose common messages to the global variable.
        YtApp::getInstance()->msgs = 
            (array)i18n::getAllTemplates("global");
    }

    public static function setupControllerV2(): void
    {
        ControllerV2::setRedirectHandler(
            require "includes/spf_redirect_handler.php"
        );
    }

    public static function setupPlayer(): void
    {
        PlayerCore::configure([
            "cacheMaxTime"  => 18000, // 5 hours (in seconds)
            "cacheDestDir"  => "cache",
            "cacheDestName" => "player_cache" // .json
        ]);
    }

    public static function setupVisitorData(): void
    {
        $visitor = null;

        // Obtain the info from the user if it exists, otherwise
        // request it and store that.
        if (isset($_COOKIE["VISITOR_INFO1_LIVE"]))
        {
            $visitor = $_COOKIE["VISITOR_INFO1_LIVE"];
        }
        else if (!SignIn::isSignedIn() || !is_string($visitor))
        {
            // Hacky algo to get it from the server:
            $request = Network::urlRequest(
                "https://www.youtube.com",
                [
                    // Force Chrome user agent to ensure we don't get an "Update your browser" message
                    "userAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36"
                ]
            );
            $response = Concurrency::awaitSync($request);

            // Find the configuration set property that contains the visitor
            // data string.
            preg_match("/ytcfg\.set\(({.*?})\);/", $response, $matches);
            $ytcfg = json_decode(@$matches[1]);
            
            // Optimise cases where this class is used:
            ExperimentFlagManager::giveYtConfig($ytcfg);

            $visitor = $ytcfg->INNERTUBE_CONTEXT->client->visitorData;

            // Very hackily extract the cookie string from the encoded base64
            // protobuf string YouTube gives.
            $visitor = Base64Url::decode($visitor);
            $visitor = substr($visitor, 2);
            $visitor = explode("(", $visitor)[0];

            setcookie("VISITOR_INFO1_LIVE", $visitor);

            // Set the visitor data in the context manager now, since we need it configured in
            // order to populate it in the below function.
            ContextManager::setVisitorData($visitor);

            // Build the recommendations list. This operation is blocking so it can't conflict
            // with any homepage requests.
            Concurrency::awaitSync(self::populateVisitorDataRecommendations($visitor));

            // Return; we don't need to do any other work.
            return;
        }

        ContextManager::setVisitorData($visitor);
    }

    /**
     * Populate the recommends for a given visitor data string.
     *
     * Due to YouTube changes in early 2024, neither signed-out sessions in which no videos
     * have been watched, nor users with watch history disabled who have no videos in their
     * history, get default video recommendations on the homepage.
     *
     * This is a fix for the case of signed-out sessions, where we can log views.
     *
     * Unfortunately, it seems to take about 10 seconds for such a request to register, so
     * it's really slow to do.
     */
    private static function populateVisitorDataRecommendations(string $visitor): Promise/*<void>*/
    {
        return Concurrency::async(function() use ($visitor) {
            $defaultVideoId = "jNQXAC9IVRw";
            $yt = YtApp::getInstance();

            $playerResponse = yield Network::innertubeRequest(
                "player",
                [
                    "playbackContext" => [
                        'contentPlaybackContext' => (object) [
                            'autoCaptionsDefaultOn' => false,
                            'autonavState' => 'STATE_OFF',
                            'html5Preference' => 'HTML5_PREF_WANTS',
                            'lactMilliseconds' => '13407',
                            'mdxContext' => (object) [],
                            'playerHeightPixels' => 1080,
                            'playerWidthPixels' => 1920,
                            'signatureTimestamp' => $yt->playerConfig->signatureTimestamp
                        ]
                    ],
                    "startTimeSecs" => $startTime ?? 0,
                    "videoId" => $defaultVideoId
                ]
            );
            $player = $playerResponse->getJson();

            // Get the template information for the default video. It's enough to make a request
            // to add watch history.
            $playbackStatsUrl = $player->playbackTracking->videostatsPlaybackUrl->baseUrl;
            $pbstatsParams = self::parsePlayerStatsParams($playbackStatsUrl);

            // Properties from the playback stats parameters.
            $ns = $pbstatsParams?->ns ?? "";
            $docid = $pbstatsParams?->docid ?? "";
            $el = $pbstatsParams?->el ?? "";
            $vm = $pbstatsParams?->vm ?? "";
            $cpn = $pbstatsParams?->cpn ?? "";
            $ei = $pbstatsParams?->ei ?? "";
            $len = $pbstatsParams?->len ?? "";
            $of = $pbstatsParams?->of ?? "";
            $fexp = $pbstatsParams?->fexp ?? "";

            $url = "https://www.youtube.com/api/stats/playback?" . implode("&", [
                "ns=" . $ns,
                "el=" . $el,
                "cpn=" . $cpn,
                "docid=" . $docid,
                "ver=2",
                "referrer=https://www.youtube.com/",
                "ei=" . $ei,
                "of=" . $of,
                "euri=", // This is okay to be empty
                "lact=622",
                "mos=0",
                "vm=" . $vm,
                "len=" . $len,
                "fexp=" . $fexp,
                "feature=g-high-crv",
            ]);

            $statsRequest = yield Network::urlRequestFirstParty($url, [
                "method" => "GET",
                "redirect" => "follow",
                "headers" => [
                    "X-Goog-Visitor-Id" => InnertubeContext::genVisitorData($visitor),
                    "Cookie" => Network::getCurrentRequestCookie() . "VISITOR_INFO1_LIVE=" . $visitor . "; ",
                    "User-Agent" => $_SERVER["HTTP_USER_AGENT"]
                        ?? "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/107.0"
                ]
            ] + Network::getDefaultYoutubeOpts());
        });
    }

    /**
     * Parses the player stats parameters.
     *
     * This is just used as a helper by the above method.
     */
    private static function parsePlayerStatsParams(string $baseUrl): object
    {
        $out = (object)[];

        // This is evil code to split up URL-encoded parameters like
        // ?arg1=val1&arg2=val2
        $params = explode("&", implode("", array_slice(explode("?", $baseUrl), 1)));

        foreach ($params as $param)
        {
            $bits = explode("=", $param);
            $key = $bits[0];
            $value = $bits[1];

            $out->{$key} = $value;
        }

        return $out;
    }
}