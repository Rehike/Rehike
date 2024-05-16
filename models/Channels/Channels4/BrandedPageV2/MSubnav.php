<?php
namespace Rehike\Model\Channels\Channels4\BrandedPageV2;

use Rehike\i18n\i18n;

use Rehike\Model\Channels\Channels4Model;

class MSubnav
{
    public $rightButtons;
    public $leftButtons;
    public $title;
    public $backButton;
    
    private Channels4Model $parent;
    
    public function __construct(Channels4Model $parent)
    {
        $this->parent = $parent;
    }

    public function addBackButton($href)
    {
        $this->backButton = new MSubnavBackButton($href);
    }

    public static function bakeVideos(Channels4Model $parent)
    {
        $i = new self($parent);

        $baseUrl = $parent->getBaseUrl();

        $i->addBackButton($baseUrl);

        if (!is_null($parent->getVideosSort()))
        {
            $i->rightButtons[] = $i->getSortButton($parent->getVideosSort());
        }

        $flow = $_GET["flow"] ?? "grid";
        if (!in_array($flow, ["grid", "list"])) $flow = "grid";
        $i->rightButtons[] = $i->getFlowButton($flow);

        // Process uploads view
        self::getViewButton($i);

        return $i;
    }

    public static function getViewButton(self &$instance): void
    {
        $i18n = i18n::getNamespace("channels");

        if (count($instance->parent->extraVideoTabs) == 0)
        {
            $instance->title = match($instance->parent->getCurrentTab())
            {
                "videos" => $i18n->get("viewUploads"),
                "streams" => $i18n->get("viewLiveStreams"),
                "shorts" => $i18n->get("viewShorts")
            };
        }
        else
        {
            $baseUrl = $instance->parent->getBaseUrl();

            \Rehike\ControllerV2\Core::$state->test = $instance->parent->extraVideoTabs;
        
            $options = [];
    
            $uploadsText = $i18n->get("viewUploads");
            $streamsText = $i18n->get("viewLiveStreams");
            $shortsText = $i18n->get("viewShorts");
    
            if ("streams" == $instance->parent->getCurrentTab())
            {
                $activeText = $streamsText;
                $options += [$uploadsText => "$baseUrl/videos"];
                if (in_array("shorts", $instance->parent->extraVideoTabs))
                {
                    $options += [$shortsText => "$baseUrl/shorts"];
                }
            }
            else if ("shorts" == $instance->parent->getCurrentTab())
            {
                $activeText = $shortsText;
                $options += [$uploadsText => "$baseUrl/videos"];
                if (in_array("streams", $instance->parent->extraVideoTabs))
                {
                    $options += [$streamsText => "$baseUrl/streams"];
                }
            }
            else
            {
                $activeText = $uploadsText;
                if (in_array("streams", $instance->parent->extraVideoTabs))
                {
                    $options += [$streamsText => "$baseUrl/streams"];
                }
                if (in_array("shorts", $instance->parent->extraVideoTabs))
                {
                    $options += [$shortsText => "$baseUrl/shorts"];
                }
            }
    
            $instance->leftButtons[] = new MSubnavMenuButton("view", $activeText, $options);
        }        
    }

    public function getSortButton($sort)
    {
        $i18n = i18n::getNamespace("channels");
        $baseUrl = $this->parent->getBaseUrl();
        $tab = $this->parent->getCurrentTab();
        $flow = $_GET["flow"] ?? "grid";

        $options = [];
        
        $popularText = $i18n->get("videoSortPopular");
        $newestText = $i18n->get("videoSortNewest");
        $oldestText = $i18n->get("videoSortOldest");

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

    public function getFlowButton($view)
    {
        $i18n = i18n::getNamespace("channels");

        $baseUrl = $this->parent->getBaseUrl();

        $gridText = $i18n->get("flowGrid");
        $listText = $i18n->get("flowList");

        $sort = match ($this->parent->getVideosSort())
        {
            0 => "dd",
            1 => "p",
            2 => "da",
            default => "dd"
        };

        $tab = $this->parent->getCurrentTab();

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

    public static function fromData(Channels4Model $parent, $data)
    {
        $i18n = i18n::getNamespace("channels");

        $baseUrl = $parent->getBaseUrl();

        $i = new self($parent);

        $i->addBackButton($baseUrl);

        if (count($data->contentTypeSubMenuItems) > 1)
        {
            $i->leftButtons[] = MSubnavMenuButton::fromData($data->contentTypeSubMenuItems);
        }
        else
        {
            $i->title = $data->contentTypeSubMenuItems[0]->title;
        }

        return $i;
    }
}