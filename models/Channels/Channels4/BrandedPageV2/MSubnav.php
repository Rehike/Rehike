<?php
namespace Rehike\Model\Channels\Channels4\BrandedPageV2;

use Rehike\i18n;

use Rehike\Model\Channels\Channels4Model;

class MSubnav
{
    public $rightButtons;
    public $leftButtons;
    public $title;
    public $backButton;

    public function addBackButton($href)
    {
        $this->backButton = new MSubnavBackButton($href);
    }

    public static function bakeVideos($data)
    {
        $i = new self();

        $baseUrl = Channels4Model::getBaseUrl();

        $i->addBackButton($baseUrl);

        // Process sort button
        if (isset($sortData))
        {
            $sortData = $data->sortSetting->sortFilterSubMenuRenderer;
            $sortButtonTitle = "";
            $sortButtonOptions = [];

            foreach ($sortData->subMenuItems as $item)
            {
                if ($item->selected)
                {
                    $sortButtonTitle = $item->title;
                }
                else
                {
                    $sortButtonOptions += [
                        $item->title => $item->navigationEndpoint->commandMetadata->webCommandMetadata->url
                    ];
                }
            }

            $i->rightButtons[] = new MSubnavMenuButton("sort", $sortButtonTitle, $sortButtonOptions);
        }

        $flow = $_GET["flow"] ?? "grid";
        if (!in_array($flow, ["grid", "list"])) $flow = "grid";
        $i->rightButtons[] = self::getFlowButton($flow);

        // Process uploads view
        $i->leftButtons[] = self::getViewButton();

        return $i;
    }

    public static function getViewButton()
    {
        $i18n = &i18n::getNamespace("channels");

        $baseUrl = Channels4Model::getBaseUrl();
        
        $options = [];

        $uploadsText = $title = $i18n->viewUploads;
        $streamsText = $title = $i18n->viewLiveStreams;

        if ("streams" == Channels4Model::$currentTab)
        {
            $activeText = $streamsText;
            $options += [$uploadsText => "$baseUrl/videos"];
        }
        else
        {
            $activeText = $uploadsText;
            $options += [$streamsText => "$baseUrl/streams"];
        }

        return new MSubnavMenuButton("view", $activeText, $options);
    }

    public static function getFlowButton($view)
    {
        $i18n = &i18n::getNamespace("channels");

        $baseUrl = Channels4Model::getBaseUrl();

        $gridText = $i18n->flowGrid;
        $listText = $i18n->flowList;

        $tab = ("streams" == Channels4Model::$currentTab) ? "streams" : "videos";

        $options = [];

        if ("grid" == $view)
        {
            $activeText = $gridText;
            $options += [$listText => "$baseUrl/$tab?flow=list"];
        }
        else if ("list" == $view)
        {
            $activeText = $listText;
            $options += [$gridText => "$baseUrl/$tab?flow=grid"];
        }

        return new MSubnavMenuButton("flow", $activeText, $options);
    }

    public static function fromData($data)
    {
        $i18n = &i18n::getNamespace("channels");

        $baseUrl = Channels4Model::getBaseUrl();

        $i = new self();

        $i -> addBackButton($baseUrl);

        if (count($data -> contentTypeSubMenuItems) > 1) {
            $i -> leftButtons[] = MSubnavMenuButton::fromData($data -> contentTypeSubMenuItems);
        } else {
            $i -> title = $data -> contentTypeSubMenuItems[0] -> title;
        }

        return $i;
    }
}