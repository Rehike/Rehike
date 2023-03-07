<?php
namespace Rehike\Model\Playlist;

use \Rehike\Model\Common\MAlert;
use \Rehike\TemplateFunctions;
use \Rehike\i18n;

class PlaylistModel {
    public static function bake($dataHost) {
        $i18n = i18n::newNamespace("playlist");
        $i18n->registerFromFolder("i18n/playlist");
        $response = (object) [];

        $contentContainer = $dataHost->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content ?? null;

        if (!isset($contentContainer->sectionListRenderer)) {
            return (object) [
                "alerts" => [
                    new MAlert([
                        "type" => MAlert::TypeError,
                        "text" => $i18n->nonexistent
                    ])
                ]
            ];
        }

        $response->videoList = $contentContainer->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->playlistVideoListRenderer->contents;

        $response->header = $dataHost->header->playlistHeaderRenderer ?? null;
        $header = &$response->header;
        $header->actions = [];

        $response->alerts = [];
        if (isset($dataHost->alerts)) foreach ($dataHost->alerts as $alert) {
            $alert = $alert->alertWithButtonRenderer
                  ?? $alert->alertRenderer
                  ?? null;

            $response->alerts[] = MAlert::fromData($alert);
        } 

        return $response;
    }
}