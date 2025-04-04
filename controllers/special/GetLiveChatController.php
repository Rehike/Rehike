<?php
namespace Rehike\Controller\special;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use function Rehike\Async\async;
use Rehike\SimpleFunnel;
use Rehike\SimpleFunnelResponse;
use Rehike\Network\Internal\Response;
use Rehike\Controller\core\HitchhikerController;
use Rehike\ControllerV2\IGetController;

/**
 * Live chat embed special controller.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author syndiate <syndiategaming@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class GetLiveChatController extends HitchhikerController implements IGetController
{
    public const YTCFG_REGEX = "/ytcfg\.set\(({.*?})\);/";
    public bool $useTemplate = false;

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        async(function() use (&$yt, &$request) {
            $chatData = yield SimpleFunnel::funnelCurrentPage();
            $chatHtml = $chatData->getText();

            $matches = [];
            preg_match(self::YTCFG_REGEX, $chatHtml, $matches);

            if (!isset($matches[1]))
                self::error("Could not find ytcfg");

            $ytcfg = json_decode($matches[1]);
            // Store the original ytcfg to replace in the HTML
            $oytcfg = $matches[1];

            if (is_null($ytcfg))
                self::error("Could not decode ytcfg");

            // Force light mode
            $ytcfg->LIVE_CHAT_ALLOW_DARK_MODE = false;

            // Configure experiment flags to disable
            // new icons and the color update
            if (!is_object($ytcfg->EXPERIMENT_FLAGS))
            {
                $ytcfg->EXPERIMENT_FLAGS = (object) [];
            }

            $exps = &$ytcfg->EXPERIMENT_FLAGS;
            
            $exps->kevlar_system_icons = false;
            $exps->web_darker_dark_theme = false;
            $exps->kevlar_watch_color_update = false;
            $exps->web_sheets_ui_refresh = false;

            

            $chatHtml = str_replace($oytcfg, json_encode($ytcfg), $chatHtml);

            // PHP doesn't let you cast objects (like: (array) $chatData->headers)
            // and the Response constructor does not accept ResponseHeaders for
            // the headers so we must convert it manually
            $headers = [];
            foreach ($chatData->headers as $name => $value) $headers[$name] = $value;
            SimpleFunnelResponse::fromResponse(
                new Response(
                    $chatData->sourceRequest, 
                    $chatData->status,
                    $chatHtml,
                    $headers
                )
            )->output();
        });
    }

    public static function error(string $msg): void
    {
        http_response_code(400);
        echo "[Rehike] Fatal error while attempting to load live chat: $msg";
        die();
    }
}