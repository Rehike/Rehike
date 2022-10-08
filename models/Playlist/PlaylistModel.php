<?php
namespace Rehike\Model\Playlist;

use \Rehike\Model\Common\Alert\MAlert;
use \Rehike\Model\Common\Alert\MAlertType;
use \Rehike\Model\Playlist\MPlaylistHeader;
use \Rehike\TemplateFunctions;

class PlaylistModel {
    /**
     * Bake the main content,
     * also gives UCID for channel header request
     */
    public static function bakePL($dataHost) {
        $response = (object) [];
        $response -> raw = $dataHost;

        $contentContainer = $dataHost -> contents -> twoColumnBrowseResultsRenderer -> tabs[0] -> tabRenderer -> content ?? null;

        if (!isset($contentContainer -> sectionListRenderer)) {
            return (object) [
                "alerts" => [
                    new MAlert((object) [
                        "type" => MAlertType::Error,
                        "content" => [
                            (object) [
                                "text" => "That playlist does not exist."
                            ]
                        ]
                    ])
                ]
            ];
        }

        $response -> videoList = $contentContainer -> sectionListRenderer -> contents[0] -> itemSectionRenderer -> contents[0] -> playlistVideoListRenderer -> contents;

        $response -> plHeader = new MPlaylistHeader(@$dataHost -> sidebar -> playlistSidebarRenderer);

        $response -> alerts = [];
        if (isset($dataHost -> alerts)) for ($i = 0; $i < count($dataHost -> alerts); $i++) {
            $alert = $dataHost -> alerts[$i] -> alertWithButtonRenderer;

            $response -> alerts[] = new MAlert((object) [
                "type" => MAlert::parseInnerTubeType($alert -> type),
                "hasCloseButton" => (isset($alert -> dismissButton)),
                "content" => [
                    (object) [
                        "text" => TemplateFunctions::getText($alert -> text)
                    ]
                ]
            ]);
        } 

        return $response;
    }
}