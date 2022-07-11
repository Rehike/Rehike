<?php
namespace Rehike\Model\Watch\Watch8;

use \Rehike\Model\Watch\Watch7\MVideoDiscussionRenderer;
use \Rehike\Model\Watch\Watch7\MVideoDiscussionNotice;

/**
 * Implements the watch8 subcontroller for the watch model
 * implementation.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Watch8Subcontroller
{
    /**
     * A static reference to the main model controller.
     */
    const MASTER = "Rehike\Model\Watch\WatchModel";

    /**
     * Called from the main watch model
     * 
     * @param object $data
     * @return object[]
     */
    public static function bakeResults(&$data)
    {
        // Create references
        $primaryInfo = &self::MASTER::$primaryInfo;
        $secondaryInfo = &self::MASTER::$secondaryInfo;
        $commentSection = &self::MASTER::$commentSection;

        $results = [];

        // Push primary info (if it exists)
        if (!is_null($primaryInfo)) $results[] = (object)[
            "videoPrimaryInfoRenderer" => new MVideoPrimaryInfoRenderer(self::MASTER)
        ];

        // Push secondary info (if it exists)
        if (!is_null($secondaryInfo)) $results[] = (object)[
            "videoSecondaryInfoRenderer" => new MVideoSecondaryInfoRenderer(self::MASTER)
        ];

        // Push comments (if they exist)
        if (!is_null($commentSection))
        {
            $content = @$commentSection->contents[0];

            if (isset($content->continuationItemRenderer))
            {
                // If the comment section exists, create a video
                // discussion renderer that contains its continuation.

                $continuationToken = $content->continuationItemRenderer
                    ->continuationEndpoint->continuationCommand->token;
                
                // Push the continuation token to yt global
                self::MASTER::$yt->commentsToken = $continuationToken;

                $results[] = (object)[
                    "videoDiscussionRenderer" => new MVideoDiscussionRenderer(
                        $continuationToken
                    )
                ];
            }
            else if (isset($content->messageRenderer))
            {
                // If the comment section renderer contains a message,
                // create a videoDiscussionNotice instead.
                $message = $content->messageRenderer->text;

                $results[] = (object)[
                    "videoDiscussionNotice" => new MVideoDiscussionNotice($message)
                ];
            }
        }

        return $results;
    }

    /**
     * Called from main watch model
     * 
     * This performs check for autoplay and moves the video
     * to its respective position if so.
     * 
     * @param object $data
     * @return object
     */
    public static function bakeSecondaryResults(&$data)
    {
        // Get data from the reference in the datahost
        $origResults = &self::MASTER::$secondaryResults;

        if (isset($origResults->results))
        {
            $secondaryResults = $origResults;

            if (self::shouldUseAutoplay($data))
            {
                $autoplayIndex = self::getRecomAutoplay($secondaryResults->results);

                // Move autoplay video to its own object
                $secondaryResults->autoplayRenderer = (object)[
                    "results" => [ $secondaryResults->results[$autoplayIndex] ]
                ];

                // Remove the original reference to prevent it from 
                // rendering twice
                array_splice($secondaryResults->results, $autoplayIndex, 1);
            }

            return $secondaryResults;
        }

        return null;
    }

    /**
     * Called from main watch model
     * 
     * This checks if the playlist is present and returns
     * the playlist data if so.
     * 
     * @param object $data
     * @return object
     */
    public static function bakePlaylist(&$data)
    {
        $playlist = &self::MASTER::$playlist;

        // Return null if there is no playlist, this
        // makes the templater ignore it.
        $out = null;

        if (!is_null($playlist))
        {
            $list = &$playlist->playlist;
            
            $out = $list;

            // Mostly Daylin's messy work
            // TODO: cleanup
            $countText = $list->videoCountText->runs;
            $curIndex = $countText[0]->text;
            $videoCount = $countText[2]->text;

            if ("1" == $videoCount)
            {
                $videoCount = "1 video";
            }
            else
            {
                $videoCount .= " videos";
            }

            $out->videoCountText = (object)[
                "currentIndex" => $curIndex,
                "videoCount" => $videoCount
            ];

            // "previous/next video ids also need a little work
            //  let's just catch two cases with one"
            // Copied from Daylin's implementation again
            $playlistId = &self::MASTER::$yt->playlistId;

            $curIndexInt = &$list->localCurrentIndex;
            $prevIndexInt = $curIndexInt - 1;
            $nextIndexInt = $curIndexInt + 1;
            $prevId = $playlist->contents[$prevIndexInt]
                ->playlistPanelVideoRenderer->videoId ?? null
            ;
            $prevUrl = "/watch?v={$prevId}&index={$prevIndexInt}&list={$playlistId}";
            $nextId = $playlist->contents[$nextIndexInt]
                ->playlistPanelVideoRenderer->videoId ?? null
            ;
            $nextUrl = "/watch?v={$nextId}&index={$nextIndexInt}&list={$playlistId}";

            // Push those to output
            $out->previousVideo = [
                "id" => $prevId,
                "url" => $prevUrl
            ];

            $out->previousVideo = [
                "id" => $nextId,
                "url" => $nextUrl
            ];
        }

        return $out;
    }

    /**
     * Determine autoplay use status
     * 
     * @return bool
     */
    public static function shouldUseAutoplay(&$data)
    {
        /**
         * TODO: Specific master check to disable globally,
         * useful for building watch7/etc. later.
         */

        // Disable if watch playlists available at all.
        if (is_null(self::MASTER::$playlist))
        {
            return true;
        }

        // If none of the conditions above are hit, always
        // return false as a catch all
        return false;
    }

    /**
     * Get autoplay recommendation
     * 
     * @param object $results (index of the results)
     * @return int The index of the recommendation.
     */
    public static function getRecomAutoplay(&$results)
    {
        for ($i = 0; $i < count($results); $i++) if (isset($results[$i]->compactVideoRenderer))
        {
            return $i;
        }

        return 0;
    }
}