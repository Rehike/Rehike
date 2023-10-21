<?php
namespace YukisCoffee\CoffeeTranslation\Lang;

/**
 * Represents basic source information.
 * 
 * This includes a name (FS path or other identifier), the encoding type of the
 * content, and the source contents themselves.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class SourceInfo
{
    private string $name;
    private string $encodingType;
    private string $contents;

    public function __construct(string $name, string $encodingType, string $contents)
    {
        $this->name = $name;
        $this->encodingType = $encodingType;
        $this->contents = $contents;
    }

    /**
     * Get the name of the file.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the encoding type of the file.
     */
    public function getEncodingType(): string
    {
        return $this->encodingType;
    }

    /**
     * Get the contents of the file.
     */
    public function getContents(): string
    {
        return $this->contents;
    }
}