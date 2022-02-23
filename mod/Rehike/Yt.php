<?php
namespace Rehike;

class Yt
{
    public static $visitorData = "";
    public static $pageBuffer;
    public static $pageContentType = "text/html";

    public static function __toObject()
    {
        $yt = (object)[];
        $yt->page = Yt\PageController::$page;
        return $yt;
    }

    public static function setVisitorData($visitorData)
    {
        self::$visitorData = $visitorData;
    }

    public static function registerTemplateRoot($root)
    {
        Yt\TemplateController::setRoot($root);
    }

    public static function setTemplate($template)
    {
        Yt\TemplateController::setTemplate($template);
    }

    public static function setPageContentType($contentType)
    {
        self::$pageContentType = $contentType;
    }

    public static function useHTML()
    {
        self::setPageContentType("text/html");
    }

    public static function useJSON()
    {
        self::setPageContentType("application/json");
    }

    public static function useXML()
    {
        self::setPageContentType("application/xml");
    }

    public static function renderToPageBuffer()
    {
        self::$pageBuffer = Yt\TemplateController::render();
    }

    public static function setPageBuffer($buffer)
    {
        self::$pageBuffer = $buffer;
    }

    public static function concatPageBuffer($append)
    {
        self::setPageBuffer(self::$pageBuffer . $append);
    }

    public static function pushPage()
    {
        header("Content-Type: " . self::$pageContentType);
        echo self::$pageBuffer;
        ob_end_flush();
    }
}