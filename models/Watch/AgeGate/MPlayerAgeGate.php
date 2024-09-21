<?php
namespace Rehike\Model\Watch\AgeGate;

use Rehike\i18n\i18n;
use Rehike\Util\ParsingUtils;

/**
 * Represents the age gate screen for content which is age restricted.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MPlayerAgeGate
{
    public $reason;
    public $subreason;

    public function __construct(?object $data = null)
    {
        $reason = i18n::getRawString("watch", "playerBlockadeContentWarning");
        
        if (isset($data->reason))
        {
            $reason = ParsingUtils::getText($data->reason);
        }
        
        $this->reason = (object)["simpleText" => $reason];
        $this->subreason = (object)["watch7PlayerAgeGateContent" => new MPlayerAgeGateContent($data)];
    }
}