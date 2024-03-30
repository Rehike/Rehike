<?php
namespace Rehike\Model\Common\Subscription;

use Rehike\i18n\i18n;
use Rehike\Util\ParsingUtils;

class MSubscriptionActions
{
    public $showUnsubConfirmDialog = true;
    public $showSubscriptionPreferences = true;
    public $subscriberCountText = "";
    public $shortSubscriberCountText = "";

    /** @var MSubscriptionButton */
    public $subscriptionButton;

    /** @var MSubscriberCount */
    public $subscriberCountRenderer;

    public $unsubConfirmDialog;
    public $subscriptionPreferencesButton;

    public function __construct($opts)
    {
        $i18n = i18n::getNamespace("misc");

        // Default options
        $opts += [
            "longText" => "",
            "shortText" => "",
            "showCount" => true,
            "isDisabled" => false,
            "isSubscribed" => false,
            "type" => "FREE",
            "branded" => "true",
            "channelExternalId" => "",
            "params" => "",
            "subscribeText" => $i18n->get("subscribeText"),
            "subscribedText" => $i18n->get("subscribedText"),
            "unsubscribeText" => $i18n->get("unsubscribeText"),
            "tooltip" => null,
            "unsubConfirmDialog" => null,
            "notificationStateId" => 3,
            "href" => null
        ];

        $this->unsubConfirmDialog = $opts["unsubConfirmDialog"];

        if ($a = @$this->unsubConfirmDialog->confirmButton->buttonRenderer)
        {
            $a->class = [
                "overlay-confirmation-unsubscribe-button",
                "yt-uix-overlay-close"
            ];
        }

        if ($a = @$this->unsubConfirmDialog->cancelButton->buttonRenderer)
        {
            $a->class = ["yt-uix-overlay-close"];
            $a->style = "STYLE_DEFAULT";
        }

        if ($opts["showCount"])
        {
            $this->subscriberCountText = $opts["longText"];
            $this->shortSubscriberCountText = $opts["shortText"];
        }

        $this->subscriptionButton = new MSubscriptionButton([
            "isDisabled" => $opts["isDisabled"],
            "isSubscribed" => $opts["isSubscribed"],
            "type" => $opts["type"],
            "branded" => $opts["branded"],
            "channelExternalId" => $opts["channelExternalId"],
            "params" => $opts["params"],
            "tooltip" => $opts["tooltip"],
            "href" => $opts["href"],
            "subscribeText" => $opts["subscribeText"],
            "subscribedText" => $opts["subscribedText"],
            "unsubscribeText" => $opts["unsubscribeText"]
        ]);

        $this->subscriptionPreferencesButton = new MSubscriptionPreferencesButton($opts["channelExternalId"], $opts["notificationStateId"]);

        if ($opts["longText"])
        {
            $this->subscriberCountRenderer = new MSubscriberCount(
                $opts["longText"], $opts["branded"], "horizontal"
            );
        }
    }

    /**
     * Build a subscription actions model from a standard InnerTube WEB data.
     */
    public static function fromData($data, $count = "", $branded = true)
    {
        return new self([
            "branded" => $branded,
            "longText" => $count,
            "shortText" => $count,
            "isSubscribed" => $data->subscribed ?? false,
            "channelExternalId" => $data->channelId ?? "",
            "params" => $data->onSubscribeEndpoints[0] ->subscribeEndpoint->params ?? null,
            "subscribeText" => ParsingUtils::getText($data->unsubscribedButtonText ?? null),
            "subscribedText" => ParsingUtils::getText($data->subscribedButtonText ?? null),
            "unsubscribeText" => ParsingUtils::getText($data->unsubscribeButtonText ?? null),
            "unsubConfirmDialog" => $data->onUnsubscribeEndpoints[0] ->signalServiceEndpoint->actions[0] ->openPopupAction->popup->confirmDialogRenderer ?? null,
            "notificationStateId" => $data->notificationPreferenceButton->subscriptionNotificationToggleButtonRenderer->currentStateId ?? 3
        ]);
    }

    public static function buildMock($count = "", $branded = true)
    {
        $i18n = i18n::getNamespace("misc");

        return new self([
            "isDisabled" => true,
            "isSubscribed" => false,
            "longText" => $count,
            "shortText" => $count,
            "branded" => $branded,
            "tooltip" => $i18n->get("selfSubscribeTooltip")
        ]);
    }

    public static function signedOutStub($count = "", $branded = true)
    {
        return new self([
            "longText" => $count,
            "shortText" => $count,
            "branded" => $branded,
            "href" => "https://accounts.google.com/ServiceLogin?service=youtube&amp;uilel=3&amp;hl=en&amp;continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fapp%3Ddesktop%26action_handle_signin%3Dtrue%26hl%3Den%26next%3D%252F%26feature%3Dsign_in_button&amp;passive=true"
        ]);
    }
}