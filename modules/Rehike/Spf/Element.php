<?php
namespace Rehike\Spf;

/**
 * Stores information about an SPF element.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Element
{
    /**
     * The ID of the element.
     */
    private string $id;

    /**
     * A map of element attributes to be used by the SPF context or converted
     * to HTML.
     */
    private array $attributes = [];

    /**
     * The name of the template to use for the SPF element, if applicable.
     */
    private string $templateName = "";

    /**
     * The name of the block to request for the SPF element, if applicable.
     */
    private string $blockName = "";

    /**
     * Determines if the source of the HTML data for the SPF element should come
     * from a Twig block rather than an independent template.
     */
    private bool $isBlockBound_ = false;

    /**
     * Do not construct directly. Use Spf::createElement.
     */
    public function __construct(
            string $id,
            string $name, 
            bool $isBlockBound = false
    )
    {
        $this->id = $id;

        if ($isBlockBound)
        {
            $this->blockName = $name;
            $this->isBlockBound_ = true;
        }
        else
        {
            $this->templateName = $name;
        }
    }

    /**
     * Determine whether or not the template is bound to a block.
     * 
     * A block-bound Element is one which sources its HTML data from a Twig
     * block, rather than a separate Twig template. This is a useful hack for
     * certain cases in Rehike's source code.
     */
    public function isBlockBound(): bool
    {
        return $this->isBlockBound_;
    }

    /**
     * Get the name of the Twig template or block name to be used for retrieving
     * HTML data.
     */
    public function getTemplateName(): string
    {
        return $this->isBlockBound_ 
            ? $this->templateName
            : $this->blockName;
    }

    /**
     * Set the name of the Twig template or block name to be used for retrieving
     * HTML data.
     */
    public function setTemplateName(string $newName): void
    {
        if ($this->isBlockBound_)
            $this->blockName = $newName;
        else
            $this->templateName = $newName;
    }

    /**
     * Get an element attribute by its name.
     * 
     * If the attribute does not exist, then this will return null.
     */
    public function getAttribute(string $name): ?string
    {
        if (isset($this->attributes[$name]))
        {
            return $this->attributes[$name];
        }
        
        return null;
    }

    /**
     * Set an attribute's value.
     */
    public function setAttribute(string $name, string $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Remove an attribute by its name.
     */
    public function removeAttribute(string $name): void
    {
        if (isset($this->attributes[$name]))
        {
            unset($this->attributes[$name]);
        }
    }

    /**
     * Get a list of all attributes specified on this element.
     */
    public function getAllAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Serializes the attributes of this element into HTML markup.
     */
    public function serializeHtmlAttributes(): string
    {
        $out = "\"id\"=\"$this->id\" ";

        foreach ($this->attributes as $name => $value)
        {
            $out .= "\"" . htmlspecialchars($name) . "\"" . "=" .
                "\"" . htmlspecialchars($value) . "\"" . " ";
        }

        return rtrim($out);
    }

    /**
     * Handles Twig aliases.
     */
    public function __get(string $propName): mixed
    {
        if ("attributes" == $propName)
        {
            return $this->serializeHtmlAttributes();
        }

        return null;
    }
}