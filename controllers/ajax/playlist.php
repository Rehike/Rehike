<?php
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
    public $useTemplate = false;

    public function onPost(&$yt, $request)
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
                    Network::innertubeRequest($action, [
                        "target" => (object) [
                            "playlistId" => $_POST["list"]
                        ]
                    ], ignoreErrors: true)->then(function($response) {
                        var_dump($response);

                        // $ytdata = $response->getJson();
                    
                        // if (isset($ytdata->error))
                        // {
                        //     echo (object) [
                        //         "code" => $ytdata->error->code
                        //     ];
                        // }
                        // else
                        // {
                        //     echo (object) [
                        //         "code" => "SUCCESS"
                        //     ];
                        // }
                    });
                }
                break;
        }
    }
};