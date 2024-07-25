<?php
namespace Rehike\Model\Watch\AgeGate;

use Rehike\i18n\i18n;

/**
 * Content class for the Age Gate screen.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MPlayerAgeGateContent
{
    public $message;
    public $button;

    public function __construct(?object $data = null)
    {
        $message = i18n::getRawString(
            "watch",
            "playerBlockadeInappropriate"
        );
        
        $this->message = $message;

        if (isset($data))
        {
            $this->button = MPlayerAgeGateButton::constructFromYti($data);
        }
        else
        {
            $this->button = MPlayerAgeGateButton::constructSignedOut();
        }
    }
}