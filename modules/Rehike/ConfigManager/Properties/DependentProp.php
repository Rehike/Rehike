<?php
namespace Rehike\ConfigManager\Properties;

/**
 * Creates a property that only shows up in the configuration GUI if another
 * property is set.
 * 
 * Note that this has no effect on loading or the configuration API, only the
 * configuration GUI is affected by this.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class DependentProp extends AbstractConfigProperty
{
    private string $condition;
    private AbstractAssociativeProp $prop;

    public function __construct(
        string $condition,
        AbstractAssociativeProp $prop
    )
    {
        $this->condition = $condition;
        $this->prop = $prop;
    }

    public function getType(): string
    {
        return self::class;
    }

    public function getCondition(): string
    {
        return $this->condition;
    }

    public function getProp(): AbstractAssociativeProp
    {
        return $this->prop;
    }
}