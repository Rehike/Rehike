<?php
namespace Rehike\Model\Watch\AgeGate;

/**
 * Content class for the Content Gate.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MPlayerContentGateContent
{
    public object $message;
    public $button;

    public function __construct(?object $data = null)
    {
        $this->message = $data->subreason;

        // We can reuse the age gate button class fine.
        $this->button = MPlayerAgeGateButton::constructFromYti($data);
    }
}