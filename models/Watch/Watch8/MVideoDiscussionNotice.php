<?php
namespace Rehike\Model\Watch\Watch8;

/**
 * Implements a model for a notice that displays in
 * place of a video discussion render, such as in the event
 * of disabled comments.
 * 
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