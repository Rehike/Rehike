<?php
namespace Rehike\Nepeta;

/**
 * API for Nepeta themes.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class NepetaTheme
{
    private string $templatesPath;

    public function __construct(string $templatesPath)
    {
        $this->templatesPath = $templatesPath;
    }

    public function getTemplatesPath(): string
    {
        return $this->templatesPath;
    }
}