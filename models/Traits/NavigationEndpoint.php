<?php
namespace Rehike\Model\Traits;

/**
 * A common NavigationEndpoint trait that can be implemented and used
 * by all models.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
class NavigationEndpoint {
    public static function createEndpoint($url) {
        return (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => $url
                ]
            ]
        ];
    }
}