<?php
namespace Rehike\Controller\ajax;

use Rehike\Controller\core\AjaxController;
use Rehike\Model\Comments\CommentThread;
use Rehike\Model\Comments\CommentsHeader;
use Rehike\Model\Appbar\MAppbar as Appbar;
use Rehike\Request;

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
class AjaxWatchFragments2Controller extends AjaxController {
    public $useTemplate = true;
    public $template = '404';

    public function onPost(&$yt, $request) {
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

    private function getGuide(&$yt) {
        $this->template = "common/appbar/appbar_guide";
        $this->spfIdListeners = [
            '@masthead_search<data-is-crosswalk>',
            'guide'
        ];

        $yt->appbar = new Appbar();
        $yt->appbar->addGuide($this->getPageGuide());
    }

    private function getComments(&$yt) {
        $this->template = 'common/watch/watch_fragments2/comments';
        $yt->page = (object) [];
        $yt->commentsRenderer = (object) [
            "headerRenderer" => (object)[],
            "comments" => (object)[]
        ];
        $this->spfIdListeners = [
            '@masthead_search<data-is-crosswalk>',
            'watch-discussion'
        ];

        $response = Request::innertubeRequest("next", (object)[
            "continuation" => $_GET['ctoken']
        ]);
        
        $ytdata = json_decode($response);

        $yt->commentsRenderer->headerRenderer = CommentsHeader::fromData($ytdata->onResponseReceivedEndpoints[0]->reloadContinuationItemsCommand->continuationItems[0]->commentsHeaderRenderer);

        /**
         * Comments Threads Rewrite
         * TODO: further rewrite may be necessary
         */
        $_oct = $ytdata->onResponseReceivedEndpoints[1]->reloadContinuationItemsCommand; // original comment threads
        $yt->commentsRenderer->comments = CommentThread::bakeComments($_oct);
    }
}

return new AjaxWatchFragments2Controller();