<?php
namespace Rehike\Yt;

use Exception;

class TemplateController
{
    public static $templateRoot = "";
    public static $template = "";
    public static $twig;
    public static $twigVars = [];
    public static $queuedFunctions = [];
    public static $queuedGlobals = [];

    public static function setRoot($root)
    {
        self::$templateRoot = $root;
    }

    public static function setTemplate($template)
    {
        self::$template = $template;
    }

    public static function init()
    {
        $twigLoader = new \Twig\Loader\FilesystemLoader(
            self::$templateRoot
        );
        
        self::$twig = new \Twig\Environment($twigLoader, [
            'debug' => true
        ]);

        self::addQueuedFunctions();
        self::addQueuedGlobals();
    }

    public static function doTwigRender()
    {
        try
        {
            return self::$twig->render(
                self::$template . ".twig",
                self::$twigVars
            );
        }
        catch (\Twig\Error\LoaderError $e)
        {
            throw new \Rehike\Exception\RehikeTemplateException("Template " . self::$template . ".twig does not exist");
        }
    }

    public static function render()
    {
        ob_start();
        
        $out = self::doTwigRender();

        ob_get_clean();

        return $out;
    }

    public static function addVariable($variable, $global = false)
    {
        self::$twigVars[] = $variable;
    }

    public static function queueFunction($name, $callback)
    {
        self::$queuedFunctions += [$name => $callback];
    }

    public static function addQueuedFunctions()
    {
        foreach (self::$queuedFunctions as $name => $callback)
        {
            self::addFunction($name, $callback);
        }

        self::$queuedFunctions = [];
    }

    public static function addFunction($name, $callback)
    {
        self::$twig->addFunction(
            new \Twig\TwigFunction($name, $callback)
        );
    }

    public static function queueGlobal($name, $var)
    {
        self::$queuedGlobals += [$name => $var];
    }

    public static function addQueuedGlobals()
    {
        foreach(self::$queuedGlobals as $name => $var) 
        {
            self::addGlobal($name, $var);
        }

        self::$queuedGlobals = [];
    }

    public static function addGlobal($name, $var)
    {
        self::$twig->addGlobal($name, $var);
    }
}