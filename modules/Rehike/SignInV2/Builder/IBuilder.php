<?php
namespace Rehike\SignInV2\Builder;

use Rehike\SignInV2\Info\IBuiltObject;

/**
 * 
 */
interface IBuilder
{
    public function build(): IBuiltObject;
}