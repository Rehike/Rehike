<?php
namespace Rehike\Model\Watch\Watch7;

/**
 * Implements a model for the video discussion renderer.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MVideoDiscussionRenderer
{
    public $continuation;

    public function __construct($continuation)
    {
        $this->continuation = $continuation;
    }
}