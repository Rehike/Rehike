<?php
namespace Rehike\Util\FormattedStringBuilder;

use stdClass;

/**
 * A single run in a formatted string.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class RunBuilder extends stdClass
{
    protected string $text = "";
    protected bool $bold = false;
    protected bool $italic = false;
    protected object $navigationEndpoint;
    
    public function build(): object
    {
        $out = (object)[];
        
        $out->text = $this->text;
        
        if ($this->bold)
        {
            $out->bold = true;
        }
        
        if ($this->italic)
        {
            $out->italic = true;
        }
        
        if (isset($this->navigationEndpoint))
        {
            $out->navigationEndpoint = $this->navigationEndpoint;
        }
        
        return (object)$out;
    }
    
    public function setText(string $text): void
    {
        $this->text = $text;
    }
    
    public function setBold(bool $value): void
    {
        $this->bold = $value;
    }
    
    public function setItalic(bool $value): void
    {
        $this->italic = $value;
    }
    
    public function setEndpointFromUrl(string $url): void
    {
        $this->navigationEndpoint = (object)[
            "urlEndpoint" => (object)[
                "url" => $url
            ],
            "commandMetadata" => (object)[
                "webCommandMetadata" => (object)[
                    "url" => $url
                ]
            ]
        ];
    }
}