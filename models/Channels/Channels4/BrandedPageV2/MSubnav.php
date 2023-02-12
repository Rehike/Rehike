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

    public static function bakeVideos()
    {
        $i = new self();

        $baseUrl = Channels4Model::getBaseUrl();

        $i->addBackButton($baseUrl);

        if (!is_null(Channels4Model::getVideosSort()))
        {
            $i->rightButtons[] = self::getSortButton(Channels4Model::getVideosSort());
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

        if ("streams" == Channels4Model::getCurrentTab())
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

    public static function getSortButton($sort) {
        $i18n = i18n::getNamespace("channels");
        $baseUrl = Channels4Model::getBaseUrl();
        $tab = Channels4Model::getCurrentTab();
        $flow = $_GET["flow"] ?? "grid";

        $options = [];
        
        $newestText = $i18n->videoSortNewest;
        $popularText = $i18n->videoSortPopular;

        switch ($sort)
        {
            case 0:
                $activeText = $newestText;
                $options += [
                    $popularText => "$baseUrl/$tab?sort=p&flow=$flow"
                ];
                break;
            case 1:
                $activeText = $popularText;
                $options += [
                    $newestText => "$baseUrl/$tab?sort=dd&flow=$flow"
                ];
                break;
            default:
                return;
        }

        return new MSubnavMenuButton("sort", $activeText, $options);
    }

    public static function getFlowButton($view)
    {
        $i18n = &i18n::getNamespace("channels");

        $baseUrl = Channels4Model::getBaseUrl();

        $gridText = $i18n->flowGrid;
        $listText = $i18n->flowList;

        $sort = "dd";
        switch (Channels4Model::getVideosSort()) {
            case 0:
                $sort = "dd";
                break;
            case 1;
                $sort = "p";
                break;
        }

        $tab = ("streams" == Channels4Model::getCurrentTab()) ? "streams" : "videos";

        $options = [];

        if ("grid" == $view)
        {
            $activeText = $gridText;
            $options += [$listText => "$baseUrl/$tab?sort=$sort&flow=list"];
        }
        else if ("list" == $view)
        {
            $activeText = $listText;
            $options += [$gridText => "$baseUrl/$tab?sort=$sort&flow=grid"];
        }

        return new MSubnavMenuButton("flow", $activeText, $options);
    }

    public static function fromData($data)
    {
        $i18n = &i18n::getNamespace("channels");

        $baseUrl = Channels4Model::getBaseUrl();

        $i = new self();

        $i->addBackButton($baseUrl);

        if (count($data->contentTypeSubMenuItems) > 1) {
            $i->leftButtons[] = MSubnavMenuButton::fromData($data->contentTypeSubMenuItems);
        } else {
            $i->title = $data->contentTypeSubMenuItems[0]->title;
        }

        return $i;
    }
}