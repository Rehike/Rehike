<?php
namespace Rehike\Controller\core;

use Rehike\TemplateManager;
use SpfPhp\SpfPhp;

abstract class HitchhikerController
{
    protected $yt;
    public $template = "";

    protected $spfIdListeners = [
        'player-unavailable<class>',
        'alerts',
        'content',
        '@page<class>',
        'player-playlist<class>',
        '@player<class>'
    ];

    public function get(&$yt, &$template, $request)
    {
        $this->yt = &$yt;
        $this->init($yt, $template);

        $this->onGet($yt, $request);

        $this->postInit($yt, $template);

        $this->doGeneralRender();
    }

    abstract public function onGet(&$yt, $request);

    protected function init(&$yt, &$template)
    {
        $yt->spfEnabled = false;
        $yt->useModularCore = false;
        $yt->page = (object)[];
    }

    public function postInit(&$yt, &$template)
    {
        $template = $this->template;
    }

    public function doGeneralRender()
    {
        \Rehike\Debugger\Debugger::expose();

        $capturedRender = TemplateManager::render();

        if (SpfPhp::isSpfRequested() && $this->yt->spfEnabled)
        {
            $spf = @SpfPhp::parse($capturedRender, $this->spfIdListeners, [
                "skipSerialization" => true
            ]);

            // Post-data generation callback for custom handling
            $this->handleSpfData($spf);

            header("Content-Type: application/json");

            echo json_encode($spf);
        }
        else
        {
            echo $capturedRender;
        }
    }

    /**
     * Modify generated SPF data before it's sent to the client.
     * 
     * For example, adding custom metadata to the response.
     * 
     * @param object $data reference
     * @return void
     */
    public function handleSpfData(&$data) {}
}