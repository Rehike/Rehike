<?php
namespace Rehike\Model\Appbar;

/**
 * Model for a channel owner link in an appbar navigation section.
 * 
 * @author The Rehike Maintainers
 */
class MAppbarNavOwner
{
    public string $title;
    public string $href;
    public string $thumbnail;

    public function __construct(string $title, string $href, string $thumbnail)
    {
        $this->title = $title;
        $this->href = $href;
        $this->thumbnail = $thumbnail;
    }
}