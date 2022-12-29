<?php
namespace Rehike\Controller;

use Rehike\Network;
use Rehike\Util\WebV2Shelves;
use Rehike\Util\RichShelfUtils;
use Rehike\Model\Feed\MFeedAppbarNav;
use Rehike\Signin\API as SignIn;
use Rehike\TemplateFunctions;
use \Com\Youtube\Innertube\Request\BrowseRequestParams;
use \Rehike\Util\Base64Url;
use \Rehike\Model\History\HistoryModel;
use \Rehike\Model\Browse\InnertubeBrowseConverter;

use function Rehike\Async\async;

/**
 * Common controller for all feed pages.
 * 
 * This includes the homepage, Trending page, Subscriptions page, and many
 * other ones.
 * 
 * Feeds are one of the most complicated and varying parts of InnerTube and
 * YouTube's internal structure, but also common enough that it's only
 * reasonable to share code for them.
 * 
 * That said, it's very difficult to make this work just right. So be warned,
 * this may be the buggiest part of Rehike.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends \Rehike\Controller\core\NirvanaController {
    public $template = "feed";

    /**
     * IDs of feeds to add the "common feed appbar" on.
     * 
     * Since 2015, YouTube has used this to create horizontal "tabs" between
     * the homepage, trending page, and subscriptions page.
     * 
     * @see MFeedAppbarNav
     */
    const FEED_APPBAR_SUPPORTED_IDS = [
        "FEwhat_to_watch",
        "FEtrending",
        "FEsubscriptions"
    ];

    /**
     * IDs of feeds that require the user to be signed in to access.
     * 
     * If the user is signed out, they will be redirected to the homepage. This
     * is to maintain compatibility with the standard YouTube server.
     */
    const SIGNIN_REQUIRED_IDS = [
        "FEsubscriptions"
    ];

    public function onGet(&$yt, $request) {
        $feedId = $request->path[1] ?? "what_to_watch";
        $feedId = "FE" . $feedId;

        $this->setEndpoint("browse", $feedId);

        if (in_array($feedId, self::FEED_APPBAR_SUPPORTED_IDS)) {
            $yt->appbar->nav = new MFeedAppbarNav($feedId);
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
     * Home page.
     * 
     * Internally, the homepage is known as FEwhat_to_watch, which corresponds
     * with its older name "What to Watch".
     */
    public static function whatToWatch(&$yt) {
    return async(function() use ($yt) 
    {
        // The copyright text in the description only appeared if the
        // user originated from the homepage.
        $yt->footer->enableCopyright = true;

        // The homepage also had the searchbox in the masthead autofocus.
        $yt->masthead->searchbox->autofocus = true;

        // Initial Android request to get continuation
        $response = yield Network::innertubeRequest(
            action: "browse",
            body: [
                "browseId" => "FEwhat_to_watch"
            ],
            clientName: "ANDROID",
            clientVersion: "17.14.33"
        );

        $ytdata = $response->getJson();

        // Why we need to write better InnerTube parsing tools:
        foreach ($ytdata->contents->singleColumnBrowseResultsRenderer->tabs as $tab)
        if (isset($tab->tabRenderer->content->sectionListRenderer))
        foreach($tab->tabRenderer->content->sectionListRenderer->continuations as $cont)
        if (isset($cont->reloadContinuationData))
        $continuation = $cont->reloadContinuationData->continuation;

        $newContinuation = WebV2Shelves::continuationToWeb($continuation);

        // Thrown to next then
        $response = yield Network::innertubeRequest(
            action: "browse",
            body: [
                "continuation" => $newContinuation
            ]
        );

        $data = $response->getJson();

        $yt->page->content = RichShelfUtils::reformatResponse($data);
    });
    }

    /**
     * History feed.
     */
    public static function history(&$yt, $request) {
        $params = new BrowseRequestParams();
        if (isset($request->params->bp))
            $params->mergeFromString(Base64Url::decode($request->params->bp));

        if (isset($request->path[2]))
            $params->setTab($request->path[2]);

        Network::innertubeRequest(
            action: "browse",
            body: [
                "browseId" => "FEhistory",
                "params" => Base64Url::encode($params->serializeToString())
            ]
        )->then(function ($response) use ($yt) {
            $yt->page = HistoryModel::bake($response->getJson());
        });
    }

    /**
     * Other feeds.
     * 
     * Don't even try to make sense of this.
     */
    public static function miscFeeds(&$yt, $request, $feedId) {
        $params = new BrowseRequestParams();
        if (isset($request->params->bp))
            $params->mergeFromString(Base64Url::decode($request->params->bp));
        
        if (isset($request->params->flow))
            $params->setFlow((int) $request->params->flow);
        
        if (isset($request->path[2]))
            $params->setTab($request->path[2]);

        Network::innertubeRequest(
            action: "browse",
            body: [
                "browseId" => $feedId,
                "params" => Base64Url::encode($params->serializeToString())
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();

            if (isset($ytdata->contents->twoColumnBrowseResultsRenderer))
            foreach ($ytdata->contents->twoColumnBrowseResultsRenderer->tabs as $tab)
            if (isset($tab->tabRenderer->content))
                $content = $tab->tabRenderer->content;

            if (isset($content->sectionListRenderer)) {
                $content->sectionListRenderer = InnertubeBrowseConverter::sectionListRenderer($content->sectionListRenderer, [
                    "channelRendererUnbrandedSubscribeButton" => true
                ]);
            }

            $yt->page->content = $content;

            if (isset($ytdata->header))
            foreach ($ytdata->header as $header)
            if (isset($header->title))
            if (isset($header->title->runs)
            or isset($header->title->simpleText))
                $yt->page->title = TemplateFunctions::getText($header->title);
            else
                $yt->page->title = $header->title;
        });
    }
};
