<?php
namespace Rehike\Model\Appbar;

/**
 * Model for an item in an appbar navigation section.
 * 
 * @author The Rehike Maintainers
 */
class MAppbarNavItem
{
    public string $title;
    public string $href;
    public int $status = self::StatusUnselected;

    const StatusUnselected = 0;
    const StatusPartiallySelected = 1;
    const StatusSelected = 2;

    public function __construct(string $title, string $href, int $status = self::StatusUnselected)
    {
        $this->title = $title;
        $this->href = $href;
        $this->status = $status;
    }
}