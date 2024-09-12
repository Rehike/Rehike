<?php
namespace Rehike\SignInV2\Builder;

use Rehike\SignInV2\{
    Info\IBuiltObject,
};

use ArrayAccess;
use ArrayIterator;
use Closure;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

/**
 * Collection of builders.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class BuilderCollection implements ArrayAccess, IteratorAggregate
{
    private string $typeRestriction = "";
    
    /**
     * @var IBuilder[]
     */
    private array $items = [];
    
    public function __construct(string $typeRestriction = "")
    {
        $this->typeRestriction = $typeRestriction;
    }
    
    public function push(IBuilder $item): void
    {
        if (!($item instanceof $this->typeRestriction))
        {
            throw new InvalidArgumentException(
                "Argument \$item of push() must be of type $this->typeRestriction."
            );
        }
        
        $this->items[] = $item;
    }
    
    /**
     * Executes a callback for each item in the builder.
     */
    public function forEach(Closure $cb): static
    {
        foreach ($this->items as $item)
        {
            $cb($item);
        }
        
        return $this;
    }
    
    /**
     * Build all items in the collection.
     * 
     * @return IBuiltObject[]
     */
    public function buildAll(): array
    {
        return array_map(fn($item) => $item->build(), $this->items);
    }
    
    //-----------------------------------------------------------------------------------------------------------------
    // implementation of ArrayAccess:
    //
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }
    
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }
    
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!($value instanceof IBuilder))
        {
            throw new InvalidArgumentException("Expected value of type IBuilder for argument \$value.");
        }
        
        $this->items[$offset] = $value;
    }
    
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }
    
    //-----------------------------------------------------------------------------------------------------------------
    // implementation of IteratorAggregate:
    //
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}