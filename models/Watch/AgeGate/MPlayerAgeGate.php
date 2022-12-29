<?php
namespace Rehike\Model\Watch\AgeGate;

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