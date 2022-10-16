<?php
namespace Rehike\Util;

class RichShelfUtils {
    public static function reformatResponse($response) {
        if ($items = @$response -> onResponseReceivedActions[0] -> appendContinuationItemsAction -> continuationItems) {
            $contents = [];
            for ($i = 0; $i < count($items); $i++) {
                $contents[] = self::reformatShelf($items[$i]);
            }
            $response -> contents = $contents;
            unset($response -> onResponseReceivedActions);
        }
        return $response;
    }

    public static function reformatShelf($shelf) {
        if ($richShelf = @$shelf -> richSectionRenderer -> content -> richShelfRenderer) {
            $response = (object) [];
            $response -> title = $richShelf -> title ?? null;
            $response -> titleAnnotation = $richShelf -> subtitle ?? null;
            $response -> thumbnail = $richShelf -> thumbnail ?? null;
            $response -> endpoint = $richShelf -> endpoint ?? null;
            $contents = [];

            for ($i = 0; $i < count($richShelf -> contents); $i++) {
                $contents[] = self::reformatShelfItem($richShelf -> contents[$i]);
            }

            $response -> content = (object) [
                "horizontalListRenderer" => (object) [
                    "items" => $contents
                ]
            ];

            return (object) [
                "itemSectionRenderer" => (object) [
                    "contents" => [
                        (object) [
                            "shelfRenderer" => $response
                        ]
                    ]
                ]
            ];
        }
        return $shelf;
    }

    public static function reformatShelfItem($item) {
        if ($tmp = @$item -> richItemRenderer -> content) {
            return $tmp;
        }
        return $item;
    }
}