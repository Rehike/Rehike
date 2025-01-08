<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Controller\core\AjaxController;
use Rehike\Model\Comments\CommentThread;
use Rehike\Model\Comments\CommentsHeader;
use Rehike\Model\Appbar\MAppbar;
use Rehike\Network;
use Rehike\ConfigManager\Config;

use Rehike\ControllerV2\{
    IGetController,
    IPostController,
};

/**
 * Watch fragments ajax controller
 *
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 *
 * @version 1.0.20220805
 */
class WatchFragments2AjaxController extends AjaxController implements IPostController
{
    public bool $useTemplate = true;

    // 404 by default.
    // The real template will be set by subcontroller functions.
    public string $template = '404';

    public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        $fragsId = $_GET['frags'] ?? '';
        switch ($fragsId)
        {
            case 'comments':
                self::getComments($yt);
                break;
            case 'guide':
                self::getGuide($yt);
                break;
            case '':
            default:
                break;
        }
    }

    private function getGuide(YtApp $yt): void
    {
        $this->template = "ajax/watch_fragments2/guide";

        $yt->appbar = new MAppbar();
        $this->getPageGuide()->then(function ($guide) use ($yt) {
            $yt->appbar->addGuide($guide);
        });
    }

    private function getComments(YtApp &$yt): void
    {
        $this->template = "ajax/watch_fragments2/comments";
        $yt->page = (object) [];
        $yt->page->commentsRenderer = (object) [
            "headerRenderer" => (object)[],
            "comments" => (object)[]
        ];

        Network::innertubeRequest(
            action: "next",
            body: [
                "continuation" => $_GET['ctoken'],
                // We use German as a base language because it has full counts
                "hl" => "de_DE"
            ]
        )->then(function($response) use (&$yt) {
            $ytdata = $response->getJson();


            /**
             * Comments Threads Rewrite
             * TODO: further rewrite may be necessary
             */
            $commentsBakery = new CommentThread($ytdata);
            foreach ($ytdata->onResponseReceivedEndpoints as $endpoint)
            {
                if (!(@$endpoint = $endpoint->reloadContinuationItemsCommand))
                {
                    continue;
                }
                if ($endpoint->slot === "RELOAD_CONTINUATION_SLOT_HEADER")
                {
                    $yt->page->commentsRenderer->headerRenderer = CommentsHeader::fromData(
                        $endpoint->continuationItems[0]->commentsHeaderRenderer
                    );
                }
                else if ($endpoint->slot === "RELOAD_CONTINUATION_SLOT_BODY")
                {
                    // At least with some videos, if there are no comments the continuationItems property doesn't exist.
                    $commentsBakery->bakeComments(($endpoint->continuationItems ?? []))->then(function ($value) use ($yt) {
                        $yt->page->commentsRenderer->comments = $value;
                    });
                }
            }


        });
    }
}