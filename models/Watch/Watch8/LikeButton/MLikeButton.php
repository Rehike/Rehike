<?php
namespace Rehike\Model\Watch\Watch8\LikeButton;

use Rehike\Model\Clickcard\MSigninClickcard;
use Rehike\Signin\API as SignIn;
use Rehike\i18n\i18n;

/**
 * Define the like button.
 */
class MLikeButton extends MAbstractLikeButton
{
    public function __construct($likeCount, $a11y, $isLiked, $videoId, $active = false)
    {
        $i18n = i18n::getNamespace("watch");

        $this->accessibility = (object) [
            "accessibilityData" => (object) [
                "label" => $a11y
            ]
        ];

        $this->tooltip = $i18n->get("actionLikeTooltip");
        
        if ($active)
            $this->tooltip = $i18n->get("actionLikeTooltipActive");

        $signinMessage = $i18n->get("likeClickcardHeading");
        $signinDetail = $i18n->get("voteClickcardTip");

        // Store a reference to the current sign in state.
        $signedIn = SignIn::isSignedIn();

        if ($signedIn)
        {
            $this->attributes["post-action"] = "/service_ajax?name=likeEndpoint";
            $this->class[] = "yt-uix-post-anchor";
        }

        if (!$signedIn && !$active)
        {
            $this->clickcard = new MSigninClickcard($signinMessage, $signinDetail, [
                "text" => $i18n->get("clickcardSignIn"),
                "href" => "https://accounts.google.com/ServiceLogin?continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fnext%3D%252F%253Faction_handle_signin%3Dtrue%26feature%3D__FEATURE__%26hl%3Den%26app%3Ddesktop&passive=true&hl=en&uilel=3&service=youtube"
            ]);
        }
        else if ($signedIn && !$active)
        {
            $this->attributes["post-data"] = "action=like&id=" . $videoId;
        }
        else if ($signedIn && $active)
        {
            $this->attributes["post-data"] = "action=removelike&id=" . $videoId;
        }

        parent::__construct("like-button", $active, $likeCount, $isLiked);
    }
}