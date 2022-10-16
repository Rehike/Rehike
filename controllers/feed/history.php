<?php
namespace Rehike\Controller;

use Rehike\Controller\core\NirvanaController;
use Rehike\Request;

/**
 * History feed controller
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 * 
 * @version 1.0.20220805
 */
class FeedHistoryController extends NirvanaController {
    public $template = 'feed/history/history';

    public function onGet(&$yt, $request) {
        $this->useJsModule('www/feed');
        $this->setEndpoint("browse", "FEhistory");

        if (isset($request->path[2])) {
            switch($request->path[2]) {
                case '':
                    self::tabMain();
                    break;
                case 'search_history':
                    self::tabSearchHistory();
                    break;
                case 'comment_history':
                    self::tabCommentHistory();
                    break;
                default:
                    $this->template = "404";
                    break;
            }
        } else {
            self::tabMain();
        }
    }

    private function tabMain() {
        $this->template = 'feed/history/history';
    }

    private function tabSearchHistory() {
        $this->template = 'feed/history/search_history';
    }

    private function tabCommentHistory() {
        $this->template = 'feed/history/comment_history';
    }
}

return new FeedHistoryController();