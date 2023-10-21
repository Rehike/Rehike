<?php
namespace Rehike\Model\Playlist;

use \Rehike\Model\Common\MAlert;
use \Rehike\i18n\i18n;

class PlaylistModel {
    public static function bake($dataHost) {
        $i18n = i18n::getNamespace("playlist");
        $response = (object) [];

        $contentContainer = $dataHost->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content ?? null;

        if (!isset($contentContainer->sectionListRenderer)) {
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
            $response->videoList = $videoList;
        }
        if ($header = @$dataHost->header->playlistHeaderRenderer)
        {
            $response->header = new MPlaylistHeader($header);
        }

        
        if (isset($dataHost->alerts)) 
        {
            $response->alerts = [];
            foreach ($dataHost->alerts as $alert) {
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