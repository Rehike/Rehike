<?php
namespace Rehike\Model\Watch\Watch7;

/**
 * Implements a model for a notice that displays in
 * place of a video discussion render, such as in the event
 * of disabled comments.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MVideoDiscussionNotice
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }
}