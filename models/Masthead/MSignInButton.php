<?php
namespace Rehike\Model\Masthead;

use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;

class MSignInButton extends MButton {
    public $style = "STYLE_PRIMARY";
    public $href = "https://accounts.google.com/ServiceLogin?service=youtube&amp;uilel=3&amp;hl=en&amp;continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fapp%3Ddesktop%26action_handle_signin%3Dtrue%26hl%3Den%26next%3D%252F%26feature%3Dsign_in_button&amp;passive=true";

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $this->text = (object) [
            "simpleText" => $i18n->get("signInButton")
        ];
    }
}