<?php
namespace Rehike\Model\Rehike\Debugger;

use Rehike\Debugger\ErrorWrapper;

/**
 * Implements the error tab.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class MErrorTab extends MTabContent
{
    public function __construct() {}

    /**
     * Push a list of errors from the debugger's main API to the error tab.
     * 
     * @param ErrorWrapper[] $errors
     * @return void
     */
    public function pushErrors($errors)
    {
        $errorCount = count($errors);

        if ($errorCount != 0)
        {
            foreach ($errors as $error)
            {
                $this->addError($error);
            }
        }
        else
        {
            $this->addNothingToSee();
        }
    }
}