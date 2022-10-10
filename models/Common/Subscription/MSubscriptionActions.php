<?php
namespace Rehike\Model\Common\Subscription;

use Rehike\i18n;
use Rehike\TemplateFunctions;

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

    public function __construct($opts)
    {
        $i18n = i18n::getNamespace("main/misc");

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
            "subscribeText" => $i18n -> get("subscribeText"),
            "subscribedText" => $i18n -> get("subscribedText"),
            "unsubscribeText" => $i18n -> get("unsubscribeText")
        ];

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
            "params" => $opts["params"]
        ]);

        if ($opts["longText"])
        {
            $this->subscriberCountRenderer = new MSubscriberCount(
                $opts["longText"], $opts["branded"], "horizontal"
            );
        }
    }

    public static function fromData($data, $count = "", $branded = true)
    {
        return new self([
            "branded" => $branded,
            "longText" => $count,
            "shortText" => $count,
            "isSubscribed" => $data -> subscribed ?? false,
            "channelExternalId" => $data -> channelId ?? "",
            "params" => $data -> onSubscribeEndpoints[0] -> subscribeEndpoint -> params ?? null,
            "subscribeText" => TemplateFunctions::getText($data -> unsubscribedButtonText),
            "subscribedText" => TemplateFunctions::getText($data -> subscribedButtonText),
            "unsubscribeText" => TemplateFunctions::getText($data -> unsubscribeButtonText)
        ]);
    }

    public static function buildMock($branded = true)
    {
        return new self([
            "isDisabled" => true,
            "isSubscribed" => false,
            "longText" => "",
            "shortText" => "",
            "branded" => $branded
        ]);
    }
}