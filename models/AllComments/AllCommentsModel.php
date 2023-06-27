<?php
namespace Rehike\Model\AllComments;

use Rehike\Util\ParsingUtils;
use Rehike\i18n;
use Rehike\Model\Comments\CommentThread;
use Rehike\Model\Comments\CommentsHeader;

class AllCommentsModel
{
    public static function bake(object $sdata, object $cdata, string $id): object
    {
        $i18n = i18n::newNamespace("all_comments")->registerFromFolder("i18n/all_comments");

        $response = (object) [];

        foreach ($sdata->contents->twoColumnSearchResultsRenderer->primaryContents->sectionListRenderer->contents as $content)
        {
            if (isset($content->itemSectionRenderer))
            {
                foreach ($content->itemSectionRenderer->contents as $icontent)
                {
                    if (@$icontent->videoRenderer->videoId == $id)
                    {
                        $response->video = $icontent;
                    }
                }
            }
        }

        if (!isset($response->video)) header("Location: /oops");

        $response->title = $i18n->pageTitle(ParsingUtils::getText($response->video->videoRenderer->title));

        $response->comments = (object) [];
        $response->comments->headerRenderer = CommentsHeader::fromData($cdata->onResponseReceivedEndpoints[0]->reloadContinuationItemsCommand->continuationItems[0]->commentsHeaderRenderer);

        CommentThread::bakeComments($cdata->onResponseReceivedEndpoints[1]->reloadContinuationItemsCommand)
        ->then(function($comments) use (&$response) {
            $response->comments->comments = $comments;
        });
        

        return $response;
    }
}