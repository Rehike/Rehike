<?php
namespace Rehike\Model\ViewModelConverter;

use Rehike\ViewModelParser;
use Rehike\Util\ParsingUtils;
use Rehike\i18n\i18n;

/**
 * Converts comment view models to renderers.
 * 
 * @author The Rehike Maintainers
 */
class CommentsViewModelConverter extends BasicVMC
{
    private object $commentPayload;
    private object $toolbarStatePayload;
    private object $toolbarPayload;
    private object $commentSurfacePayload;

    private bool $isLiked = false;
    private bool $isDisliked = false;
    private bool $isHearted = false;
    
    private bool $isHeartEditable = false;
    private bool $isOwnComment = false;
    private bool $isCreatorComment = false;
    private bool $isVerifiedAuthor = false;

    public function bakeCommentRenderer(array $context = []): object
    {
        // This is to report to the user that the experiment is active in the GUI:
        \Rehike\YtApp::getInstance()->hasEvilCommentsExperimentBySatan = true;

        $parser = new ViewModelParser($this->viewModel, $this->frameworkUpdates);

        // We do not read from the shared payload because no such thing exists
        // for continuations (such as replies). Instead of requesting it at all,
        // I just find it to be better to store static strings like this in the
        // Rehike i18n system.
        $entData = $parser->getViewModelEntities([
            "commentKey" => "comment",
            "toolbarStateKey" => "toolbarState",
            "toolbarSurfaceKey" => "toolbarSurface",
            "commentSurfaceKey" => "commentSurface"
        ]);

        $commentPayload = $entData["comment"]->payload->commentEntityPayload;
        $this->commentPayload = $commentPayload;
        $toolbarStatePayload = $entData["toolbarState"]->payload->engagementToolbarStateEntityPayload;
        $this->toolbarStatePayload = $toolbarStatePayload;
        $toolbarPayload = $entData["toolbarSurface"]->payload->engagementToolbarSurfaceEntityPayload;
        $this->toolbarPayload = $toolbarPayload;
        $commentSurface = $entData["commentSurface"]->payload->commentSurfaceEntityPayload;
        $this->commentSurfacePayload = $commentSurface;

        $out = [];

        // To add insult to injury, they also moved comment text to use commandRuns.
        // I call for a firebombing on YouTube's offices tbh
        $commentText = ParsingUtils::commandRunsToRuns($commentPayload->properties->content);
        $publishedTimeText = $commentPayload->properties->publishedTime;
        $commentId = $commentPayload->properties->commentId;

        $this->isLiked = $toolbarStatePayload->likeState == "TOOLBAR_LIKE_STATE_LIKED";
        $this->isDisliked = ($toolbarStatePayload->dislikeState ?? $toolbarStatePayload->likeState) == "TOOLBAR_LIKE_STATE_DISLIKED";
        $this->isHearted = !in_array($toolbarStatePayload->heartState, [
            "TOOLBAR_HEART_STATE_UNHEARTED",
            "TOOLBAR_HEART_STATE_UNHEARTED_EDITABLE"
        ]);
        $this->isHeartEditable = in_array($toolbarStatePayload->heartState, [
            "TOOLBAR_HEART_STATE_UNHEARTED_EDITABLE",
            "TOOLBAR_HEART_STATE_HEARTED_EDITABLE"
        ]);

        $likeToolbarState = str_replace("TOOLBAR_LIKE_STATE_", "", $toolbarStatePayload->heartState);

        $this->isOwnComment = $commentPayload->author->isCurrentUser;
        $this->isCreatorComment = $commentPayload->author->isCreator;
        $this->isVerifiedAuthor = $commentPayload->author->isVerified;
        
        // Early 2024-06: They got rid of the browse command too, so we have to rebuild those too:
        if (isset($commentPayload->author->channelPageEndpoint->innertubeCommand))
        {
            $authorEndpoint = $commentPayload->author->channelPageEndpoint->innertubeCommand;
        }
        else
        {
            $browseId = $commentPayload->author->channelId;
            
            $authorEndpoint = (object)[
                "browseEndpoint" => (object)[
                    "browseId" => $browseId
                ],
                "commandMetadata" => (object)[
                    "webCommandMetadata" => (object)[
                        "url" => "/channel/$browseId"
                    ]
                ]
            ];
        }

        $out["commentId"] = $commentId;
        $out["publishedTimeText"] = (object)[
            "runs" => [
                (object)[
                    "text" => $publishedTimeText,
                    "navigationEndpoint" => $commentSurface->publishedTimeCommand->innertubeCommand
                ]
            ]
        ];
        $out["contentText"] = $commentText;
        $out["authorText"] = (object)[
            "simpleText" => $commentPayload->author->displayName
        ];
        $out["authorIsChannelOwner"] = $this->isCreatorComment;
        $out["authorEndpoint"] = $authorEndpoint;
        $out["authorThumbnail"] = (object)[
            "accessiblity" => (object)[
                "accessibilityData" => (object)[
                    "label" => $commentPayload->avatar->accessibilityText
                ]
            ],
            "thumbnails" => $commentPayload->avatar->image->sources
        ];

        if ($this->isVerifiedAuthor)
        {
            $out["authorCommentBadge"] = (object)[
                "authorCommentBadgeRenderer" => (object)[
                    "authorText" => (object)[
                        // There's also accessibility string like "確認済みユーザー username",
                        // but this case isn't handled by Rehike i18n, so it's excluded from
                        // here.
                        "simpleText" => $commentPayload->author->displayName
                    ],
                    "authorEndpoint" => $authorEndpoint,
                    "icon" => (object)[
                        "iconType" => "CHECK"
                    ],
                    "iconTooltip" => i18n::getRawString("global", "verified")
                ]
            ];
        }

        $out["actionButtons"] = (object)[
            "commentActionButtonsRenderer" => $this->internalBakeCommentActionButtonsRenderer()
        ];
        
        if (isset($toolbarPayload->menuCommand->innertubeCommand))
        {
            $out["actionMenu"] = $toolbarPayload->menuCommand->innertubeCommand->menuEndpoint->menu;
        }
        
        $i18n = i18n::getNamespace("comments");
        $expandButtonText = $i18n->get("expandButtonText");
        $collapseButtonText = $i18n->get("collapseButtonText");
        $out["expandButton"] = (object)[
            "buttonRenderer" => (object)[
                "accessibility" => (object)[
                    "accessibilityData" => (object)[
                        "label" => $expandButtonText
                    ]
                ],
                "text" => (object)[
                    "runs" => [
                        (object)[
                            "text" => $expandButtonText
                        ]
                    ]
                ],
                "size" => "SIZE_DEFAULT",
                "style" => "STYLE_TEXT"
            ]
        ];
        $out["collapseButton"] = (object)[
            "buttonRenderer" => [
                "accessibility" => (object)[
                    "accessibilityData" => (object)[
                        "label" => $collapseButtonText
                    ]
                ],
                "text" => (object)[
                    "runs" => [
                        (object)[
                            "text" => $collapseButtonText
                        ]
                    ]
                ],
                "size" => "SIZE_DEFAULT",
                "style" => "STYLE_TEXT"
            ]
        ];
        $out["voteStatus"] = $likeToolbarState;
        $out["isLiked"] = $this->isLiked;
        $out["isDisliked"] = $this->isDisliked;

        // They removed full like count too, fucking cunts.
        $out["voteCount"] = (object)[
            "accessibility" => (object)[
                "accessibilityData" => (object)[
                    "label" => $commentPayload->toolbar->likeCountA11y
                ]
            ],
            "simpleText" => $commentPayload->toolbar->likeCountNotliked
        ];

        if (isset($this->viewModel->pinnedText))
        {
            $out["pinnedCommentBadge"] = (object)[
                "pinnedCommentBadgeRenderer" => (object)[
                    "label" => $this->viewModel->pinnedText
                ]
            ];
        }

        if (isset($this->viewModel->linkedCommentText))
        {
            $out["linkedCommentBadge"] = (object)[
                "metadataBadgeRenderer" => (object)[
                    "label" => $i18n->get("linkedComment")
                ]
            ];
        }

        return (object)$out;
    }

