<?php
namespace Rehike\Model\Share;

use \Rehike\Model\Common\MAlert;
use Rehike\i18n;
use Rehike\Model\Share\MShareTabBar;

class ShareEmbedModel {

    public static function bake($videoId, $title, $listData) {

        $response = (object) [];
        $i18n = i18n::newNamespace("share");
        $i18n->registerFromFolder("i18n/share");


        $alternateUrls = [];


        if ($listData != null) {



            $playlistHost = $listData->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content ?? null;
            
            if (!isset($playlistHost->sectionListRenderer)) {
                return (object) [
                    "alerts" => [
                        new MAlert([
                            "type" => MAlert::TypeError,
                            "text" => $i18n->playlistNonexistent
                        ])
                    ]
                ];
            }

            
            $listId = $listData->header->playlistHeaderRenderer->playlistId;
            $videoList = $playlistHost->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->playlistVideoListRenderer->contents;


            if (count($videoList) <= 0) {
                return (object) [
                    "alerts" => [
                        new MAlert([
                            "type" => MAlert::TypeError,
                            "text" => $i18n->playlistNoVideos
                        ])
                    ]
                ];
            }


            $firstVideoId = $videoList[0]->playlistVideoRenderer->videoId;

            $response->embedUrl = "https://www.youtube.com/embed/$videoId?list=$listId";
            $response->isList = true;
            
            $alternateUrls = [
                (object) [
                    "key" => "first",
                    "content" => "https://www.youtube.com/embed/$firstVideoId?list=$listId"
                ],
                (object) [
                    "key" => "default",
                    "content" => $response->embedUrl
                ],
                (object) [
                    "key" => "nolist",
                    "content" => "https://www.youtube.com/embed/$videoId"
                ]
             ];


        } else {
            $response->embedUrl = "https://www.youtube.com/embed/$videoId";
            $response->isList = false;
        }


        
        $response->alternateUrls = $alternateUrls;
        $response->strs = $i18n;



        $sizes = [
            (object) [
                "name" => "default",
                "width" => "560",
                "height" => "315"
            ],
            (object) [
                "name" => "hd720",
                "width" => "1280",
                "height" => "720"
            ],
            (object) [
                "name" => "large",
                "width" => "853",
                "height" => "450"
            ],
            (object) [
                "name" => "default",
                "width" => "640",
                "height" => "360"
            ],
            (object) [
                "name" => "default",
                "text" => $i18n->get("customSize")
            ]
        ];


        $options = [
            (object) [
                "name" => "show-related",
                "text" => $i18n->get("showRelated"),
                "active" => true
            ],
            (object) [
                "name" => "show-controls",
                "text" => $i18n->get("showControls"),
                "active" => true
            ],
            (object) [
                "name" => "show-info",
                "text" => $i18n->get("showInfo"),
                "active" => true
            ],
            (object) [
                "name" => "delayed-cookies",
                "text" => $i18n->get("delayedCookies"),
                "url" => "http://www.google.com/support/youtube/bin/answer.py?answer=171780&expand=PrivacyEnhancedMode#privacy",
                "active" => false
            ]
        ];

        $response->options = $options;
        $response->sizes = $sizes;

        return $response;

    }

}