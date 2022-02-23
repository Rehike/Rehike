<?php
namespace Rehike;

class Main
{
    public static function main()
    {
        require "resourceConstants.php"; // Todo: clean this up

        Yt::registerTemplateRoot("template/hitchhiker");
        Yt\TemplateController::init();
        //Yt::setTemplate("home");

        Yt\PageController::loadPageByFile("homepage.php");

        Yt\TemplateController::addGlobal("yt", Yt::__toObject());
        Yt::renderToPageBuffer();
        Yt\PageController::callPostRenderCallback();

        // Init SPF
        if (Yt\SpfController::shouldUseSpf())
        {
            Yt\SpfController::initUseSpf();

            $ids = [];
            if (isset(Yt\PageController::$page->spfIdListeners))
            {
                $ids = Yt\PageController::$page->spfIdListeners;
            }

            Yt\SpfController::registerIdListeners($ids);

            Yt::setPageBuffer(
                Yt\SpfController::renderSpf(Yt::$pageBuffer)
            );
        }

        Yt::pushPage();
    }
}