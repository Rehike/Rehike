<?php
namespace Rehike\Model\Watch\Watch8\LikeButton;

use Rehike\Model\Clickcard\MSigninClickcard;
use Rehike\Signin\API as SignIn;
use Rehike\i18n\i18n;

/**
 * Define the dislike button.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
class MDislikeButton extends MAbstractLikeButton
{
    public function __construct($dislikeCount, $a11y, $isDisliked, $videoId, $active = false)
    {
        $i18n = i18n::getNamespace("watch");

        $this->accessibilityAttributes = [
            "label" => $a11y
        ];

        $this->tooltip = $i18n->get("actionDislikeTooltip");

        $signinMessage = $i18n->get("dislikeClickcardHeading");
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
            $this->attributes["post-data"] = "action=dislike&id=" . $videoId;
        }
        else if ($signedIn && $active)
        {
            $this->attributes["post-data"] = "action=removedislike&id=" . $videoId;
        }

        parent::__construct("dislike-button", $active, $dislikeCount, $isDisliked);
    }
}