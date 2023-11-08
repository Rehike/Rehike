<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Controller\core\AjaxController;
use Rehike\Model\Comments\CommentThread;
use Rehike\Model\Comments\CommentsHeader;
use Rehike\Model\Appbar\MAppbar as Appbar;
use Rehike\Network;
use Rehike\ConfigManager\Config;

/**
 * Watch fragments ajax controller
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 * 
 * @version 1.0.20220805
 */
class AjaxWatchFragments2Controller extends AjaxController
{
    public bool $useTemplate = true;

    // 404 by default.
    // The real template will be set by subcontroller functions.
    public string $template = '404';

    public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        $fragsId = $_GET['frags'] ?? '';
        switch ($fragsId) {
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
        $this->spfIdListeners = [
            '@masthead_search<data-is-crosswalk>',
            'guide'
        ];

        $yt->appbar = new Appbar();
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
            body: [ "continuation" => $_GET['ctoken'] ]
        )->then(function($response) use (&$yt) {
            $ytdata = $response->getJson();

            $yt->page->commentsRenderer->headerRenderer = CommentsHeader::fromData(
                $ytdata->onResponseReceivedEndpoints[0]->reloadContinuationItemsCommand->continuationItems[0]->commentsHeaderRenderer
            );

            /**
             * Comments Threads Rewrite
             * TODO: further rewrite may be necessary
             */
            $_oct = $ytdata->onResponseReceivedEndpoints[1]->reloadContinuationItemsCommand; // original comment threads

            CommentThread::bakeComments($_oct->continuationItems)->then(function ($value) use ($yt) {
                $yt->page->commentsRenderer->comments = $value;
            });
        });
    }
}

return new AjaxWatchFragments2Controller();