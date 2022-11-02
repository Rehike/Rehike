<?php
namespace Rehike\Model\Watch;

use Rehike\Model\Common\MButton;

class MPlayerAgeGate
{
    public $reason;
    public $subreason;

    public function __construct()
    {
        $reason = "Content warning";
        
        $this->reason = (object)["simpleText" => $reason];
        $this->subreason = (object)["watch7PlayerAgeGateContent" => new MPlayerAgeGateContent()];
    }
}

class MPlayerAgeGateContent
{
    public $message;
    public $button;

    public function __construct()
    {
        $message = "This video may be inappropriate for some users.";
        
        $this->message = $message;
        $this->button = new MPlayerAgeGateButton();
    }
}

class MPlayerAgeGateButton extends MButton
{
    public $style = "STYLE_PRIMARY";
    public $role = "link";

    public $customAttributes = [
        "href" => "https://accounts.google.com/ServiceLogin?hl=en&service=youtube&uilel=3&continue=http%3A%2F%2Fwww.youtube.com%2Fsignin%3Fcontinue_action%3DQUFFLUhqbm5YUkxYSGRlWHphMjAwczlsLTBlcUFzTmpnQXxBQ3Jtc0trV2hlS1FyeWExa3hJQWtuRTB5TXEyckFwVGNuajAwZU5UWXZzM0ZRR0F5X1hISm8ybmczbUdqQkp6VGExTEhrRXdLOG94NmRlbWhYQ3FQcjRiSHFNbkhWV0dBZHdyNzJ3LW9PRFcwd21sQ0dWY05OemRFZV9hZUo2TGFlY0pjaXAyMEp2aEFPcEVBSHktU3d0dEdhdy1JaWhFUU1SWVVKcm9OUGNjSHc2Sm4yZ2t2Rmx4V2NOTm1MT1NQX1lLaEZ2RjZCMGk%253D%26feature%3Dsubscribe%26action_handle_signin%3Dtrue%26next%3D%252Fchannel%252FUCuAXFkgsw1L7xaCfnd5JJOw%26hl%3Den%26app%3Ddesktop&passive=true"
    ];

    public function __construct()
    {
        $text = "Sign in to confirm your age"; // TODO: i18n

        $this->setText($text);
    }
}