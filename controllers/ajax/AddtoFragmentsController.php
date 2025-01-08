<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Controller\core\AjaxController;
use Rehike\Network;
use Rehike\Model\AddTo\MAddTo;

use Rehike\ControllerV2\{
    IGetController,
    IPostController,
};

/**
 * Add to playlist AJAX controller.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class AddtoFragmentsController extends AjaxController implements IGetController, IPostController
{
    public bool $useTemplate = true;
    public string $template = "ajax/addto";
    public string $contentType = "application/xml";

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $this->onPost($yt, $request);
    }

    public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        // Because YouTube's own server is a bit weird, this
        // might go too fast and break everything.
        // Hence: very gross fix for a server-side bug
        sleep(3);

        Network::innertubeRequest(
            action: "playlist/get_add_to_playlist",
            body: [
                "videoIds" => explode(",", $_POST["video_ids"]) ?? [""]
            ]
        )->then(function ($response) use ($yt) {
            $data = $response->getJson();

            $lists = $data->contents[0]->addToPlaylistRenderer->playlists;

            $yt->page->addto = new MAddTo($lists);
        });
    }
}