<?php
namespace Rehike\Model\Watch\AgeGate;

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