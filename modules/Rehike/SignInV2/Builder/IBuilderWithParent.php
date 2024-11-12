<?php
namespace Rehike\SignInV2\Builder;

use Rehike\SignInV2\Info\IBuiltObject;

/**
 * Represents a builder with a parent builder.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
interface IBuilderWithParent
{
    /**
     * Get the finalized parent of the builder.
     */
    public function getFinalizedParent(): IBuiltObject;
    
    /**
     * Set the finalized parent of the builder.
     */
    public function setFinalizedParent(IBuiltObject $parent): void;
}