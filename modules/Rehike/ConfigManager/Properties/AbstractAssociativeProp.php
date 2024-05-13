<?php
namespace Rehike\ConfigManager\Properties;

use Closure;

/**
 * Defines a base for a configuration property.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class AbstractAssociativeProp extends AbstractConfigProperty
{
    protected ?Closure $onUpdateHandler = null;
    
    abstract public function getDefaultValue(): mixed;
    abstract public function getType(): string;
    
    /**
     * Registers an update callback.
     */
    public function registerUpdateCb(callable|Closure $onUpdate): static
    {
        if (!$onUpdate instanceof Closure)
        {
            $this->onUpdateHandler = Closure::fromCallable($onUpdate);
        }
        else
        {
            $this->onUpdateHandler = $onUpdate;
        }
        
        return $this;
    }
    
    /**
     * Method to be called whenever the configuration property changes.
     */
    public function onUpdate(): mixed
    {
        if ($this->onUpdateHandler)
        {
            return ($this->onUpdateHandler)();
        }
        
        return null;
    }
}