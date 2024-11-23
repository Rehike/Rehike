<?php
namespace Rehike\Model\Traits;

/**
 * A common Runs trait that can be implemented and used
 * by all models.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
trait Runs
{
    /**
     * Create a run.
     */
    public function createRun($text, $href = null)
    {
        return (object)([
            "text" => $text
        ] + ((null != $href) ? [
            "navigationEndpoint" => (object)[
                "commandMetadata" => (object)[
                    "webCommandMetadata" => (object)[
                        "url" => $href
                    ]
                ]
            ]
        ] : []));
    }
}