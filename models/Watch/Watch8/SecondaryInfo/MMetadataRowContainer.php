<?php
namespace Rehike\Model\Watch\Watch8\SecondaryInfo;

use Rehike\Model\Traits\Runs;
use Rehike\Util\ParsingUtils;
use Rehike\i18n\i18n;
use Rehike\Model\Watch\WatchBakery;

class MMetadataRowContainer
{
    use Runs;

    public $items = [];

    public function __construct(WatchBakery $bakery, &$rows)
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
                                            "simpleText" => $i18n->get("metadataGame")
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
        if (isset($bakery->engagementPanels))
        // Go through the panels
        foreach ($bakery->engagementPanels as $panel)
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
                    $title = $i18n->get("metadataSong");

                    $content = ParsingUtils::getText($lockup->carouselLockupRenderer->videoLockup->compactVideoRenderer->title);
                    $href = ParsingUtils::getUrl($lockup->carouselLockupRenderer->videoLockup->compactVideoRenderer);
                    if ("" == $href) $href = null;

                    $musicItems[] = self::createSimpleField($title, $content, $href);
                }

                foreach (@$lockup->carouselLockupRenderer->infoRows as $row)
                {
                    $data = @$row->infoRowRenderer;
                    $element = isset($data->expandedMetadata) ? "expandedMetadata" : "defaultMetadata"; 

                    $title = ucfirst(strtolower(ParsingUtils::getText(@$data->title)));

                    $content = ParsingUtils::getText(@$data->{$element});
                    $href = ParsingUtils::getUrl(@$data->{$element}->runs[0]);
                    if ("" == $href) $href = null;

                    $musicItems[] = self::createSimpleField($title, $content, $href);
                }
            }

            $this->items += $musicItems;
        }

        // If specified, add a license field for standard.
        if ($addLicense && !$bakery->isLive)
            array_unshift($this->items, self::getLicenseField());

        // Add category option
        array_unshift($this->items, $this->getCategoryField($bakery));

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

    protected function getCategoryField(WatchBakery $bakery)
    {
        $i18n = i18n::getNamespace("watch");
        $title = $i18n->get("metadataCategory");

        $canonicalCategoryName = @$bakery->yt->playerResponse->microformat
            ->playerMicroformatRenderer->category
        ;
        
        if ($canonicalCategoryName)
            $category = $this->getLocalizedCategoryName($canonicalCategoryName);

        if (isset($category))
        {
            return self::createSimpleField($title, $category);
        }
    }

    protected function getLocalizedCategoryName(string $playerStr): string
    {
        $i18n = i18n::getNamespace("watch");

        return match ($playerStr)
        {
            "Autos & Vehicles" => $i18n->get("categoryAutosAndVehicles"),
            "Comedy" => $i18n->get("categoryComedy"),
            "Education" => $i18n->get("categoryEducation"),
            "Entertainment" => $i18n->get("categoryEntertainment"),
            "Film & Animation" => $i18n->get("categoryFilmAndAnimation"),
            "Gaming" => $i18n->get("categoryGaming"),
            "Howto & Style" => $i18n->get("categoryHowtoAndStyle"),
            "Music" => $i18n->get("categoryMusic"),
            "News & Politics" => $i18n->get("categoryNewsAndPolitics"),
            "Nonprofits & Activism" => $i18n->get("categoryNonprofitsAndActivism"),
            "People & Blogs" => $i18n->get("categoryPeopleAndBlogs"),
            "Pets & Animals" => $i18n->get("categoryPetsAndAnimals"),
            "Science & Technology" => $i18n->get("categoryScienceAndTechnology"),
            "Sports" => $i18n->get("categorySports"),
            "Travel & Events" => $i18n->get("categoryTravelAndEvents"),

            // If there is no i18n string, then just return the input.
            default => $playerStr
        };
    }

    protected function getLicenseField()
    {
        $i18n = i18n::getNamespace("watch");

        $title = $i18n -> get("metadataLicense");
        $text = $i18n -> get("metadataLicenseStandard");

        return self::createSimpleField($title, $text);
    }
}
