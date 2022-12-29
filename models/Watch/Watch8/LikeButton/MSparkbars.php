<?php
namespace Rehike\Model\Watch\Watch8\LikeButton;

/**
 * Define the sparkbars (sentiment bars; like to dislike ratio bar).
 */
class MSparkbars
{
    /** @var float */
    public $likePercent = 50;
    
    /** @var float */
    public $dislikePercent = 50;
    
    public function __construct($likeCount, $dislikeCount)
    {
        if (0 != $likeCount + $dislikeCount)
        {
            $this->likePercent = ($likeCount / ($likeCount + $dislikeCount)) * 100;
            $this->dislikePercent = 100 - $this->likePercent;
        }
    }
}