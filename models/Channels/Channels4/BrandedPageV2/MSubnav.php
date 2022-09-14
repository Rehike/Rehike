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

        $i->rightButtons[] = self::getFlowButton("grid");

        // Process uploads view
        // TODO
        $i->leftButtons[] = new MSubnavMenuButton("view", "Uploads", []);

        return $i;
    }

    public static function getFlowButton($view)
    {
        $i18n = &i18n::getNamespace("channels");

        $baseUrl = Channels4Model::getBaseUrl();

        $gridText = $i18n->flowGrid;
        $listText = $i18n->flowList;

        $options = [];

        if ("grid" == $view)
        {
            $activeText = $gridText;
            $options += [$listText => "$baseUrl/videos?flow=list"];
        }
        else if ("list" == $view)
        {
            $activeText = $listText;
            $options += [$listText => "$baseUrl/videos?flow=grid"];
        }

        return new MSubnavMenuButton("flow", $activeText, $options);
    }
}