<?php
namespace Rehike\Model\Watch\AgeGate;

use Rehike\Util\ParsingUtils;

/**
 * Represents the content gate screen for content that is restricted for special
 * reasons (i.e. suicidal topics).
 * 
 * These are handled separately from age-gated content by the YouTube API, and
 * it is easier to handle them as such for Rehike.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MPlayerContentGate
{
    public $reason;
    public $subreason;

    public function __construct(?object $data = null)
    {
        $this->reason = $data->reason;
        $this->subreason = (object)["watch7PlayerAgeGateContent" => new MPlayerContentGateContent($data)];
    }
}