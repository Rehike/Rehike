<?php
namespace Rehike\Controller;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Async\Promise;

use Com\Youtube\Innertube\Helpers\VideosContinuationWrapper;
use Rehike\Network;
use Rehike\Helper\WebV2Shelves;
use Rehike\Util\RichShelfUtils;
use Rehike\Model\Feed\MFeedAppbarNav;
use Rehike\SignInV2\SignIn;
use \Com\Youtube\Innertube\Request\BrowseRequestParams;
use \Rehike\Util\Base64Url;
use \Rehike\Model\History\HistoryModel;
use \Rehike\Model\Browse\InnertubeBrowseConverter;
use Rehike\ConfigManager\Config;
use \Rehike\Util\ParsingUtils;

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
    public string $template = "feed";

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

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $feedId = $request->path[1] ?? "what_to_watch";
        $feedId = "FE" . $feedId;

        $this->setEndpoint("browse", $feedId);

        if (in_array($feedId, self::FEED_APPBAR_SUPPORTED_IDS))
        {
            $yt->appbar->nav = new MFeedAppbarNav($feedId);
        }

        if (!SignIn::isSignedIn() && in_array($feedId, self::SIGNIN_REQUIRED_IDS))
        {
            header("Location: /");
        }
        
        switch ($feedId) {
            case "FEwhat_to_watch":
                $this->whatToWatch($yt);
                break;
            case "FEsubscriptions":
                $this->subscriptions($yt, $request);
                break;
            default:
                $this->miscFeeds($yt, $request, $feedId);
                break;
        }
    }

    /**
     * Home page.
     * 
     * Internally, the homepage is known as FEwhat_to_watch, which corresponds
     * with its older name "What to Watch".
     */
    private function whatToWatch(YtApp $yt): Promise
    {
        return async(function() use ($yt) {
            // The copyright text in the description only appeared if the
            // user originated from the homepage.
            $yt->footer->enableCopyright = true;

            // The homepage also had the searchbox in the masthead autofocus.
            $yt->masthead->searchbox->autofocus = true;

            if ($a = Config::getConfigProp("experiments.disableSignInOnHome"))
            {
                $useAuthentication = !(bool)$a;
            }
            else
            {
                $useAuthentication = true;
            }

            // Initial Android request to get continuation
            $response = yield Network::innertubeRequest(
                action: "browse",
                body: [
                    "browseId" => "FEwhat_to_watch"
                ],
                clientName: "TVHTML5",
                clientVersion: "7.20241024.10.00",
                useAuthentication: $useAuthentication
            );

            $ytdata = $response->getJson();
            
            foreach ($ytdata->contents->tvBrowseRenderer->content->tvSurfaceContentRenderer->content->sectionListRenderer->continuations as $continuation)
            if (isset($continuation->nextContinuationData))
            {
                $continuation = $continuation->nextContinuationData->continuation;
            }

            $newContinuation = WebV2Shelves::continuationToWeb($continuation);

            // Thrown to next then
            $response = yield Network::innertubeRequest(
                action: "browse",
                body: [
                    "continuation" => $newContinuation
                ],
                useAuthentication: $useAuthentication
            );

            $data = $response->getJson();

            $yt->page->content = (object) [
                "sectionListRenderer" => InnertubeBrowseConverter::sectionListRenderer(RichShelfUtils::reformatResponse($data)->sectionListRenderer)
            ];
        });
    }

    /**
     * History feed.
     */
    private function history(YtApp $yt, RequestMetadata $request): void
    {
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

            if (isset($yt->page->title))
            {
                $this->setTitle($yt->page->title);
            }
        });
    }

    /**
     * Other feeds.
     * 
     * Don't even try to make sense of this.
     */
    private function miscFeeds(YtApp $yt, RequestMetadata $request, string $feedId): void
    {
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

            if (isset($content->sectionListRenderer))
            {
                $content->sectionListRenderer = InnertubeBrowseConverter::sectionListRenderer($content->sectionListRenderer, [
                    "channelRendererUnbrandedSubscribeButton" => true
                ]);
            }

            $yt->page->content = $content;

            if (isset($ytdata->header))
            foreach ($ytdata->header as $header)
            if (isset($header->title))
            if (isset($header->title->runs)
            || isset($header->title->simpleText))
                $this->setTitle(ParsingUtils::getText($header->title));
            else
                $this->setTitle($header->title);
        });
    }

    /**
     * Subscriptions feed.
     * 
     * Now a separate function due to the rich grid update.
     * 
     * For anyone who is about to read or edit this function, I am sincerely
     * sorry, and I wish you the best of luck. You're going to need it.
     */
    private function subscriptions(YtApp $yt, RequestMetadata $request): void
    {
        $list = ((int)@$request->params->flow == 2);

        Network::innertubeRequest(
            action: "browse",
            body: [
                "browseId" => "FEsubscriptions"
            ]
        )->then(function($response) use (&$yt, $list) {
            $ytdata = $response->getJson();

            $rgr = $ytdata->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content->richGridRenderer;
            $rcontents = $ytdata->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content->richGridRenderer->contents;

            $contents = [
                (object) [
                    "shelfRenderer" => $rcontents[0]->richSectionRenderer->content->shelfRenderer
                ]
            ];
            $menu = &$contents[0]->shelfRenderer->menu->menuRenderer->topLevelButtons;

            // Fix the state of the shelf menu accordingly
            if ($list)
            {
                foreach ($menu as $button)
                {
                    $button->buttonRenderer->isSelected = !$button->buttonRenderer->isSelected;
                }
            }

            // Snip the shelf off the array so we can work on the videos themselves
            array_shift($rcontents);

            if ($list)
            {    
                foreach ($rcontents as $i => $content)
                if (isset($content->richItemRenderer))
                {
                    
                    if ($i == 0)
                    {
                        $contents[0]->shelfRenderer->content = (object) [
                            "expandedShelfContentsRenderer" => (object) [
                                "items" => [
                                    InnertubeBrowseConverter::richItemRenderer($content->richItemRenderer, [
                                        "listView" => $list
                                    ])
                                ]
                            ]
                        ];
                    }
                    else
                    {
                        $contents[] = InnertubeBrowseConverter::richItemRenderer($content->richItemRenderer, [
                            "listView" => $list
                        ]);
                    }
                }
            }
            else
            {
                $contents[0]->shelfRenderer->content = (object)[
                    "gridRenderer" => InnerTubeBrowseConverter::richGridRenderer($rgr)
                ];
            }

            $yt->page->content = (object) [
                "sectionListRenderer" => (object) [
                    "contents" => [
                        (object) [
                            "itemSectionRenderer" => (object) [
                                "contents" => $contents
                            ]
                        ]
                    ]
                ]
            ];

            if ($cont = @$rcontents[count($rcontents) - 1]->continuationItemRenderer)
            {
                
                $ctoken = &$cont->continuationEndpoint->continuationCommand->token;
                $contw = new VideosContinuationWrapper();
                $contw->setContinuation($ctoken);
                $contw->setList($list);
                $contw->setWrapInGrid(!$list);

                $ctoken = Base64Url::encode($contw->serializeToString());

                $yt->page->content->sectionListRenderer->contents[] = (object) [
                    "continuationItemRenderer" => $cont
                ];
            }

            if (isset($ytdata->header))
            foreach ($ytdata->header as $header)
            if (isset($header->title))
            if (isset($header->title->runs)
            || isset($header->title->simpleText))
                $this->setTitle(ParsingUtils::getText($header->title));
            else
                $this->setTitle($header->title);
        });
    }
};