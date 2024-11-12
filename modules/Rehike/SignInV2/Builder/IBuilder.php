<?php
namespace Rehike\SignInV2\Builder;

use Rehike\SignInV2\Info\IBuiltObject;

/**
 * Represents a builder object.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
interface IBuilder
{
    public function build(): IBuiltObject;
}