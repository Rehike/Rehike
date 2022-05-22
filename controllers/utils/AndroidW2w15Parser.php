<?php

/**
 * Parse ANDROID 15.21.54 FEwhat_to_watch contents.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class AndroidW2w15Parser
{
    /**
     * Parse the results of an API response.
     * 
     * @param object $results containing all of the shelf renderers
     */
    public static function parse($results)
    {
        $response = [];

        // Iterate all shelves and rewrite them.
        for ($i = 0; $i < count($results); $i++)
        if (isset($results[$i]->shelfRenderer))
        {
            $response[] = self::wrapShelf(
                self::parseVideoRenderers($results[$i]->shelfRenderer)
            );
        }

        return $response;
    }

    /**
     * Reparse video renderers to correct thumbnails and
     * metadata renderers.
     * 
     * @param object $shelfRenderer
     * @return object
     */
    private static function parseVideoRenderers($shelfRenderer)
    {
        $items = @$shelfRenderer->content->horizontalListRenderer->items;

        // If the input is abnormal, just move on.
        if (null == $items) return $shelfRenderer;

        // Iterate and select the direct renderer item.
        // InnerTube encodes these in an object, so a foreach
        // loop is used to select an ambiguous child.
        for ($i = 0; $i < count($items); $i++)
        foreach($items[$i] as $_key => $renderer)
        {
            // Rebuild WEB-like thumbnail overlays array
            self::buildThumbnailOverlays($renderer);

            // Correct the thumbnails array
            self::fixThumbnails($renderer);

            // Set length text property (if it isn't already present)
            if (isset($renderer->lengthText))
            {
                $renderer->lengthText = (object)[
                    "simpleText" => $renderer->lengthText->runs[0]->text
                ];
            }
        }

        return $shelfRenderer;
    }

    /**
     * Wrap a shelf in the same way the WEB ones were,
     * otherwise the view can't recognise it.
     * 
     * @param object $shelfRenderer
     * @return object
     */
    private static function wrapShelf($shelfRenderer)
    {
        return (object)[
            "itemSectionRenderer" => (object)[
                "contents" => [
                    (object)[
                        "shelfRenderer" => $shelfRenderer
                    ]
                ]
            ]
        ];
    }

    /**
     * Correct the thumbnails array. ANDROID thumbnails array returns
     * low quality 4:3 thumbnail images, which display wrong on
     * Hitchhiker
     * 
     * @param object $videoRenderer or other similar renderers.
     * @return void
     */
    private static function fixThumbnails(&$videoRenderer)
    {
        // Skip abnormal input
        if (!isset($videoRenderer->thumbnail->thumbnails)) return;
        if (!isset($videoRenderer->videoId)) return;
        
        $videoId = $videoRenderer->videoId;
        $sourceThumbs = $videoRenderer->thumbnail->thumbnails;

        $thumbnails = [];

        // Iterate through all provided thumbnails and pick
        // out the 16:9 ones
        for ($i = 0; $i < count($sourceThumbs); $i++)
        {
            if (isset($sourceThumbs[$i]->width))
            {
                $width = $sourceThumbs[$i]->width;
                $height = $sourceThumbs[$i]->height;

                // Calculate to see if the thumbnail is 16:9
                // and larger than the minimum size (196x110)
                if (
                    9 == $height / $width * 16 &&
                    196 >= $width
                )
                {
                    $thumbnails[] = $sourceThumbs[$i];
                }
            }
            else continue;
        }

        // If the above solution didn't work out,
        // then synthetically generate the thumbnails array.
        if (0 == count($thumbnails))
        {
            $thumbnails = [(object)[
                "url" => "//i.ytimg.com/vi/{$videoId}/mqdefault.jpg",
                "width" => 320,
                "height" => 180
            ]];
        }

        $videoRenderer->thumbnail->thumbnails = $thumbnails;
    }

    /**
     * Build a WEB-style thumbnailOverlays model from ANDROID data.
     * 
     * @param object $videoRenderer or other similar renderers.
     * @return void
     */
    private static function buildThumbnailOverlays(&$videoRenderer)
    {
        // Skip abnormal input
        if (!isset($videoRenderer->videoId)) return;
        $videoId = $videoRenderer->videoId;

        $thumbnailOverlays = [];

        // Iterate pre-existing items
        if (isset($videoRenderer->thumbnailOverlays))
        for ($i = 0; $i < count($videoRenderer->thumbnailOverlays); $i++)
        foreach ($videoRenderer->thumbnailOverlays[$i] as $key => $value)
        switch ($key)
        {
            // For the time status renderer, all that has to be done is
            // changing the text from runs to simpleText.
            case "thumbnailOverlayTimeStatusRenderer":
                $value->text = (object)[
                    "simpleText" => $value->text->runs[0]->text ?? $videoRenderer->lengthText->runs[0]->text
                ];
                
                // Keep the array in the response.
                $thumbnailOverlays[] = (object)["thumbnailOverlayTimeStatusRenderer" => $value];
                break;
        }

        // Add non-existing items
        if (self::rendererHasWL($videoRenderer))
        {
            // Add a watch later renderer if the video
            // renderer has that item.
            $thumbnailOverlays[] = (object)[
                "thumbnailOverlayToggleButtonRenderer" => (object)[
                    "isToggled" => false,
                    "untoggledIcon" => (object)[
                        "iconType" => "WATCH_LATER"
                    ],
                    "toggledIcon" => (object)[
                        "iconType" => "CHECK"
                    ],
                    "untoggledTooltip" => "Watch later",
                    "toggledTooltip" => "Added",
                    "untoggledServiceEndpoint" => (object)[
                        "commandMetadata" => (object)[
                            "webCommandMetadata" => (object)[
                                "sendPost" => true,
                                "apiUrl" => "/youtubei/v1/browse/edit_playlist"
                            ]
                        ],
                        "playlistEditEndpoint" => (object)[
                            "playlistId" => "WL",
                            "actions" => (object)[
                                "addedVideoId" => $videoId,
                                "action" => "ACTION_ADD_VIDEO"
                            ]
                        ]
                    ],
                    "toggledServiceEndpoint" => (object)[
                        "commandMetadata" => (object)[
                            "webCommandMetadata" => (object)[
                                "sendPost" => true,
                                "apiUrl" => "/youtubei/v1/browse/edit_playlist"
                            ]
                        ],
                        "playlistEditEndpoint" => (object)[
                            "playlistId" => "WL",
                            "actions" => (object)[
                                "removedVideoId" => $videoId,
                                "action" => "ACTION_REMOVE_VIDEO_BY_VIDEO_ID"
                            ]
                        ]
                    ],
                    "untoggledAccessibility" => (object)[
                        "accessibilityData" => (object)[
                            "label" => "Watch later"
                        ]
                    ],
                    "toggledAccessibility" => (object)[
                        "accessibilityData" => (object)[
                            "label" => "Added"
                        ]
                    ]
                ]
            ];
        }

        $videoRenderer->thumbnailOverlays = $thumbnailOverlays;
    }

    /**
     * Determine if a video renderer has a Watch Later
     * item.
     * 
     * @param object $renderer
     * @return bool
     */
    private static function rendererHasWL($renderer)
    {
        if (!isset($renderer->menu->menuRenderer)) return;

        $items = $renderer->menu->menuRenderer->items;

        // Iterate all menu items and determine based on the
        // presence of the ADD_TO_WATCH_LATER icon being used
        // by one of the items.
        for ($i = 0; $i < count($items); $i++)
        {
            if (
                isset($items[$i]->menuServiceItemRenderer->icon) &&
                "ADD_TO_WATCH_LATER" == $items[$i]->menuServiceItemRenderer->icon->iconType
            ) return true;
        }

        // Otherwise, it probably doesn't!
        return false;
    }
}