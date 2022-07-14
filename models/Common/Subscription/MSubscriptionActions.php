<?php
namespace Rehike\Model\Common\Subscription;

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
            "unsubText" => "Unsubscribe?"
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
            "channelExternalId" => $opts["channelExternalId"]
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
            "channelExternalId" => $data -> channelId ?? false
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