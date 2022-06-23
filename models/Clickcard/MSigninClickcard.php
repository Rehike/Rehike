<?php
namespace Rehike\Model\Clickcard;

use Rehike\Model\Common\MAbstractClickcard;

/**
 * Implements a common model for the signin clickcard.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MSigninClickcard extends MAbstractClickcard
{
    public $template = "signin_clickcard";
    public $class = "signin-clickcard";

    public function __construct($heading, $message)
    {
        $this->content = (object)[
            "heading" => $heading,
            "message" => $message
        ];
    }
}