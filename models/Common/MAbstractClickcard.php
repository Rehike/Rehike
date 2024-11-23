<?php
namespace Rehike\Model\Common;

/**
 * Abstract clickcard definitions.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class MAbstractClickcard
{
    public $template = "";
    public $class = "";
    public $content;

    /**
     * Should generate content only. Template and
     * class should be overridden in the child class
     * declaration.
     * 
     * @return void
     */
    public function __construct() {}
}