<?php
namespace Rehike\Model\Rehike\Config;

use Rehike\ConfigDefinitions;
use Rehike\ConfigManager\Config;
use Rehike\ConfigManager\Properties\{
    AbstractAssociativeProp,
    AbstractConfigProperty,
    DependentProp,
    BoolProp,
    EnumProp,
    PropGroup
};
use Rehike\i18n\i18n;

/**
 * Bakes the contents of the configuration model.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ConfigModelContents
{
    private array $defs;
    private string $page = "";
    private array $result = [];
    private bool $hasBaked = false;

    public function __construct()
    {
        $this->defs = ConfigDefinitions::getConfigDefinitions();
    }

    /**
     * Sets the page filter.
     * 
     * When set, the contents of the result will be limited to a specific
     * category.
     */
    public function setPage(string $id): void
    {
        if ($this->hasBaked)
        {
            throw new \Exception(
                "ConfigModelContents::\$page must be set before baking."
            );
        }

        $this->page = $id;
    }

    /**
     * Bakes the page model.
     */
    public function bake(): void
    {
        if ($this->hasBaked)
        {
            throw new \Exception(
                "ConfigModelContents has already baked, cannot bake again"
            );
        }

        $page = $this->defs;
        $id = $this->page;
        if (!empty($this->page))
        {
            if (isset($this->defs[$id]))
            {
                $page = $this->defs[$id];
            }
        }

        $this->bakePropRootRenderer($this->result, $page, $id);
        $this->hasBaked = true;
    }

    /**
     * Gets the result of bake().
     */
    public function getResult(): array
    {
        if (!$this->hasBaked)
        {
            throw new \Exception(
                "ConfigModelContents has not baked yet, contents are unavailable."
            );
        }

        return $this->result;
    }

    /**
     * Adds to the end of a path with "." for path separation.
     */
    private function concatPath(string $path, string $newPath): string
    {
        if ("" != $path)
        {
            return $path . "." . $newPath;
        }
        
        return $newPath;
    }

    /**
     * Bakes the root of a property renderer, such as $contents.
     */
    private function bakePropRootRenderer(
            array &$target,
            array $root,
            string $path
    ): void
    {
        foreach ($root as $name => $def)
        {
            if (is_array($def))
            {
                $this->bakePropRootRenderer(
                    $target, 
                    $def, 
                    $this->concatPath($path, $name)
                );
            }
            else if ($def instanceof PropGroup)
            {
                $result = $this->getGroupRendererTemplate();
                $this->bakePropRootRenderer(
                    $result->propertyGroupRenderer->contents, 
                    $def->getProperties(), 
                    $path
                );
                $target[] = $result;
            }
            else if ($def instanceof DependentProp)
            {
                // Dependent properties are just wrapped associative properties,
                // so they're accessed in much of the same way. We only wrap them
                // in the model because it's a little more convenient to implement.
                $result = $this->getDependentPropRendererTemplate();
                $result->dependentPropertyRenderer->condition =
                    $def->getCondition();
                $result->dependentPropertyRenderer->content =
                    $this->bakeAssociativePropRenderer(
                        [$name => $def->getProp()],
                        $path,
                        $name
                    );
                $target[] = $result;
            }
            else
            {
                $target[] = $this->bakeAssociativePropRenderer(
                    $root,
                    $path,
                    $name
                );
            }
        }
    }

    /**
     * Bakes the models for associative properties, which are those which
     * correspond a name to a value rather than being used for markup reasons.
     */
    private function bakeAssociativePropRenderer(
            array $root,
            string $path,
            string $name
    ): object
    {
        $prop = $root[$name];

        $params = new AssociativePropParams;
        $params->name = $name;
        $params->path = $this->concatPath($path, $name);
        $params->source = $prop;

        if (!$prop)
        {
            trigger_error("Fuck", E_USER_WARNING);
        }

        if ($prop instanceof EnumProp)
        {
            return $this->bakeEnumPropRenderer($params);
        }
        else if ($prop instanceof BoolProp)
        {
            return $this->bakeBoolPropRenderer($params);
        }
        else
        {
            // Unsupported.
            return (object)[];
        }
    }

    /**
     * dependentPropertyRenderer template
     */
    private function getDependentPropRendererTemplate(): object
    {
        return (object)[
            "dependentPropertyRenderer" => (object)[
                "condition" => null,
                "content" => null
            ]
        ];
    }

    /**
     * propertyGroupRenderer template
     */
    private function getGroupRendererTemplate(): object
    {
        return (object)[
            "propertyGroupRenderer" => (object)[
                "contents" => []
            ]
        ];
    }

    /**
     * checkboxRenderer baker
     */
    private function bakeBoolPropRenderer(AssociativePropParams $params): object
    {
        $i18n = $this->getI18nInfo($params->path);
        $value = $this->getPropValue($params->path);

        $title = $params->name;
        if (isset($i18n->title))
        {
            $title = $i18n->title;
        }

        $subtitle = null;
        if (isset($i18n->subtitle))
        {
            $subtitle = $i18n->subtitle;
        }

        return (object) [
            "checkboxRenderer" => (object) [
                "title" => $title,
                "subtitle" => $subtitle,
                "checked" => $value ? true : false,
                "name" => $params->path,
            ]
        ];
    }

    /**
     * selectRenderer baker
     */
    private function bakeEnumPropRenderer(AssociativePropParams $params): object
    {
        $i18n = $this->getI18nInfo($params->path);
        $value = $this->getPropValue($params->path);

        $title = $params->name;
        if (isset($i18n->title))
        {
            $title = $i18n->title;
        }

        $subtitle = null;
        if (isset($i18n->subtitle))
        {
            $subtitle = $i18n->subtitle;
        }

        $values = [];
        $selectedValue = null;

        /** @var EnumProp */
        $prop = $params->source;

        foreach ($prop->getValidValues() as $enumValue)
        {
            $text = $enumValue;
            if (isset($i18n->values->{$enumValue}))
            {
                $text = $i18n->values->{$enumValue};
            }

            $values[] = (object) [
                "text" => $text,
                "value" => $enumValue,
                "selected" => ($value == $enumValue)
            ];

            if ($value == $enumValue) $selectedValue = $value;
        }

        return (object) [
            "selectRenderer" => (object) [
                "label" => $title,
                "name" => $params->path,
                "values" => $values,
                "selectedValue" => $selectedValue
            ]
        ];
    }

    /**
     * Requests translation string information for a given property.
     */
    private function getI18nInfo(string $path): object
    {
        try
        {
            $root = i18n::getNamespace("rehike/config")->getAllTemplates();

            $cur = $root->props ?? (object)[];
            $paths = explode(".", $path);

            while (!empty($paths))
            {
                if (isset($cur->{$paths[0]}))
                {
                    $cur = $cur->{$paths[0]};
                    array_splice($paths, 0, 1);
                }
                else
                {
                    return (object)[];
                }
            }

            return $cur;
        }
        catch (\Throwable $e)
        {
            return (object)[];
        }
    }

    /**
     * Gets the currently-selected value of a property path.
     */
    private function getPropValue(string $path): mixed
    {
        return Config::getConfigProp($path);
    }
}