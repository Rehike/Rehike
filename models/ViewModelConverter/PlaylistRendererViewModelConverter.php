<?php
namespace Rehike\Model\ViewModelConverter;

use Rehike\Util\ParsingUtils;
use Rehike\i18n\i18n;
use Rehike\FormattedString;
use Rehike\Util\FormattedStringBuilder;

/**
 * Converts a playlist renderer view model to the classic format that Rehike expects.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class PlaylistRendererViewModelConverter extends BasicVMC
{
    public function bake(LockupViewModelConverter $parent): object
    {
        $result = (object)[];
        
        $metadataVM = $this->viewModel->metadata->lockupMetadataViewModel;
        $thumbnailVM = $this->viewModel->contentImage->collectionThumbnailViewModel->primaryThumbnail->thumbnailViewModel;
        $thumbnailBadgeVM = $thumbnailVM->overlays[0]->thumbnailOverlayBadgeViewModel->thumbnailBadges[0]->thumbnailBadgeViewModel;
        
        $result->title = ParsingUtils::indexedRunsToRuns($metadataVM->title);
        $result->playlistId = $this->viewModel->contentId;
        
        $result->thumbnail = (object)[
            "thumbnails" => $thumbnailVM->image->sources
        ];
        
        $result->videoCountText = (object)[
            "simpleText" => $thumbnailBadgeVM->text
        ];

        $result->thumbnailOverlays = [];
        foreach ($thumbnailVM->overlays as $overlay)
        {
            if (isset($overlay->thumbnailHoverOverlayViewModel))
            {
                $text = $overlay->thumbnailHoverOverlayViewModel->text->content;
                $result->thumbnailOverlays[] = (object)[
                    "thumbnailOverlayHoverTextRenderer" => (object)[
                        "text" => (object)[
                            "simpleText" => $text
                        ]
                    ]
                ];
            }
        }
        
        $result->thumbnailText = $this->formatThumbnailText($thumbnailBadgeVM->text);
        $result->navigationEndpoint = $this->viewModel->rendererContext->commandContext->onTap->innertubeCommand;
        
        // Parse metadata rows:
        $metadataRows = $metadataVM->metadata->contentMetadataViewModel->metadataRows;
        foreach ($metadataRows as $rowId => $contents)
        {
            if (isset($contents->metadataParts))
            {
                $parts = $contents->metadataParts;
                
                if (isset($parts[0]->text->commandRuns[0]->onTap->innertubeCommand))
                {
                    $endpoint = $parts[0]->text->commandRuns[0]->onTap->innertubeCommand;
                    
                    if (isset($endpoint->commandMetadata->webCommandMetadata->webPageType))
                    {
                        $webPageType = $endpoint->commandMetadata->webCommandMetadata->webPageType;
                        
                        if ($webPageType == "WEB_PAGE_TYPE_CHANNEL" && $rowId == 0 && !isset($result->longBylineText))
                        {
                            // This is almost certainly a link to the channel of the creator of the playlist.
                            $result->longBylineText = ParsingUtils::indexedRunsToRuns($parts[0]->text);
                        }
                        else if ($webPageType == "WEB_PAGE_TYPE_WATCH")
                        {
                            // Playlist contents preview.
                            if (!isset($result->videos))
                            {
                                $result->videos = [];
                            }
                            
                            // The title has the time embedded. Dirty hack to isolate it:
                            $titleParts = explode(" · ", ParsingUtils::getText($parts[0]->text));
                            $lengthText = $titleParts[count($titleParts) - 1];
                            array_pop($titleParts);
                            $title = implode(" · ", $titleParts);
                            
                            $result->videos[] = (object)[
                                "childVideoRenderer" => (object)[
                                    "title" => $title,
                                    "navigationEndpoint" => $endpoint,
                                    "lengthText" => (object)[
                                        "simpleText" => $lengthText
                                    ]
                                ]
                            ];
                        }
                    }
                }
            }
        }
        
        return $result;
    }
    
    private function formatThumbnailText(string $original): FormattedString
    {
        $fsb = new FormattedStringBuilder();
        
        $i18n = i18n::getNamespace("regex");
        $numberMatchRegex = $i18n->get("numberMatch");
        
        $parts = preg_split($numberMatchRegex, $original, 0, PREG_SPLIT_DELIM_CAPTURE);
            
        foreach ($parts as $part)
        {
            $fsb->createAndAddRun($part, preg_match($numberMatchRegex, $part) ? FormattedStringBuilder::RUN_DISPLAY_BOLD : 0);
        }
        
        return $fsb->build();
    }
}