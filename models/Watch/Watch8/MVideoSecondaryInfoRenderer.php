<?php
namespace Rehike\Model\Watch\Watch8;

use Rehike\Util\ExtractUtils;
use Rehike\Model\Traits\Runs;
use Rehike\TemplateFunctions;

/**
 * Implement the model used by the video's secondary info renderer.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MVideoSecondaryInfoRenderer
{
    public $description;
    public $defaultExpanded;
    public $dateText;
    public $metadataRowContainer;
    public $showMoreText;
    public $showLessText;

    public function __construct($dataHost)
    {
        if (!is_null($dataHost::$secondaryInfo))
        {
            $info = &$dataHost::$secondaryInfo;
            $primaryInfo = &$dataHost::$primaryInfo;

            $this->description = $info->description ?? null;
            $this->defaultExpanded = $info->defaultExpanded ?? false;
            $this->dateText = isset($primaryInfo->dateText)
                ? ExtractUtils::resolveDate($primaryInfo->dateText)
                : null;
            $this->metadataRowContainer = new MMetadataRowContainer(
                $info->metadataRowContainer->metadataRowContainerRenderer->rows,
                $dataHost
            );
            $this->showMoreText = $info->showMoreText ?? null;
            $this->showLessText = $info->showLessText ?? null;
        }
    }
}

class MMetadataRowContainer
{
    use Runs;

    public $items;

    public function __construct(&$rows, $dataHost)
    {
        $this->items = $rows;
        
        // Configuration
        $addLicense = true;

        // If no rows, then create an empty array
        if (null == $this->items)
        {
            $this->items = [];
        }
        else foreach ($this->items as $item)
        {
            if ($runs = @$item->metadataRowRenderer->contents[0]->runs) foreach ($runs as $run)
            {
                $url = @$run->navigationEndpoint->commandMetadata->webCommandMetadata->url;
                if ($url && preg_match("/\/t\/creative_commons/", $url))
                {
                    $addLicense = false;
                }
            }
        }

        /* 
         * Also there is a new music renderer thing that is completely
         * batshit insane.
         * So enjoy this mess
         */
        // Check if engagement panels exist.
        if (isset($dataHost::$response->engagementPanels))
        // Go through the panels
        foreach ($dataHost::$response->engagementPanels as $panel)
        // Check the name of the current panel
        foreach ($panel->engagementPanelSectionListRenderer->content as $name => $content)
        if ("structuredDescriptionContentRenderer" == $name)
        // Go through the children of the panel and check the name of it
        foreach ($content->items as $item)
        foreach ($item as $name => $item)
        if ("videoDescriptionMusicSectionRenderer" == $name)
        {
            // And we're finally here. Now time for even more iteration.
            $musicItems = [];

            // Find the rows and go through them
            if (isset($item->carouselLockups)) // For some reason they don't exist on some videos.
            foreach (@$item->carouselLockups as $lockup)
            foreach (@$lockup->carouselLockupRenderer->infoRows as $row)
            {
                $data = @$row->infoRowRenderer;
                $element = isset($data->expandedMetadata) ? "expandedMetadata" : "defaultMetadata"; 

                // WHY IS THE FUCKING TITLE HARDCODED UPPERCASE
                // I AM GOING TO FUCKING KILL MYSELF
                // love taniko
                $title = ucfirst(strtolower(TemplateFunctions::getText(@$data->title)));

                $content = TemplateFunctions::getText(@$data->{$element});
                $href = TemplateFunctions::getEndpoint(@$data->{$element}->runs[0]);
                if ("" == $href) $href = null;

                $musicItems[] = self::createSimpleField($title, $content, $href);
                $this->items += $musicItems;
            }
        }

        // If specified, add a license field for standard.
        if ($addLicense && !$dataHost::$isLive)
            array_unshift($this->items, self::getLicenseField());

        // Add category option
        array_unshift($this->items, self::getCategoryField($dataHost));
    }

    protected function createSimpleField($title, $text, $href = null)
    {
        return (object)[
            "metadataRowRenderer" => (object)[
                "title" => (object)[
                    "runs" => [
                        self::createRun($title)
                    ]
                ],
                "contents" => [
                    (object)[
                        "runs" => [
                            self::createRun($text, $href)
                        ]
                    ]
                ]
            ]
        ];
    }

    protected function getCategoryField($dataHost)
    {
        $title = "Category"; // TODO: i18n

        $category = @$dataHost::$yt->playerResponse->microformat
            ->playerMicroformatRenderer->category
        ;

        if ($category)
        {
            return self::createSimpleField($title, $category);
        }
    }

    protected function getLicenseField()
    {
        $title = "License";
        $text = "Standard YouTube License";

        return self::createSimpleField($title, $text);
    }
}
