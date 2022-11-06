<?php
namespace Rehike\Controller;

use Rehike\Request;
use Rehike\Util\WebV2Shelves;
use Rehike\Util\RichShelfUtils;
use Rehike\Model\Feed\MFeedAppbarNav;
use Rehike\Signin\API as SignIn;
use Rehike\TemplateFunctions;

return new class extends \Rehike\Controller\core\NirvanaController {
    public $template = "feed";

    const FEED_APPBAR_SUPPORTED_IDS = [
        "FEwhat_to_watch",
        "FEtrending",
        "FEsubscriptions"
    ];

    const SIGNIN_REQUIRED_IDS = [
        "FEsubscriptions"
    ];

    public function onGet(&$yt, $request) {
        $feedId = $request -> path[1] ?? "what_to_watch";
        $feedId = "FE" . $feedId;

        $this -> setEndpoint("browse", $feedId);

        if (in_array($feedId, self::FEED_APPBAR_SUPPORTED_IDS)) {
            $yt -> appbar -> nav = new MFeedAppbarNav($feedId);
        }

        if (!SignIn::isSignedIn() && in_array($feedId, self::SIGNIN_REQUIRED_IDS)) {
            header("Location: /");
        }
        
        switch ($feedId) {
            case "FEwhat_to_watch":
                self::whatToWatch($yt);
                break;
            default:
                self::miscFeeds($yt, $request, $feedId);
                break;
        }
    }

    /**
     * Get and build homepage.
     */
    public static function whatToWatch(&$yt) {
        $yt -> footer -> enableCopyright = true;
        $yt -> masthead -> searchbox -> autofocus = true;

        // Initial Android request to get continuation
        Request::queueInnertubeRequest(
            "android",
            "browse", 
            (object)[
                "browseId" => "FEwhat_to_watch"
            ],
            "ANDROID",
            "17.14.33"
        );
        $android = Request::getResponses()["android"];
        $ytdata = json_decode($android);

        foreach ($ytdata -> contents -> singleColumnBrowseResultsRenderer -> tabs as $tab)
        if (isset($tab -> tabRenderer -> content -> sectionListRenderer))
        foreach($tab -> tabRenderer -> content -> sectionListRenderer -> continuations as $cont)
        if (isset($cont -> reloadContinuationData))
        $continuation = str_replace("%3D", "", $cont -> reloadContinuationData -> continuation);


        $newContinuation = WebV2Shelves::continuationToWeb($continuation);

        Request::queueInnertubeRequest("wv2", "browse", (object) [
            "continuation" => $newContinuation
        ]);
        $wv2response = Request::getResponses()["wv2"];
        $wv2data = json_decode($wv2response);
        
        $yt -> page -> content = RichShelfUtils::reformatResponse($wv2data);
    }

    /**
     * Other feeds.
     */
    public static function miscFeeds(&$yt, $request, $feedId) {
        $useListFlow = $request -> params -> flow == "2";

        Request::queueInnertubeRequest("feed", "browse", (object) [
            "browseId" => $feedId,
            "params" => @$request -> params -> bp ?? ($useListFlow) ? "MAI%3D" : null
        ]);
        $ytdata = json_decode(Request::getResponses()["feed"]);

        if (isset($ytdata -> contents -> twoColumnBrowseResultsRenderer))
        foreach ($ytdata -> contents -> twoColumnBrowseResultsRenderer -> tabs as $tab)
        if (isset($tab -> tabRenderer -> content))
            $yt -> page -> content = $tab -> tabRenderer -> content;

        foreach ($ytdata -> header as $header)
        if (isset($header -> title))
        if (isset($header -> title -> runs)
         or isset($header -> title -> simpleText))
            $yt -> page -> title = TemplateFunctions::getText($header -> title);
        else
            $yt -> page -> title = $header -> title;
    }
};