    private function internalBakeCommentActionButtonsRenderer(): object
    {
        $commentPayload = $this->commentPayload;
        $toolbarPayload = $this->toolbarPayload;
        $i18n = i18n::getNamespace("comments");

        $out = [
            "likeButton" => (object)[
                "toggleButtonRenderer" => (object)[
                    "accessibilityData" => (object)[
                        "accessibilityData" => (object)[
                            "label" => $commentPayload->toolbar->likeCountA11y
                        ]
                    ],
                    "defaultIcon" => (object)[
                        "iconType" => "LIKE"
                    ],
                    "isToggled" => $this->isLiked,
                    "defaultServiceEndpoint" => (object)[
                        "performCommentActionEndpoint" => $toolbarPayload->likeCommand->innertubeCommand->performCommentActionEndpoint
                    ],
                    "toggledServiceEndpoint" => (object)[
                        "performCommentActionEndpoint" => $toolbarPayload->unlikeCommand->innertubeCommand->performCommentActionEndpoint
                    ],
                    "defaultTooltip" => $commentPayload->toolbar->likeInactiveTooltip,
                    "toggledTooltip" => $commentPayload->toolbar->likeActiveTooltip,
                ]
            ],
            "dislikeButton" => (object)[
                "toggleButtonRenderer" => (object)[
                    "defaultIcon" => (object)[
                        "iconType" => "DISLIKE"
                    ],
                    "isToggled" => $this->isDisliked,
                    "defaultServiceEndpoint" => (object)[
                        "performCommentActionEndpoint" => $toolbarPayload->dislikeCommand->innertubeCommand->performCommentActionEndpoint
                    ],
                    "toggledServiceEndpoint" => (object)[
                        "performCommentActionEndpoint" => $toolbarPayload->undislikeCommand->innertubeCommand->performCommentActionEndpoint
                    ],
                    "defaultTooltip" => $commentPayload->toolbar->dislikeInactiveTooltip,
                    "toggledTooltip" => $commentPayload->toolbar->dislikeActiveTooltip,
                ]
            ]
        ];

        if ($this->isHeartEditable || $this->isHearted)
        {
            $out["creatorHeart"] = (object)[
                "creatorHeartRenderer" => (object)[
                    "creatorThumbnail" => (object)[
                        "accessibility" => (object)[
                            "accessibilityData" => (object)[
                                "label" => $commentPayload->avatar->accessibilityText
                            ]
                        ],
                        "thumbnails" => [
                            (object)[
                                "url" => $commentPayload->toolbar->creatorThumbnailUrl,
                                "width" => 88,
                                "height" => 88
                            ]
                        ]
                    ],
                    "unheartedTooltip" => $commentPayload->toolbar->heartInactiveTooltip,
                    "heartedTooltip" => $commentPayload->toolbar->heartActiveTooltip,
                    "heartedAccessibility" => (object)[
                        "accessibilityData" => (object)[
                            "label" => $i18n->get("heartButtonText")
                        ]
                    ],
                    "unheartedAccessibility" => (object)[
                        "accessibilityData" => (object)[
                            "label" => $i18n->get("unheartButtonText")
                        ]
                    ],
                    "heartIcon" => (object)[
                        "iconType" => "FULL_HEART"
                    ],
                    "heartColor" => (object)[
                        "basicColorPaletteData" => (object)[
                            "foregroundTitleColor" => 4294901760
                        ]
                    ],
                    "isEnabled" => $this->isHeartEditable,
                    "isHearted" => $this->isHearted,
                    "kennedyHeartColorString" => "#ff0000"
                ]
            ];
        }

        if (isset($toolbarPayload->replyCommand->innertubeCommand))
        {
            $replyButtonText = $i18n->get("replyButtonText");
            $out["replyButton"] = (object)[
                "buttonRenderer" => (object)[
                    "navigationEndpoint" => $toolbarPayload->replyCommand->innertubeCommand,
                    "text" => (object)[
                        "runs" => [ (object)[ "text" => $replyButtonText ] ]
                    ],
                    "size" => "SIZE_DEFAULT",
                    "style" => "STYLE_TEXT"
                ]
            ];
        }

        return (object)$out;
    }
}