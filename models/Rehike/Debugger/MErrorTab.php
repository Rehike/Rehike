<?php
namespace Rehike\Model\Rehike\Debugger;

/**
 * Implements the error tab.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class MErrorTab extends MTabContent
{
    public function __construct() {}

    public function pushErrors($errors)
    {
        foreach ($errors as $error)
        {
            $this->addError($error);
        }
    }
}