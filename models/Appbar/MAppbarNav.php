<?php
namespace Rehike\Model\Appbar;

/**
 * Model for an appbar navigation section.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MAppbarNav
{
    /**
     * All navigation links in the appbar navigation section.
     * 
     * @var MAppbarNavItem[]
     */
    public array $items;
    
    /**
     * The owner channel, or null. 
     */
    public ?MAppbarNavOwner $owner = null;

    /**
     * Add a navigation link item to this appbar navigation section.
     */
    public function addItem(string $title, string $href, int $status): void
    {
        $this->items[] = new MAppbarNavItem($title, $href, $status);
    }

    /**
     * Add an owner channel link to this appbar navigation section.
     */
    public function addOwner(string $title, string $href, string $thumbnail): void
    {
        $this->owner = new MAppbarNavOwner($title, $href, $thumbnail);
    }
}