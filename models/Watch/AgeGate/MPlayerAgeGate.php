<?php
namespace Rehike\Model\Watch\AgeGate;

/**
 * Represents the age gate screen for content which is age restricted.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
class MPlayerAgeGate
{
    public $reason;
    public $subreason;

    public function __construct(?object $data = null)
    {
        $reason = "Content warning";
        
        $this->reason = (object)["simpleText" => $reason];
        $this->subreason = (object)["watch7PlayerAgeGateContent" => new MPlayerAgeGateContent($data)];
    }
}