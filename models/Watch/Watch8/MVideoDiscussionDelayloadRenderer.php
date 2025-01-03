<?php
namespace Rehike\Model\Watch\Watch8;

/**
 * Implements a model for the video discussion renderer when delayloaded.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MVideoDiscussionDelayloadRenderer
{
    public $continuation;

    public function __construct($continuation)
    {
        $this->continuation = $continuation;
    }
}