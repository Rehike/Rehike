<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Controller\core\AjaxController;
use Rehike\Network;

use function Rehike\Async\async;

/**
 * Controller for the other playlist AJAX endpoints.
 * ...yeah, playlists on Hitchhiker are kinda odd to
 * work with.
 *
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends AjaxController
{
    public bool $useTemplate = false;

    public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        $action = self::findAction();

        switch ($action)
        {
            // This is the feature that saves the playlist to your library.
            // It used to be called liking playlists, and for that reason,
            // it retains that name internally.
            case "playlist_vote":
                $action = null;
                if ($_POST["vote"] == "like")
                {
                    $action = "like";
                }
                else if ($_POST["vote"] == "remove_like")
                {
                    $action = "removelike";
                }
                if (!is_null($action))
                {
                    Network::innertubeRequest("like/$action", [
                        "target" => (object) [
                            "playlistId" => $_POST["list"]
                        ]
                    ])->then(function($response) {
                        // var_dump($response);

                        $ytdata = $response->getJson();
                        if (isset($ytdata->error))
                        {
                            echo json_encode([
                                "code" => $ytdata->error->code
                            ]);
                        }
                        else
                        {
                            echo json_encode([
                                "code" => "SUCCESS"
                            ]);
                        }
                    });
                }
                break;
        }
    }
};