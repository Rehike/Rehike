<?php
namespace Rehike\Controller\core;

use Rehike\ConfigManager\Config;
use Rehike\YtApp;
use Rehike\Spf\Spf;

use Rehike\Model\{
    Appbar\MAppbar,
    Footer\MFooter,
    Masthead\MMasthead
};
use Rehike\Network;

/**
 * Defines a general YouTube Nirvana controller.
 * 
 * This implements the base API and data used to render a Nirvana (Appbar)
 * page.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
 */
abstract class NirvanaController extends HitchhikerController
{
    /**
     * Don't request the guide on initial visit.
     * 
     * This should be true on pages like watch, where the guide
     * isn't open by default.
     */
    protected bool $delayLoadGuide = false;

    /** @inheritdoc */
    protected function init(): void
    {
        $this->yt->spfEnabled = true;
        $this->yt->useModularCore = true;
        $this->yt->modularCoreModules = [];
        $this->yt->appbar = new MAppbar();
        $this->yt->page = (object)[];

        // Nirvana pages support SPF, so player experiments must be initialized
        // in this case in order for videos to play properly on SPF navigation.
        $this->initPlayer();
        
        if (!Spf::isSpfRequested()
            && !Config::getConfigProp("experiments.asyncAttestationRequest"))
        {
            Network::innertubeRequest(
                "att/get",
                [
                    "engagementType" => "ENGAGEMENT_TYPE_UNBOUND",
                ],
            )->then(function ($response) {
                $this->yt->attestation = $response->getJson();
            });
        }

        if ($this->useTemplate)
        {
            $this->yt->masthead = new MMasthead(true);
            $this->yt->footer = new MFooter();
        }
        $this->yt->footer = new MFooter();

        // Request appbar guide fragments if the page has the
        // guide enabled, the request is not SPF, and the guide
        // is open by default.
        if (!$this->delayLoadGuide && !Spf::isSpfRequested())
        {
            $this->getPageGuide()->then(function ($guide) {
                $this->yt->appbar->addGuide($guide);
            });
        }
    }

    /**
     * Define the page to use a JS page module.
     * 
     * @param string $module  Name of the module (not URL)
     * 
     * @return void
     */
    protected function useJsModule(string $module): void
    {
        $this->yt->modularCoreModules[] = $module;
    }
}