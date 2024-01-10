<?php
namespace Rehike\ConfigManager\Properties;

use Iterator;

/**
 * Represents a list of grouped properties, which have a semantic association.
 * 
 * Note that this has no effect on loading or the configuration API, only the
 * configuration GUI is affected by this.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class PropGroup extends AbstractConfigProperty implements Iterator
{
    private array $items;
    private int $currentItem = 0;

    public function __construct(AbstractConfigProperty ...$items)
    {
        $this->items = $items;
    }

    public function getProperties(): array
    {
        return $this->items;
    }

    public function getType(): string
    {
        return self::class;
    }

    public function current(): AbstractConfigProperty
    {
        return $this->items[$this->currentItem];
    }
    
    public function key(): int
    {
        return $this->currentItem;
    }

    public function rewind(): void
    {
        $this->currentItem = 0;
    }

    public function next(): void
    {
        $this->currentItem++;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->currentItem]);
    }
}