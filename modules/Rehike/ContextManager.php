<?php
namespace Rehike;

/**
 * Used for storing information about the current session.
 * 
 * Because Rehike has limited internationalisation support at the moment,
 * this class remains relatively unused.
 * 
 * @author The Rehike Maintainers
 */
class ContextManager
{
    /**
     * Visitor data from InnerTube.
     * 
     * The server uses this information to synchronise user data between 
     * sessions if the user is logged out.
     * 
     * @var string
     */
    public static $visitorData;

    /**
     * Host language.
     * 
     * When implemented, this can be synchronised with the "hl" parameter of
     * the PREF cookie or retrieved from an initial response. As such, we don't
     * need to be mindful of legal values.
     * 
     * @var string
     */
    public static $hl = "en";

    /**
     * Global location.
     * 
     * Synchronised with the "gl" parameter of the PREF cookie.
     * 
     * @var string
     */
    public static $gl = "US";
}