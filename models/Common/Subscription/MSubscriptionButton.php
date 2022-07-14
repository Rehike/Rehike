<?php
namespace Rehike\Model\Common\Subscription;

use Rehike\Model\Common\MButton;

/**
 * Implements a model for the subscription button.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MSubscriptionButton extends MButton
{
    /** @var string */
    public $style = "";

    /** @var bool */
    public $hasIcon = true;

    /** @var bool */
    public $noIconMarkup = true;

    /** @var string[] */
    public $class = [
        "yt-uix-subscription-button",
        "yt-can-buffer",
        "yt-uix-servicelink",
        "vve-check"
    ];

    /** @var string[] */
    public $accessibilityAttributes = [
        "live" => "polite",
        "busy" => "false"
    ];

    /** @var string[] */
    public $attributes = [
        "subscribed-timestamp" => "0",
        "style-type" => "", // branded/unbranded
        "clicktracking" => "",
        "servicelink" => "",
        "visibility-tracking" => "",
        "show-unsub-confirm-time-frame" => "always",
        "channel-external-id" => ""
    ];

    /** @var bool */
    public $disabled;

    /** @var bool */
    public $subscribed;

    /** @var bool */
    public $branded;

    /** @var string */
    public $type;

    public function __construct($opts)
    {
        parent::__construct([]);

        // Default options
        $opts += [
            "isDisabled" => false,
            "isSubscribed" => false,
            "type" => "FREE",
            "branded" => "true",
            "channelExternalId" => ""
        ];

        $this->disabled = $opts["isDisabled"];
        $this->branded = $opts["branded"];
        $this->subscribed = $opts["isSubscribed"];
        $this->unsubText = $opts["unsubText"];
        
        $this->type = $opts["type"];
        $this->attributes["channel-external-id"] = $opts["channelExternalId"];

        if ($this->subscribed) {
            $this->style .= "subscribed";
            $this->class[] = "hover-enabled";
            $this->attributes += ["is-subscribed" => "True"];
        } else {
            $this->style .= "subscribe";
        }
        
        if ($this->branded)
        {
            $this->style .= "-branded";
            $this->attributes["style-type"] = "branded";
        }
        else
        {
            $this->style .= "-unbranded";
            $this->attributes["style-type"] = "unbranded";
        }

        // TODO (kirasicecreamm): if logged out here
        // $this->attributes["href"] = "https://accounts.google.com/ServiceLogin?hl=en&service=youtube&uilel=3&continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Fcontinue_action%3DQUFFLUhqbm5YUkxYSGRlWHphMjAwczlsLTBlcUFzTmpnQXxBQ3Jtc0trV2hlS1FyeWExa3hJQWtuRTB5TXEyckFwVGNuajAwZU5UWXZzM0ZRR0F5X1hISm8ybmczbUdqQkp6VGExTEhrRXdLOG94NmRlbWhYQ3FQcjRiSHFNbkhWV0dBZHdyNzJ3LW9PRFcwd21sQ0dWY05OemRFZV9hZUo2TGFlY0pjaXAyMEp2aEFPcEVBSHktU3d0dEdhdy1JaWhFUU1SWVVKcm9OUGNjSHc2Sm4yZ2t2Rmx4V2NOTm1MT1NQX1lLaEZ2RjZCMGk%253D%26feature%3Dsubscribe%26action_handle_signin%3Dtrue%26next%3D%252Fchannel%252FUCuAXFkgsw1L7xaCfnd5JJOw%26hl%3Den%26app%3Ddesktop&passive=true";

        // i18n:
        $this->content = (object)["runs" => [
            (object)[
                "text" => "Subscribe",
                "class" => "subscribe-label"
            ],
            (object)[
                "text" => "Subscribed",
                "class" => "subscribed-label"
            ],
            (object)[
                "text" => "Unsubscribe",
                "class" => "unsubscribe-label"
            ]
        ]];
    }
}