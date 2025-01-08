<?php
namespace Rehike;

use Twig\TwigFunction, Twig\TwigFilter;
use Rehike\ControllerV2\Core as ControllerV2;

/**
 * Implements the template manager.
 * 
 * This manages Twig and provides an API for interacting with its
 * bound instance.
 * 
 * It generally exists just to get the Twig instance away from the global
 * scope for code safety.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class TemplateManager
{
    // Must be an reference to the global Twig instance.
    public static \Twig\Environment $twig;

    // Must be a reference to the global context variable.
    public static YtApp $yt;

    public static function __initStatic()
    {
        $defaultViewsDir = Constants\VIEWS_DIR;

        $fileSystemLoader = new \Twig\Loader\FilesystemLoader();
        
        $templateDirs = [];

        // Add default (no extensions) template paths:
        $fileSystemLoader->addPath(
            $_SERVER["DOCUMENT_ROOT"] . "/" . $defaultViewsDir,
            "rehike"
        );

        $fileSystemLoader->addPath(
            $_SERVER["DOCUMENT_ROOT"] . "/" . $defaultViewsDir
        );

        self::$twig = new \Twig\Environment(
            $fileSystemLoader,
            [
                "cache" => $_SERVER["DOCUMENT_ROOT"] . "/cache/templates",
                "auto_reload" => true
            ]
        );
    }

    /**
     * Register the global state variable ($yt)
     * 
     * @param object $yt (reference)
     * @return void
     */
    public static function registerGlobalState(YtApp $yt): void
    {
        self::$yt = $yt;
        self::addGlobal("yt", $yt);
    }

    /**
     * Render the template.
     * 
     * @param array $vars to additionally pass
     */
    public static function render(array $vars = [], string $template = ""): string
    {
        if (!is_array($vars) && !is_null($vars)) throw new \Exception("\$vars must be an array.");

        $passedVars = [self::$yt] + $vars;

        if ("" == $template) $template = self::$template;

        return self::$twig->render("$template.twig", $passedVars);
    }

    /**
     * Add a global variable to the templater.
     */
    public static function addGlobal(string $name, mixed &$value): void
    {
        self::$twig->addGlobal($name, $value);
    }

    /**
     * Add a function to the templater.
     */
    public static function addFunction(
            string|TwigFunction $name, 
            ?callable $callback = null
    ): void
    {
        // $name here is a TwigFunction instance, this ordinarily
        // allows creating TwigFunctions on the fly instead.
        if ($name instanceof TwigFunction)
        {
            self::$twig->addFunction($name);
        }

        self::$twig->addFunction(
            new TwigFunction($name, $callback)
        );
    }

    /**
     * Add a filter to the templater.
     */
    public static function addFilter(
            string|TwigFilter $name, 
            ?callable $callback = null
    ): void
    {
        // $name here is a TwigFilter instance, ditto above
        if ($name instanceof TwigFilter)
        {
            self::$twig->addFilter($name);
        }

        self::$twig->addFilter(
            new TwigFilter($name, $callback)
        );
    }
}