<?php
namespace Rehike\Model\Watch\Watch8;

use Rehike\Util\ExtractUtils;
use Rehike\Model\Traits\Runs;
use Rehike\Util\ImageUtils;
use Rehike\TemplateFunctions;
use Rehike\i18n;

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

            i18n::newNamespace("watch/secondary")->registerFromFolder("i18n/watch");

            $this->description = $info->description ?? null;
            
            // Legacy (COMPETENT) description
            if (isset($info->description))
            {
                $this->description = $info->description;
            }
            // "Modern" (RETARDED) description
            elseif (isset($info->attributedDescription))
            {
                $this->description = self::convertDescription($info->attributedDescription);
            }

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

    /**
     * Welp, the fucktards have done it again. The shitty description experiment is back,
     * and it's just as painful as it was before. Do they even have any fucking idea what
     * they're doing? The link color is fucking hardcoded blue for god's sake. There is
     * no fucking way they can be serious about this. They have to be fucking with us at 
     * this point. What a bunch of fucking retards.
     *     - Love, Aubrey <3
     * 
     * Anyways, this function just converts it back to the standard runs format.
     * 
     * We use mb_substr with UTF-8 here, because the indices are set up for a JS environment.
     * 
     * @param object $description  videoSecondaryInfoRenderer.attributedDescription
     */
    private static function convertDescription(object $description): object
    {
        // If there's no links
        if (!isset($description->commandRuns))
        {
            return (object) [
                "runs" => [
                    (object) [
                        "text" => $description->content
                    ]
                ]
            ];
        }

        $runs = [];

        // Start at the beginning of the string
        $start = 0;

        foreach ($description->commandRuns as $run)
        {
            // Text from before the link
            $beforeText = self::telegram_substr($description->content, $start, $run->startIndex - $start);

            if (!empty($beforeText))
            {
                $runs[] = (object) [
                    "text" => $beforeText
                ];
            }

            // Add the actual link
            $text = self::telegram_substr($description->content, $run->startIndex, $run->length);
            $endpoint = $run->onTap->innertubeCommand;
            $runs[] = (object) [
                "text" => $text,
                "navigationEndpoint" => $endpoint
            ];

            $start = $run->startIndex + $run->length;
        }

        // Add the text after the last link
        $lastText = self::telegram_substr($description->content, $start, null);
        if (!empty($lastText))
        {
            $runs[] = (object) [
                "text" => $lastText
            ];
        }

        // Fix link text
        foreach ($runs as &$run)
        if (isset($run->navigationEndpoint))
        {
            // Video links
            if (isset($run->navigationEndpoint->watchEndpoint)
            &&  !preg_match("/^([0-9]{1,2}(:)?)+$/", $run->text)) // Prevent replacing timestamps
            {
                $run->text = self::truncate("https://www.youtube.com" . $run->navigationEndpoint->commandMetadata->webCommandMetadata->url);
            }
            // Channel links
            elseif (isset($run->navigationEndpoint->browseEndpoint))
            {
                switch (substr($run->navigationEndpoint->browseEndpoint->browseId, 0, 2))
                {
                    case "UC":
                        $count = 1; // This has to be a variable for some reason
                        $run->text = str_replace("\xc2\xa0", "", str_replace("/", "", $run->text, $count));
                        // Add @ if it isn't there
                        if (substr($run->text, 0, 1) != "@")
                        {
                            $run->text = "@" . $run->text;
                        }
                        break;
                    case "FE":
                        break;
                    default:
                        $run->text = self::truncate("https://www.youtube.com" . $run->navigationEndpoint->commandMetadata->webCommandMetadata->url);
                        break;
                }
            }
            // Weird fake channel links
            else if (str_contains($run->text, "\xc2\xa0"))
            {
                $run->text = self::truncate($run->navigationEndpoint->commandMetadata->webCommandMetadata->url);
            }
        }

        return (object) [
            "runs" => $runs
        ];
    }

    /**
     * Truncate link texts
     */
    private static function truncate(?string $string): ?string
    {
        if (is_null($string)) return null;
        if (strlen($string) <= 37)
        {
            return $string;
        }
        else
        {
            return substr($string, 0, 37) . "...";
        }
    }

    // FUCKING THANK YOU SO MUCH
    // THIS FIXED EMOJI PROBLEM
    // https://stackoverflow.com/a/66878985
    private static function telegram_substr($str, $offset, $length) {
        $bmp = [];
        for( $i = 0; $i < mb_strlen($str); $i++ )
        {
            $mb_substr = mb_substr($str, $i, 1);
            $mb_ord = mb_ord($mb_substr);
            $bmp[] = $mb_substr;
            if ($mb_ord > 0xFFFF)
            {
                $bmp[] = "";
            }
        }
        return implode("", array_slice($bmp, $offset, $length));
    }
}

class MMetadataRowContainer
{
    use Runs;

    public $items = [];

    public function __construct(&$rows, $dataHost)
    {
        $i18n = i18n::getNamespace("watch/secondary");
        
        // Configuration
        $addLicense = true;

        if (!is_null($rows))
        {
            $this->items = $rows;

            foreach ($this->items as $index => $item)
            foreach ($item as $type => $data)
            {
                // if ($runs = @$item->metadataRowRenderer->contents[0]->runs) foreach ($runs as $run)
                // {
                //     $url = @$run->navigationEndpoint->commandMetadata->webCommandMetadata->url;
                //     if ($url && preg_match("/\/t\/creative_commons/", $url))
                //     {
                //         $addLicense = false;
                //     }
                // }

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
        $i18n = i18n::getNamespace("watch/secondary");
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
        $i18n = i18n::getNamespace("watch/secondary");

        $title = $i18n->metadataLicense;
        $text = $i18n->metadataLicenseStandard;

        return self::createSimpleField($title, $text);
    }
}
