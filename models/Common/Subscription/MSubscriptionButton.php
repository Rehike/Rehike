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

    /** @var object */
    public $icon;

    /** @var string[] */
    public $class = [
        "yt-uix-subscription-button",
        "yt-can-buffer"
    ];

    /** @var string[] */
    public $accessibility;

    /** @var string[] */
    public $attributes = [
        "subscribed-timestamp" => "0",
        "style-type" => "", // branded/unbranded
        "clicktracking" => "",
        "show-unsub-confirm-dialog" => "true",
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
            "channelExternalId" => "",
            "params" => "",
            "tooltip" => null
        ];
        
        $this->icon = (object) [];

        $this->accessibility = (object) [
            "accessibilityData" => (object) [
                "live" => "polite",
                "busy" => "false"
            ]
        ];

        $this->isDisabled = $opts["isDisabled"];
        $this->branded = $opts["branded"];
        $this->subscribed = $opts["isSubscribed"];

        $this->type = $opts["type"];
        $this->attributes["channel-external-id"] = $opts["channelExternalId"];
        $this->attributes["params"] = $opts["params"];

        $this->tooltip = $opts["tooltip"];

        if ($this->subscribed) {
            $this->style .= "STYLE_SUBSCRIBED";
            $this->class[] = "hover-enabled";
            $this->attributes += ["is-subscribed" => "True"];
        } else {
            $this->style .= "STYLE_SUBSCRIBE";
        }

        if ($this->branded)
        {
            $this->style .= "_BRANDED";
            $this->attributes["style-type"] = "branded";
        }
        else
        {
            $this->style .= "_UNBRANDED";
            $this->attributes["style-type"] = "unbranded";
        }

        if (!is_null($opts["href"])) 
        {
            $this->href = $opts["href"];
        }

        $this->text = (object) ["runs" => [
            (object) [
                "text" => $opts["subscribeText"],
                "class" => "subscribe-label"
            ],
            (object) [
                "text" => $opts["subscribedText"],
                "class" => "subscribed-label"
            ],
            (object) [
                "text" => $opts["unsubscribeText"],
                "class" => "unsubscribe-label"
            ]
        ]];
    }
}