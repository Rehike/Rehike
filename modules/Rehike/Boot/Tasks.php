<?php
namespace Rehike\Boot;

use YukisCoffee\CoffeeRequest\CoffeeRequest;

use Rehike\{
    ContextManager,
    TemplateManager,
    i18n,
    YtApp,
    Network,
    Async\Concurrency,
    ControllerV2\Core as ControllerV2,
    Player\PlayerCore,
    Misc\RehikeUtilsDelegate,
    Misc\ResourceConstantsStore,
    Util\Nameserver\Nameserver,
    Util\Base64Url
};

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
        CoffeeRequest::setResolve([
            Nameserver::get("www.youtube.com", "1.1.1.1", 443)->serialize()
        ]);
    }

    public static function initResourceConstants(): void
    {
        ResourceConstantsStore::init();
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
        i18n::setDefaultLanguage("en");

        i18n::newNamespace("main/regex")
            ->registerFromFolder("i18n/regex");
        i18n::newNamespace("main/misc")
            ->registerFromFolder("i18n/misc");
        i18n::newNamespace("main/guide")
            ->registerFromFolder("i18n/guide");
        $msgs = i18n::newNamespace("main/global")
            ->registerFromFolder("i18n/global");

        // Also expose common messages to the global variable.
        YtApp::getInstance()->msgs = 
            $msgs->getStrings()[$msgs->getLanguage()];
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
        // Obtain the info from the user if it exists, otherwise
        // request it and store that.
        if (isset($_COOKIE["VISITOR_INFO1_LIVE"]))
        {
            $visitor = $_COOKIE["VISITOR_INFO1_LIVE"];
        }
        else
        {
            // Hacky algo to get it from the server:
            $request = Network::urlRequest("https://www.youtube.com");
            $response = Concurrency::awaitSync($request);

            // Find the configuration set property that contains the visitor
            // data string.
            preg_match("/ytcfg\.set\(({.*?})\);/", $response, $matches);
            $ytcfg = json_decode(@$matches[1]);

            $visitor = $ytcfg->INNERTUBE_CONTEXT->client->visitorData;

            // Very hackily extract the cookie string from the encoded base64
            // protobuf string YouTube gives.
            $visitor = Base64Url::decode($visitor);
            $visitor = substr($visitor, 2);
            $visitor = explode("(", $visitor)[0];

            setcookie("VISITOR_INFO1_LIVE", $visitor);
        }

        ContextManager::setVisitorData($visitor);
    }
}