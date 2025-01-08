<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Controller\core\AjaxController;

use Rehike\Network;
use Rehike\Async\Promise;
use Rehike\Util\Base64Url;
use Com\Youtube\Innertube\Request\EventReminderRequestParams;
use Com\Youtube\Innertube\Request\EventReminderRequestParams\UnknownThing;

use Rehike\ControllerV2\{
    IGetController,
    IPostController,
};

/**
 * Controller for the live event reminders AJAX.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
class LiveEventsRemindersAjaxController extends AjaxController implements IPostController
{
    public bool $useTemplate = false;

    public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        $action = self::findAction();

        switch ($action)
        {
            case "set_reminder":
                $request = self::setReminder($request);
                break;
            case "remove_reminder":
                $request = self::removeReminder($request);
                break;
            default:
                self::error();
                return;
        }

        $request->then(function ($ytdata) {
            if (isset($ytdata->errors))
            {
                self::error();
            }
            else
            {
                http_response_code(200);
                echo '{"response":"SUCCESS"}';
            }
        });
    }

    /**
     * Set a live event reminder.
     */
    private static function setReminder(RequestMetadata $request): Promise
    {
        return new Promise(function ($resolve) use ($request) {
            $params = new EventReminderRequestParams();

            if (!isset($request->params->vid))
            {
                self::error();
            }

            $params->setVideoId($request->params->vid);

            $thing = new UnknownThing();
            $thing->setUnknownValue(0);
            $thing->setUnknownValue2(0);

            $params->setUnknownThing($thing);

            Network::innertubeRequest(
                action: "notification/add_upcoming_event_reminder",
                body: [
                    "params" => Base64Url::encode($params->serializeToString())
                ]
            )->then(function ($response) use ($resolve) {
                $resolve( $response->getJson() );
            });
        });
    }
    
    /**
     * Remove a live event reminder.
     */
    private static function removeReminder(RequestMetadata $request): Promise
    {
        return new Promise(function ($resolve) use ($request) {
            $params = new EventReminderRequestParams();

            if (!isset($request->params->vid))
            {
                self::error();
            }
            
            $params->setVideoId($request->params->vid);

            $thing = new UnknownThing();
            $thing->setUnknownValue(0);
            $thing->setUnknownValue2(0);

            $params->setUnknownThing($thing);

            Network::innertubeRequest(
                action: "notification/remove_upcoming_event_reminder",
                body: [
                    "params" => Base64Url::encode($params->serializeToString())
                ]
            )->then(function ($response) use ($resolve) {
                $resolve( $response->getJson() );
            });
        });
    }
}