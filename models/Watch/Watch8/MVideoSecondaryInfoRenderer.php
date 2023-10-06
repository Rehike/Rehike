<?php
namespace Rehike\Model\Watch\Watch8;

use Rehike\Util\ExtractUtils;
use Rehike\Util\ParsingUtils;
use Rehike\i18n;
use Rehike\Model\Watch\Watch8\SecondaryInfo\MMetadataRowContainer;

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
            
            if (isset($info->attributedDescription))
            {
                $this->description = ParsingUtils::commandRunsToRuns($info->attributedDescription);
                self::fixDescLinks($this->description->runs);
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
     * Fix description link text.
     * 
     * @param object $runs  runs object
     * @param 
     */
    private static function fixDescLinks(array &$runs): void
    {
        foreach ($runs as &$run)
        if (isset($run->navigationEndpoint))
        {
            // Video links
            if (isset($run->navigationEndpoint->watchEndpoint)
            &&  !preg_match("/^([0-9]{1,2}(:)?)+$/", $run->text)) // Prevent replacing timestamps
            {
                $run->text = self::truncate(
                    $run->navigationEndpoint->commandMetadata->webCommandMetadata->url,
                    true
                );
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
                        $run->text = self::truncate(
                            $run->navigationEndpoint->commandMetadata->webCommandMetadata->url,
                            true
                        );
                        break;
                }
            }
            // Weird fake channel links
            else if (str_contains($run->text, "\xc2\xa0"))
            {
                $run->text = self::truncate($run->navigationEndpoint->commandMetadata->webCommandMetadata->url);
            }
        }
    }

    /**
     * Truncate a string for displaying as a description link.
     */
    private static function truncate(?string $string, bool $prefix = false): ?string
    {
        if (is_null($string)) return null;
        if ($prefix) $string = "https://www.youtube.com" . $string;
        if (strlen($string) <= 37)
        {
            return $string;
        }
        else
        {
            return substr($string, 0, 37) . "...";
        }
    }
}