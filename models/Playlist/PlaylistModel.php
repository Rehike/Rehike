<?php
namespace Rehike\Model\Playlist;

use \Rehike\Model\Common\MAlert;
use \Rehike\i18n\i18n;
use Rehike\Model\ViewModelConverter\LockupViewModelConverter;
use Rehike\Model\ViewModelConverter\PlaylistHeaderViewModelConverter;
use Rehike\Model\ViewModelConverter\PlaylistVideoRendererViewModelConverter;
use Rehike\YtApp;

class PlaylistModel
{
    public static function bake($dataHost)
    {
        $i18n = i18n::getNamespace("playlist");
        $response = (object) [];

        $contentContainer = $dataHost->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content ?? null;

        if (!isset($contentContainer->sectionListRenderer))
        {
            return (object) [
                "alerts" => [
                    new MAlert([
                        "type" => MAlert::TypeError,
                        "text" => $i18n->get("nonexistent")
                    ])
                ]
            ];
        }

        if ($videoList = @$contentContainer->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->playlistVideoListRenderer->contents)
        {
            // Legacy InnerTube:
            $response->videoList = $videoList;
        }
        else if ($videoList = @$contentContainer->sectionListRenderer->contents[0]->itemSectionRenderer->contents)
        {
            // 2026-06: Viewmodel-based renderers:
            $response->videoList = [];
            
            foreach ($videoList as $listItem)
            {
                if (isset($listItem->lockupViewModel))
                {
                    $vmc = new PlaylistVideoRendererViewModelConverter(
                        $listItem->lockupViewModel,
                        (object)[],
                    );
                    
                    $response->videoList[] = (object)[
                        "playlistVideoRenderer" => $vmc->bake(),
                    ];
                }
                else
                {
                    $response->videoList[] = $listItem;
                }
            }
        }

        if ($header = @$dataHost->header->playlistHeaderRenderer)
        {
            // Legacy InnerTube:
            $response->header = new MPlaylistHeader($header);
        }
        else if ($header = @$dataHost->header->pageHeaderRenderer)
        {
            // 2026-06: Viewmodel-based renderers:
            $vmc = new PlaylistHeaderViewModelConverter(
                $header->content->pageHeaderViewModel,
                (object)[],
            );
            $vmc->setPlaylistId(YtApp::getInstance()->playlistId);
            
            $response->header = new MPlaylistHeader($vmc->bake());
        }

        
        if (isset($dataHost->alerts)) 
        {
            $response->alerts = [];
            foreach ($dataHost->alerts as $alert)
            {
                $alert = $alert->alertWithButtonRenderer
                      ?? $alert->alertRenderer
                      ?? null;
    
                $response->alerts[] = MAlert::fromData($alert);
            } 
        }

        if ($response == (object) [])
        {
            return (object) [
                "alerts" => [
                    new MAlert([
                        "type" => MAlert::TypeError,
                        "text" => $i18n->get("unsupported")
                    ])
                ]
            ];
        }

        return $response;
    }
}