<?php
namespace Rehike\Model\ViewModelConverter;

use Rehike\Util\ParsingUtils;
use Rehike\i18n\i18n;
use Rehike\Model\Common\MCollaborator;

/**
 * Converts a video renderer view model to the classic format that Rehike expects.
 * 
 * @author aubymori <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
class VideoRendererViewModelConverter extends BasicVMC
{
    public function bake(LockupViewModelConverter $parent): object
    {
        $result = (object)[];

        $metadata = $this->viewModel->metadata->lockupMetadataViewModel;
        $result->title = (object)[
            "simpleText" => $metadata->title->content
        ];
        $result->videoId = $this->viewModel->contentId;

        $thumbnail = $this->viewModel->contentImage->thumbnailViewModel;
        $result->thumbnail = (object)[
            "thumbnails" => $thumbnail->image->sources
        ];

        if (is_array(@$thumbnail->overlays))
        {
            $result->thumbnailOverlays = [];
            foreach ($thumbnail->overlays as $overlay)
            foreach ($overlay as $name => $content)
            {
                switch ($name)
                {
                    case "animatedThumbnailOverlayViewModel":
                        $result->richThumbnail = (object)[
                            "movingThumbnailRenderer" => (object)[
                                "enableHoveredLogging" => true,
                                "enableOverlay" => true,
                                "movingThumbnailDetails" => (object)[
                                    "logAsMovingThumbnail" => true,
                                    "thumbnails" => $content->thumbnail->sources
                                ]
                            ]
                        ];
                        break;
                    case "thumbnailOverlayBadgeViewModel":
                        foreach ($content->thumbnailBadges as $tbadge)
                        {
                            $tcontent = $tbadge->thumbnailBadgeViewModel;
                            switch ($tcontent->badgeStyle)
                            {
                                case "THUMBNAIL_OVERLAY_BADGE_STYLE_LIVE":
                                    $strings = i18n::getNamespace("browse");
                                    $text = $tcontent->text ?? '';
                                    $text = ($text == $strings->get("liveBadgeOriginal"))
                                        ? $strings->get("liveBadge")
                                        : $text;
                                    if (!isset($result->badges))
                                        $result->badges = [];
                                    $result->badges[] = (object)[
                                        "metadataBadgeRenderer" => (object)[
                                            "label" => $text,
                                            "style" => "BADGE_STYLE_TYPE_LIVE_NOW"
                                        ]
                                    ];
                                    break;
                                default:
                                    if (isset($tcontent->text))
                                    {
                                        $result->thumbnailOverlays[] = (object)[
                                            "thumbnailOverlayTimeStatusRenderer" => (object)[
                                                "style" => "DEFAULT",
                                                "text" => (object)[
                                                    "accessibility" => (object)[
                                                        "accessibilityData" => (object)[
                                                            "label" => @$tcontent->rendererContext->accessibilityContext->label ?? ""
                                                        ]
                                                    ],
                                                    "simpleText" => $tcontent->text
                                                ]
                                            ]
                                        ];
                                    }
                                    if (isset($tcontent->animatedText))
                                    {
                                        $result->thumbnailOverlays[] = (object)[
                                            "thumbnailOverlayNowPlayingRenderer" => (object)[
                                                "text" => (object)[
                                                    "runs" => [
                                                        (object)[
                                                            "text" => $tcontent->animatedText
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ];
                                    }
                                    break;
                            }
                            
                        }
                        break;
                    case "thumbnailHoverOverlayToggleActionsViewModel":
                        foreach ($content->buttons as $button)
                        {
                            $tbConverter = new ToggleButtonViewModelConverter(
                                $button->toggleButtonViewModel,
                                $this->frameworkUpdates
                            );

                            $convertedButton = $tbConverter->bakeToggleButtonRenderer(["isToggled" => false]);
                            $memberMap = [
                                "defaultIcon" => "untoggledIcon",
                                "defaultServiceEndpoint" => "untoggledServiceEndpoint",
                                "accessibilityData" => "untoggledAccessibility",
                                "toggledAccessibilityData" => "toggledAccessibility",
                            ];
                            foreach ($memberMap as $name => $newName)
                            {
                                if (isset($convertedButton->{$name}))
                                {
                                    $convertedButton->{$newName} = $convertedButton->{$name};
                                    unset($convertedButton->{$name});
                                }
                            }

                            $convertedButton->untoggledTooltip = $convertedButton->accessibility->label;
                            $convertedButton->toggledTooltip = $convertedButton->toggledAccessibility->accessibilityData->label;

                            $result->thumbnailOverlays[] = (object)[
                                "thumbnailOverlayToggleButtonRenderer" => $convertedButton
                            ];
                        }
                        break;
                    case "thumbnailBottomOverlayViewModel":
                    {
                        if (isset($content->progressBar))
                        {
                            $result->thumbnailOverlays[] = (object)[
                                "thumbnailOverlayResumePlaybackRenderer" => (object)[
                                    "percentDurationWatched" => $content->progressBar->thumbnailOverlayProgressBarViewModel->startPercent
                                ]
                            ];
                        }
                        
                        $badge = @$content->badges[0]->thumbnailBadgeViewModel ?? null;
                        if (!is_null($badge))
                        {
                            if (isset($badge->text))
                            {
                                $result->thumbnailOverlays[] = (object)[
                                    "thumbnailOverlayTimeStatusRenderer" => (object)[
                                        "style" => "DEFAULT",
                                        "text" => (object)[
                                            "accessibility" => (object)[
                                                "accessibilityData" => (object)[
                                                    "label" => @$badge->rendererContext->accessibilityContext->label ?? ""
                                                ]
                                            ],
                                            "simpleText" => $badge->text
                                        ]
                                    ]
                                ];
                            }
                            if (isset($badge->animatedText))
                            {
                                $result->thumbnailOverlays[] = (object)[
                                    "thumbnailOverlayNowPlayingRenderer" => (object)[
                                        "text" => (object)[
                                            "runs" => [
                                                (object)[
                                                    "text" => $badge->animatedText
                                                ]
                                            ]
                                        ]
                                    ]
                                ];
                            }
                        }
                        break;
                    }
                }
            }
        }
    
        $metadataRows = $metadata->metadata->contentMetadataViewModel->metadataRows;
        foreach ($metadataRows as $rowId => $contents)
        {
            if (isset($contents->metadataParts))
            foreach ($contents->metadataParts as $partId => $part)
            {
                $hasBadges = isset($metadataRows[array_key_last($metadataRows)]->badges);
                $rowCount = count($metadataRows);
                // Author text.
                if ($rowId == 0 &&
                ((!$hasBadges && $rowCount == 2) || ($hasBadges && $rowCount == 3)))
                {
                    $bylineText = ParsingUtils::indexedRunsToRuns($part->text);
                    
                    // Handle the multiple authors bullshit by just grabbing the first one.
                    $run = &$bylineText->runs[0];
                    if (isset($run->navigationEndpoint->showDialogCommand))
                    {
                        $channel = new MCollaborator($run->navigationEndpoint->showDialogCommand->panelLoadingStrategy->inlineContent->dialogViewModel->customContent
                            ->listViewModel->listItems[0]);

                        $run->text = $channel->name;
                        $run->navigationEndpoint = $channel->navigationEndpoint;
                        $badgeIcon = @$channel->rawData->title->attachmentRuns[0]->element->type->imageType->image->sources[0]->clientResource->imageName ?? null;
                    }
                    
                    $result->shortBylineText = $bylineText;
                    $result->longBylineText = $bylineText;

                    // Just fucking kill me now.
                    $badgeIcon = @$part->text->attachmentRuns[0]->element->type->imageType->image->sources[0]->clientResource->imageName ?? null;
                    $badgeIcon = match ($badgeIcon)
                    {
                        "CHECK_CIRCLE_FILLED" => "CHECK_CIRCLE_THICK",
                        "AUDIO_BADGE" => "MUSIC_NOTE",
                        default => null
                    };
                    if (!is_null($badgeIcon))
                    {
                        $result->ownerBadges = [
                            (object)[
                                "metadataBadgeRenderer" => (object)[
                                    "icon" => (object)[
                                        "iconType" => $badgeIcon
                                    ],
                                    "style" => "BADGE_STYLE_TYPE_VERIFIED"
                                ]
                            ]
                        ];
                    }
                }
                // Below byline (view count and date)
                else if (($rowCount == 1)
                || (!$hasBadges && $rowCount == 2 && $rowId == 1)
                || ($hasBadges && $rowCount == 3 && $rowId == 1))
                {
                    $memberName = match ($partId)
                    {
                        0 => "viewCountText",
                        1 => "publishedTimeText",
                        default => null
                    };
                    if (!is_null($memberName))
                    {
                        $result->{$memberName} = ParsingUtils::indexedRunsToRuns($part->text);
                    }
                }
            }
            
            if (isset($contents->badges))
            foreach ($contents->badges as $badge)
            {
                if (!isset($result->badges))
                    $result->badges = [];

                $badgeInner = $badge->badgeViewModel;
                $result->badges[] = (object)[
                    "metadataBadgeRenderer" => (object)[
                        "label" => $badgeInner->badgeText,
                        "style" => match ($badgeInner->badgeStyle)
                        {
                            "BADGE_COMMERCE" => "BADGE_STYLE_TYPE_YPC",
                            default => "BADGE_STYLE_TYPE_SIMPLE"
                        }
                    ]
                ];
            }
        }

        $result->navigationEndpoint = $this->viewModel->rendererContext->commandContext->onTap->innertubeCommand;

        return $result;
    }
}
