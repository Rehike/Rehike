<?php
namespace Rehike\Model\Guide;

/**
 * List of InnerTube icons.
 * 
 * This is required as the InnerTube icon names are beginning to change as part
 * of a server-side experiment. See issue #600 for more information.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
final class InnertubeIcons
{
    public const WHAT_TO_WATCH = [ "WHAT_TO_WATCH", "TAB_HOME_CAIRO" ];
    public const SHORTS = [ "TAB_SHORTS", "TAB_SHORTS_CAIRO" ];
    public const SUBSCRIPTIONS = [ "SUBSCRIPTIONS", "TAB_SUBSCRIPTIONS_CAIRO" ];
    public const WATCH_HISTORY = [ "WATCH_HISTORY", "WATCH_HISTORY_CAIRO" ];
    public const WATCH_LATER = [ "WATCH_LATER", "WATCH_LATER_CAIRO" ];
    public const LIKES_PLAYLIST = [ "LIKES_PLAYLIST", "LIKES_PLAYLIST_CAIRO" ];
    
    private function __construct() {}
}