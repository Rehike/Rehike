<?php
namespace Rehike\Model\ViewModelConverter;

use Rehike\i18n\i18n;
use Rehike\Util\ParsingUtils;
use Rehike\YtApp;

/**
 * 
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class PlaylistHeaderViewModelConverter extends BasicVMC
{
    private string $playlistId = "";
    
    public function setPlaylistId(string $playlistId): void
    {
        $this->playlistId = $playlistId;
    }
    
    public function bake(): ?object
    {
        $i18n = i18n::getNamespace("playlist");
        $regexes = i18n::getNamespace("regex");
        $source = $this->viewModel;
        
        $playAllButton = null;
        $shareButton = null;
        $saveButton = null;
        
        foreach ($source->actions->flexibleActionsViewModel->actionsRows as $actionsRow)
        foreach ($actionsRow->actions as $action)
        {
            if (
                isset($action->buttonViewModel->iconName) && 
                $action->buttonViewModel->iconName == "PLAY_ARROW"
            )
            {
                $playAllButton = $action->buttonViewModel;
            }
            else if (
                isset($action->buttonViewModel->iconName) && 
                $action->buttonViewModel->iconName == "SHARE"
            )
            {
                $shareButton = $action->buttonViewModel;
            }
            else if (
                isset($action->toggleButtonViewModel->defaultButtonViewModel->buttonViewModel->iconName) && 
                $action->toggleButtonViewModel->defaultButtonViewModel->buttonViewModel->iconName == "PLAYLIST_ADD"
            )
            {
                $saveButton = $action->toggleButtonViewModel;
            }
        }
        
        $ownerText = null;
        $ownerEndpoint = null;
        
        $bylineRenderers = [];
        
        foreach ($source->metadata->contentMetadataViewModel->metadataRows as $rowIndex => $metadataRow)
        {
            if ($rowIndex == 0)
            {
                // TODO(isabella): This will probably not always be an avatar stack with author
                // metadata, as was also the case with the legacy InnerTube version.
                $avatarStack = $metadataRow->metadataParts[0]->avatarStack->avatarStackViewModel;
                
                $rawOwnerText = ParsingUtils::getText($avatarStack->text);
                $ownerText = $rawOwnerText;
                if (isset($regexes->getAllTemplates()->bylineChannelNameIsolator))
                {
                    if (preg_match($regexes->get("bylineChannelNameIsolator"), $rawOwnerText, $matches))
                    {
                        $ownerText = $matches[1];
                    }
                }
                
                $ownerEndpoint = $avatarStack->rendererContext->commandContext->onTap->innertubeCommand;
            }
            else
            {
                foreach ($metadataRow->metadataParts as $partIndex => $metadataPart)
                {
                    // The 0th item here is a string denoting the type of playlist, which we
                    // don't want to display.
                    if ($partIndex != 0)
                    {
                        $bylineRenderers[] = (object)[
                            "playlistBylineRenderer" => (object)[
                                "text" => (object)[
                                    "simpleText" => ParsingUtils::getText($metadataPart->text),
                                ],
                            ],
                        ];
                    }
                }
            }
        }
        
        $response = (object)[
            "playlistId" => $this->playlistId,
            "title" => ParsingUtils::getText($source->title->dynamicTextViewModel->text) ?? "",
            "playlistHeaderBanner" => (object)[
                "heroPlaylistThumbnailRenderer" => (object)[
                    "thumbnail" => (object)[
                        "thumbnails" => $source->heroImage->contentPreviewImageViewModel->image->sources,
                    ],
                    "onTap" => $playAllButton?->onTap?->innertubeCommand ?? (object)[
                        // Fallback data to avoid a crash in MPlaylistHeader:
                        "commandMetadata" => (object)[
                            "webCommandMetadata" => (object)[
                                "url" => "",
                            ],
                        ],
                    ],
                    "thumbnailOverlays" => (object)[
                        "thumbnailOverlayHoverTextRenderer" => (object)[
                            "text" => (object)[
                                "simpleText" => $playAllButton?->title,
                            ],
                        ],
                    ],
                ],
            ],
            "byline" => $bylineRenderers,
        ];
        
        if ($playAllButton)
        {
            $vmc = new ButtonViewModelConverter($playAllButton, (object)[]);
            
            $playButtonRenderer = $vmc->bakeButtonRenderer([]);
            $playButtonRenderer->text = (object)[
                "simpleText" => $i18n->get("playAll"),
            ];
            
            $response->playButton = (object)[
                "buttonRenderer" => $playButtonRenderer,
            ];
        }
        
        if ($shareButton)
        {
            $vmc = new ButtonViewModelConverter($shareButton, (object)[]);
            $response->shareButton = (object)[
                "buttonRenderer" => $vmc->bakeButtonRenderer([]),
            ];
        }
        
        if ($saveButton)
        {
            $vmc = new ToggleButtonViewModelConverter($saveButton, (object)[]);
            $response->saveButton = (object)[
                "toggleButtonRenderer" => $vmc->bakeToggleButtonRenderer([]),
            ];
        }
        
        if ($ownerText)
            $response->ownerText = $ownerText;
        
        if ($ownerEndpoint)
            $response->ownerEndpoint = $ownerEndpoint;
        
        return $response;
    }
}