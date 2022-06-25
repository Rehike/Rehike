<?php
use \Rehike\Request;

$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'feed/what_to_watch_v2';
$yt->modularCoreModules = ['www/feed'];
$yt->page = (object) [];
$yt->enableFooterCopyright = true;
$yt->flow = (isset($_GET["flow"]) and $_GET["flow"] == "2") ? "list" : "grid";

if(!isset($yt->spf) or $yt->spf == false) {
    require "mod/getGuide.php";
}

$response = Request::innertubeRequest(
    "browse", 
    (object)[
        "browseId" => "FEwhat_to_watch"
    ]
);

$ytdata = json_decode($response);
$items = $ytdata -> contents -> twoColumnBrowseResultsRenderer -> tabs[0] -> tabRenderer -> content -> richGridRenderer -> contents;

$yt -> response = $response;
$yt -> videoList = [];

for ($i = 0; $i < count($items); $i++)
{
    if ($content = @$items[$i]->richItemRenderer->content)
    {
        if ("grid" == $yt->flow)
        {
            foreach ($content as $name => $value)
            {
                // Convert name formatting
                // videoRenderer => gridVideoRenderer
                $name = "grid" . ucfirst($name);

                $yt->videoList[] = (object)[$name => $value];
                break;
            }
        }
        else
        {
            $yt->videoList[] = $content;
        }
    }
    else
    {
        $yt->videoList[] = $items[$i];
    }
}

$yt -> page -> continuation = end($yt -> videoList) -> continuationItemRenderer -> continuationEndpoint -> continuationCommand -> token ?? null;