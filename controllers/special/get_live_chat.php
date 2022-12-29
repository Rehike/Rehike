<?php
namespace Rehike\Controller\Special;

use Rehike\SimpleFunnel;

return new class
{
    public const YTCFG_REGEX = "/ytcfg\.set\(({.*?})\);/";

    public function get(&$yt, $request)
    {
        $chatData = SimpleFunnel::funnelCurrentPage();
        $chatHtml = &$chatData->body;

        $matches = [];
        preg_match(self::YTCFG_REGEX, $chatHtml, $matches);

        if (!isset($matches[1]))
            self::error();

        $ytcfg = json_decode($matches[1]);
        // Store the original ytcfg to replace in the HTML
        $oytcfg = $matches[1];

        if (is_null($ytcfg))
            self::error();

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

        SimpleFunnel::output($chatData);
    }

    public static function error()
    {
        http_response_code(400);
        echo "[Rehike] Fatal error while attempting to load live chat";
        die();
    }
};