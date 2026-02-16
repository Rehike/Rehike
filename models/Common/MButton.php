<?php
namespace Rehike\Model\Common;

/**
 * Implements the common button model
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
#[\AllowDynamicProperties]
class MButton
{
    /**
     * Specifies the style of the button.
     */
    public string $style = "STYLE_DEFAULT";

    /**
     * Specifies the size of the button.
     */
    public string $size = "SIZE_DEFAULT";

    /**
     * Specifies the icon of the button.
     * 
     * @var object|bool|null
     *      This can be an InnerTube icon object, a boolean value where true
     *      specifies that an icon should be used (in case the icon is specified
     *      by a class, as is the case with playlist header icons), or null.
     */
    public object|bool|null $icon = null;
    
    /**
     * A string specifying a tooltip to be displayed when the user hovers the
     * button.
     */
    public ?string $tooltip = null;

    /**
     * An array of strings comprising the class of the button element.
     * 
     * @var string[]
     */
    public array $class = [];

    /**
     * An associative array of additional attributes to apply to the button
     * element in HTML.
     * 
     * @var array<string, string>
     */
    public array $attributes = [];

    /**
     * An object specifying InnerTube accessibility data.
     */
    public ?object $accessibility = null;

    /**
     * A flag representing if the button is disabled.
     */
    public bool $isDisabled = false;

    public function __construct($array = [])
    {
        $this->text = (object)["runs" => []];

        foreach ($array as $key => $value)
        {
            $this->{$key} = $value;
        }
    }

    protected function setText($string)
    {
        $this->text = (object)[
            "runs" => [(object)[
                "text" => $string
            ]]
        ];
    }

    protected function addRun($object)
    {
        $this->text->runs[] = $object;
    }
}