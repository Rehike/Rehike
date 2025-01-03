<?php
namespace Rehike\ConfigManager\Properties;

/**
 * Enumerated property.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class EnumProp extends AbstractAssociativeProp
{
    protected string $defaultValue = "";
    protected array $validValues = [];

    public function __construct(string $defaultValue, array $validValues)
    {
        $this->defaultValue = $defaultValue;
        $this->validValues = $validValues;
    }

    /**
     * Get the default value of the property.
     */
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function getValidValues(): array
    {
        return $this->validValues;
    }

    public function getType(): string
    {
        return "enum";
    }
}