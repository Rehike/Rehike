<?php
namespace Rehike\Yt;

class PageController
{
    const PAGES_DIR = "views";

    public static $page;

    public static function registerPage($page)
    {
        self::$page = $page;
    }

    public static function loadPageByFile($filename)
    {
        $page = include self::PAGES_DIR . "/" . $filename;
        self::registerPage($page);

        self::loadPage();
    }

    public static function loadPage()
    {
        if (!is_object(self::$page)) throw new \Rehike\Exception\RehikeException("Page must be a class.");

        self::$page->_buildPage();
    }

    public static function callPostRenderCallback()
    {
        self::$page->_postRenderCallback();
    }
}