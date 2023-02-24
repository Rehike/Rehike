<?php
namespace Rehike\Model\Watch\Watch8;

use Rehike\Util\ExtractUtils;
use Rehike\i18n;

use Rehike\Model\Watch\Watch8\{
    SecondaryInfo\MMetadataRowContainer
};

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

            i18n::newNamespace("watch/secondary") ->registerFromFolder("i18n/watch");

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
    public static function convertDescription(object $description): object
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
            $beforeText = mb_substr($description->content, $start, $run->startIndex - $start, "utf-8");

            if (!empty($beforeText))
            {
                $runs[] = (object) [
                    "text" => $beforeText
                ];
            }

            // Add the actual link
            $text = mb_substr($description->content, $run->startIndex, $run->length, "utf-8");
            $endpoint = $run->onTap->innertubeCommand;
            $runs[] = (object) [
                "text" => $text,
                "navigationEndpoint" => $endpoint
            ];

            $start = $run->startIndex + $run->length;
        }

        // Add the text after the last link
        $lastText = mb_substr($description->content, $start, null, "utf-8");
        if (!empty($lastText))
        {
            $runs[] = (object) [
                "text" => $lastText
            ];
        }

        // Fix link text
        foreach ($runs as &$run)
        {
            // Video links
            if (isset($run->navigationEndpoint->watchEndpoint))
            {
                $run->text = substr(
                    "https://www.youtube.com" . $run->navigationEndpoint->commandMetadata->webCommandMetadata->url,
                    0, 37
                ) . "...";
            }
            // Channel links
            elseif (isset($run->navigationEndpoint->browseEndpoint))
            {
                $count = 1; // This has to be a variable for some reason
                $run->text = str_replace("\xc2\xa0", "", str_replace("/", "", $run->text, $count));
            }
        }

        return (object) [
            "runs" => $runs
        ];
    }
}