<?php
namespace Rehike;

class Main
{
    public static function main()
    {
        require "resourceConstants.php"; // Todo: clean this up

        Yt::registerTemplateRoot("template/hitchhiker");
        Yt\TemplateController::init();
        Yt::setTemplate("home");
        Yt::renderToPageBuffer();
        Yt::pushPage();
    }
}