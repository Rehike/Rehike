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
        self::getViewButton($i);

        return $i;
    }

    public static function getViewButton(self &$instance): void
    {
        $i18n = &i18n::getNamespace("channels");

        if (count(Channels4Model::$extraVideoTabs) == 0)
        {
            $instance->title = match(Channels4Model::getCurrentTab())
            {
                "videos" => $i18n->viewUploads,
                "streams" => $i18n->viewLiveStreams,
                "shorts" => $i18n->viewShorts
            };
        }
        else
        {
            $baseUrl = Channels4Model::getBaseUrl();

            \Rehike\ControllerV2\Core::$state->test = Channels4Model::$extraVideoTabs;
        
            $options = [];
    
            $uploadsText = $i18n->viewUploads;
            $streamsText = $i18n->viewLiveStreams;
            $shortsText = $i18n->viewShorts;
    
            if ("streams" == Channels4Model::getCurrentTab())
            {
                $activeText = $streamsText;
                $options += [$uploadsText => "$baseUrl/videos"];
                if (in_array("shorts", Channels4Model::$extraVideoTabs))
                {
                    $options += [$shortsText => "$baseUrl/shorts"];
                }
            }
            else if ("shorts" == Channels4Model::getCurrentTab())
            {
                $activeText = $shortsText;
                $options += [$uploadsText => "$baseUrl/videos"];
                if (in_array("streams", Channels4Model::$extraVideoTabs))
                {
                    $options += [$streamsText => "$baseUrl/streams"];
                }
            }
            else
            {
                $activeText = $uploadsText;
                if (in_array("streams", Channels4Model::$extraVideoTabs))
                {
                    $options += [$streamsText => "$baseUrl/streams"];
                }
                if (in_array("shorts", Channels4Model::$extraVideoTabs))
                {
                    $options += [$shortsText => "$baseUrl/shorts"];
                }
            }
    
            $instance->leftButtons[] = new MSubnavMenuButton("view", $activeText, $options);
        }        
    }

    public static function getSortButton($sort) {
        $i18n = i18n::getNamespace("channels");
        $baseUrl = Channels4Model::getBaseUrl();
        $tab = Channels4Model::getCurrentTab();
        $flow = $_GET["flow"] ?? "grid";

        $options = [];
        
        $popularText = $i18n->videoSortPopular;
        $newestText = $i18n->videoSortNewest;
        $oldestText = $i18n->videoSortOldest;

        switch ($sort)
        {
            case 0:
                $activeText = $newestText;
                $options += [
                    $popularText => "$baseUrl/$tab?sort=p&flow=$flow",
                    $oldestText => "$baseUrl/$tab?sort=da&flow=$flow"
                ];
                break;
            case 1:
                $activeText = $popularText;
                $options += [
                    $oldestText => "$baseUrl/$tab?sort=da&flow=$flow",
                    $newestText => "$baseUrl/$tab?sort=dd&flow=$flow"
                ];
                break;
            case 2:
                $activeText = $oldestText;
                $options += [
                    $popularText => "$baseUrl/$tab?sort=p&flow=$flow",
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

        $sort = match (Channels4Model::getVideosSort()) {
            0 => "dd",
            1 => "p",
            2 => "da",
            default => "dd"
        };

        $tab = Channels4Model::getCurrentTab();

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