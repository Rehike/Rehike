<?php
namespace Rehike\Model\Masthead\Notifications;

use Rehike\i18n;

class MNotificationClickcard {
    public $template = "masthead_notifications";
    public $id = "yt-masthead-notifications";
    public $cardAction = "yt.www.notifications.inbox.handleNotificationsClick";
    public $cardClass = [
        "yt-scrollbar",
        "yt-notification-inbox-clickcard"
    ];
    public $cardId = "yt-masthead-notifications-clickcard";
    public $content;

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $this->content = (object) [];
        $this->content->title = $i18n->notificationsTitle;
        $this->content->button = new MNotificationSettingsButton();
    }
}