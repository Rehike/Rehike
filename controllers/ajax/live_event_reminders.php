<?php
namespace Rehike\Controller\ajax;

use Rehike\Request;
use Rehike\Util\Base64Url;
use Com\YouTube\Innertube\Request\EventReminderRequestParams;
use Com\YouTube\Innertube\Request\EventReminderRequestParams\UnknownThing;

return new class extends \Rehike\Controller\core\AjaxController {
    public $useTemplate = false;

    public function onPost(&$yt, $request) {
        $action = self::findAction();

        $ytdata = (object) [];

        switch ($action) {
            case "set_reminder":
                self::setReminder($ytdata, $request);
                break;
            default:
                self::error();
                break;
        }

        echo json_encode($ytdata);
        die();

        if (isset($ytdata -> errors)) {
            self::error();
        } else {
            http_response_code(200);
            echo '{"response":"SUCCESS"}';
        }
    }

    /**
     * Set a live event reminder.
     * 
     * @var object          $ytdata   Object to be filled with data.
     * @var RequestMetadata $request  Request metadata.
     */
    private static function setReminder(&$ytdata, $request) {
        $params = new EventReminderRequestParams();
        if (!isset($request -> params -> vid)) {
           self::error();
        }
        $params -> setVideoId($request -> params -> vid);

        $thing = new UnknownThing();
        $thing -> setUnknownValue(0);
        $thing -> setUnknownValue2(0);

        $params -> setUnknownThing($thing);

        Request::queueInnertubeRequest("main", "notification/add_upcoming_event_reminder", (object) [
            "params" => Base64Url::encode($params -> serializeToString())
        ]);
        $ytdata = json_decode(Request::getResponses()["main"]);
    }
};