<?php
namespace Rehike\TemplateUtilsDelegate;

/**
 * A wrapper class which denotes safe HTML input from Twig templates.
 * 
 * @author Pumpkin <pumpkinpielemon@gmail.com>
 */
class SafeHtml
{
    public string $html;

    public function __construct(string $html)
    {
        $this->html = $html;
    }
}