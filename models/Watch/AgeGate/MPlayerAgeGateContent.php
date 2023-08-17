<?php
namespace Rehike\Model\Watch\AgeGate;

/**
 * Content class for the Age Gate screen.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
class MPlayerAgeGateContent
{
    public $message;
    public $button;

    public function __construct(?object $data = null)
    {
        $message = "This video may be inappropriate for some users.";
        
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