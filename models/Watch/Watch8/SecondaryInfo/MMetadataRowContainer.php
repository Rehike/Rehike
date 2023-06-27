<?php
namespace Rehike\Model\Watch\Watch8\SecondaryInfo;

use Rehike\Model\Traits\Runs;
use Rehike\TemplateFunctions;
use Rehike\i18n;

class MMetadataRowContainer
{
    use Runs;

    public $items = [];

    public function __construct(&$rows, $dataHost)
    {
        $i18n = i18n::getNamespace("watch");
        
        // Configuration
        $addLicense = true;

        if (!is_null($rows))
        {
            $this->items = $rows;

            foreach ($this->items as $index => $item)
            foreach ($item as $type => $data)
            {
                switch ($type)
                {
                    case "metadataRowRenderer":
                        $url = "";
                        if ($url = @$data->contents[0]->runs->navigationEndpoint->commandMetadata->webCommandMetadata->url
                        &&  str_contains($url, "/t/creative_commons/"))
                            $addLicense = false;
                            break;
                    case "richMetadataRowRenderer":
                        // Very very icky, we don't want this.
                        array_splice($this->items, $index, 1);

                        $richItems = [];

                        foreach ($data->contents as $row)
                        {
                            $row = $row->richMetadataRenderer ?? null;
                            if (!is_null($row) && $row->style == "RICH_METADATA_RENDERER_STYLE_BOX_ART")
                            {
                                $richItems[] = (object) [
                                    "richMetadataRowRenderer" => (object) [
                                        "label" => (object) [
                                            "simpleText" => $i18n->metadataGame
                                        ],
                                        "title" => $row->title,
                                        "subtitle" => $row->subtitle ?? null,
                                        "callToAction" => $row->callToAction ?? null,
                                        "navigationEndpoint" => $row->endpoint,
                                        "thumbnail" => $row->thumbnail ?? null
                                    ]
                                ];
                            }
                        }
                        break;
                }
            }
        }

        /* 
         * Also there is a new music renderer thing that is completely
         * batshit insane.
         * So enjoy this mess
         */
        // Check if engagement panels exist.
        if (isset($dataHost::$engagementPanels))
        // Go through the panels
        foreach ($dataHost::$engagementPanels as $panel)
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
            {
                // Add song title from the lockup if it exists
                if (isset($lockup->carouselLockupRenderer->videoLockup))
                {
                    $title = $i18n->metadataSong;

                    $content = TemplateFunctions::getText($lockup->carouselLockupRenderer->videoLockup->compactVideoRenderer->title);
                    $href = TemplateFunctions::getUrl($lockup->carouselLockupRenderer->videoLockup->compactVideoRenderer);
                    if ("" == $href) $href = null;

                    $musicItems[] = self::createSimpleField($title, $content, $href);
                }

                foreach (@$lockup->carouselLockupRenderer->infoRows as $row)
                {
                    $data = @$row->infoRowRenderer;
                    $element = isset($data->expandedMetadata) ? "expandedMetadata" : "defaultMetadata"; 

                    // WHY IS THE FUCKING TITLE HARDCODED UPPERCASE
                    // I AM GOING TO FUCKING KILL MYSELF
                    // love taniko
                    $title = ucfirst(strtolower(TemplateFunctions::getText(@$data->title)));

                    $content = TemplateFunctions::getText(@$data->{$element});
                    $href = TemplateFunctions::getUrl(@$data->{$element}->runs[0]);
                    if ("" == $href) $href = null;

                    $musicItems[] = self::createSimpleField($title, $content, $href);
                }
            }

            $this->items += $musicItems;
        }

        // If specified, add a license field for standard.
        if ($addLicense && !$dataHost::$isLive)
            array_unshift($this->items, self::getLicenseField());

        // Add category option
        array_unshift($this->items, self::getCategoryField($dataHost));

        // Add rich items (game in gaming video)
        if (isset($richItems) && count($richItems) > 0)
        foreach ($richItems as $item)
            array_unshift($this->items, $item);
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
        $i18n = i18n::getNamespace("watch");
        $title = $i18n->metadataCategory;

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
        $i18n = i18n::getNamespace("watch");

        $title = $i18n -> metadataLicense;
        $text = $i18n -> metadataLicenseStandard;

        return self::createSimpleField($title, $text);
    }
}