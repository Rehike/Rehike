<?php
namespace Rehike\UserPrefs;

/**
 * An int enum for flags.
 * 
 * @enum
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
final class UserPrefFlags
{
    public const FLAG_SAFE_SEARCH = 0;
    public const FLAG_GRID_VIEW_SEARCH_RESULTS = 1;
    public const FLAG_EMBED_NO_RELATED_VIDEOS = 2;
    public const FLAG_HTML5_THREED = 3;
    public const FLAG_GRID_VIEW_VIDEOS_AND_CHANNELS = 4;
    public const FLAG_WATCH_EXPAND_ABOUT_PANEL = 5;
    public const FLAG_WATCH_EXPAND_MOREFROM_PANEL = 6;
    public const FLAG_WATCH_COLLAPSE_RELATED_PANEL = 7;
    public const FLAG_WATCH_COLLAPSE_PLAYLIST_PANEL = 8;
    public const FLAG_WATCH_COLLAPSE_QUICKLIST_PANEL = 9;
    public const FLAG_WATCH_EXPAND_ALSOWATCHING_PANEL = 10;
    public const FLAG_WATCH_COLLAPSE_COMMENTS_PANEL = 11;
    public const FLAG_STATMODULES_INBOX_COLLAPSED = 12;
    public const FLAG_STATMODULES_ABOUTYOU_COLLAPSED = 13;
    public const FLAG_STATMODULES_ABOUTVIDEOS_COLLAPSED = 14;
    public const FLAG_HIDE_WATCH_AUTOSHARE_PROMOTION = 15;
    public const FLAG_PERSONALIZED_HOMEPAGE_FEED_FEATURED_COLLAPSED = 16;
    public const FLAG_PERSONALIZED_HOMEPAGE_FEED_RECOMMENDED_COLLAPSED = 17;
    public const FLAG_PERSONALIZED_HOMEPAGE_FEED_SUBSCRIPTIONS_COLLAPSED = 18;
    public const FLAG_PERSONALIZED_HOMEPAGE_FEED_POPULAR_COLLAPSED = 19;
    public const FLAG_PERSONALIZED_HOMEPAGE_FEED_FRIENDTIVITY_COLLAPSED = 20;
    public const FLAG_SUGGEST_ENABLED = 21;
    public const FLAG_HAS_SUGGEST_ENABLED = 22;
    public const FLAG_WATCH_BETA_PLAYER = 23;
    public const FLAG_HAS_REDIRECTED_TO_LOCAL_SITE = 24;
    public const FLAG_ACCOUNT_SHOW_PLAYLIST_INFO = 25;
    public const FLAG_HAS_TAKEN_CHANNEL_SURVEY = 26;
    public const FLAG_HIDE_TOOLBAR = 27;
    public const FLAG_SHOWN_LANG_OPT_OUT = 28;
    public const FLAG_HAS_REDIRECTED_TO_LOCAL_LANG = 29;
    public const FLAG_SHOWN_COUNTRY_OPT_OUT = 30;
    public const FLAG_UPLOAD_BETA_OPTSET = 31;
    public const FLAG_UPLOAD_BETA_OPTIN = 32;
    public const FLAG_HIDE_MASTHEAD = 33;
    public const FLAG_TV_PARITY = 34;
    public const FLAG_TV_AUTO_FULLSCREEN_OFF = 35;
    public const FLAG_TV_AUTO_PLAY_NEXT_OFF = 36;
    public const FLAG_TV_ENABLE_MULTIPLE_CONTROLLERS = 37;
    public const FLAG_TV_RESERVED = 38;
    public const FLAG_LIGHT_HOMEPAGE = 39;
    public const FLAG_REDLINE_HIDE_TOAST = 40;
    public const FLAG_ANNOTATIONS_EDITOR_WATCH_PAGE_DEFAULT_OFF = 41;
    public const FLAG_REDLINE_HIDE_START_MESSAGE = 42;
    public const FLAG_ANNOTATIONS_LOAD_POLICY_BY_DEMAND = 43;
    public const FLAG_EMBED_DELAYED_COOKIES = 44;
    public const FLAG_HD_TIP_DEMOTE = 45;
    public const FLAG_NEWS_TIP_DEMOTE = 46;
    public const FLAG_UPLOAD_RESTRICT_TIP_DEMOTE = 47;
    public const FLAG_YPP_HIDE_INVITE_SPAM_BOX = 48;
    public const FLAG_YPP_HIDE_NEEDS_ADSENSE_BOX = 49;
    public const FLAG_YPP_HIDE_NEEDS_TRAINING_BOX = 50;
    public const FLAG_SKIP_CONTRINTER = 51;
    public const FLAG_EMBED_DEFAULT_HD = 52;
    public const FLAG_ENABLE_FILTER_WORDS = 53;
    public const FLAG_OPTED_IN_FOR_COMMENTS = 54;
    public const FLAG_HQ_SETTING_SAVED = 55;
    public const FLAG_HAS_TAKEN_WATCH_PAGE_SURVEY = 56;
    public const FLAG_SERVE_MOBILE_HQ_VIDEO = 57;
    public const FLAG_SAFETY_CONTENT_MODE = 58;
    public const FLAG_HIDE_PROMO_ACTIVITY_SUBSCRIPTIONS = 59;
    public const FLAG_MOBILE_APP_OPTOUT = 60;
    public const FLAG_HTML5_BETA = 61;
    public const FLAG_LITE_WATCH = 62;
    public const FLAG_ANNOTATIONS_EDITOR_WATCH_PAGE_DEFAULT_ON = 63;
    public const FLAG_WATCH5_OPTIN = 64;
    public const FLAG_CAPTIONS_DEFAULT_OFF = 65;
    public const FLAG_AUTO_CAPTIONS_DEFAULT_ON = 66;
    public const FLAG_LITE_WATCH_OPT_OUT = 67;
    public const FLAG_FBPROMO_OPT_OUT = 68;
    public const FLAG_HIDE_CHROME_PROMOS = 69;
    public const FLAG_HOMEPAGE_ALL_VS_SUB_VIEW = 70;
    public const FLAG_MYVIDEOSMANAGER_BETA_OPTOUT = 71;
    public const FLAG_HIDE_VIDEO_EDITOR_GUIDED_HELP = 72;
    public const FLAG_SAFETY_MODE_CHANGED_MANUALLY = 73;
    public const FLAG_LIVE_COMMENTS_SCROLL = 74;
    public const FLAG_USE_FLASH_EMBED_CODE = 75;
    public const FLAG_AUTOPLAY_PLAYLISTS_OFF = 76;
    public const FLAG_QUICKLIST_COLLAPSED = 77;
    public const FLAG_HIDE_CMS_DETAIL_GUIDED_HELP = 78;
    public const FLAG_USE_IFRAME_EMBED_CODE = 79;
    public const FLAG_HTML5_OPT_OUT = 80;
    public const FLAG_EMERALD_SEA_YT_OPT_OUT = 81;
    public const FLAG_MINIMIZE_SIGNUP_PROMO = 82;
    public const FLAG_HOMEPAGE_FEED_OPT_IN = 83;
    public const FLAG_HOMEPAGE_FEED_OPT_OUT = 84;
    public const FLAG_HOMEPAGE_FEED_OPT_IN_PROMO_DISMISSED = 85;
    public const FLAG_HOMEPAGE_FEED_VIEW1 = 86;
    public const FLAG_HOMEPAGE_FEED_VIEW2 = 87;
    public const FLAG_CREATE_PROMO_OPT_OUT = 88;
    public const FLAG_240P_LIGHT_OPT_IN = 89;
    public const FLAG_CREATIVE_COMMONS_PROMO_OPT_OUT = 90;
    public const FLAG_PARTNER_EMAIL_UPDATES_PROMO_OPT_OUT = 91;
    public const FLAG_MY_REV_SHARE_MOVE_NOTIFICATION = 92;
    public const FLAG_ENHANCE_PROMO_OPT_OUT = 93;
    public const FLAG_YPP_HIDE_NEEDS_SIGNED_CONTRACT_BOX = 94;
    public const FLAG_HIDE_IVPE_PROMO_BOX = 95;
    public const FLAG_HIDE_HOMEPAGE_GUIDED_HELP = 96;
    public const FLAG_HIDE_MONETIZATION_CONGRATULATIONS = 97;
    public const FLAG_HTML5_PREFERRED = 98;
    public const FLAG_HIDE_ADWORDS_PROMO = 99;
    public const FLAG_MDE_OPT_OUT = 100;
    public const FLAG_HITCHHIKER_BANNERS_DISABLED = 101;
    public const FLAG_HITCHHIKER_BACKGROUNDS_ENABLED = 102;
    public const FLAG_HITCHHIKER_GUIDED_HELP_HOME = 103;
    public const FLAG_HITCHHIKER_GUIDED_HELP_WATCH7 = 104;
    public const FLAG_HITCHHIKER_GUIDED_HELP_C4 = 105;
    public const FLAG_VIDEO_QUESTIONS = 106;
    public const FLAG_HIDE_FLASH_PROMO = 107;
    public const FLAG_HIDE_FEED_ITEM_DISMISSAL_PROMO = 108;
    public const FLAG_RENDER_GUIDE_EXPANDED = 109;
    public const FLAG_HIDE_GOOGLE_COOKIE_ALERT = 110;
    public const FLAG_HITCHHIKER_INITIAL_PROMO_DISMISS = 111;
    public const FLAG_HITCHHIKER_RET_USER_NO_SUBS_PROMO_DISMISS = 112;
    public const FLAG_RENDER_CONTEXT_EXPANDED = 113;
    public const FLAG_GUIDE_MODULE_ENOUGH_ROOM = 114;
    public const FLAG_WATCH7_HIDE_GUIDE_PROMO = 115;
    public const FLAG_WATCH7_HIDE_CONTEXT_PROMO = 116;
    public const FLAG_UPLOAD_HIDE_GPLUS_SHARING_PROMO = 117;
    public const FLAG_ENGAGEMENT_WIZARD_V2_DISMISS = 118;
    public const FLAG_HDPI = 119;
    public const FLAG_HIDE_CHANNELS4_NOTIFICATION = 120;
    public const FLAG_HIDE_CHANNELS4_PROMO_ON_C3 = 121;
    public const FLAG_BEHAVIORAL_CONSUMPTION_SURVEY_DISMISS = 122;
}