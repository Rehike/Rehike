<?php
namespace Rehike\ResourceConstants;

const YTS_HOST = 's.ytimg.com';
const MODULAR_JS_PATH = 'www-en_US-vflkPQDpM';

function jsModuleUrl($name) {
    return '//' . YTS_HOST . '/yts/jsbin/' . MODULAR_JS_PATH . '/' . $name . '.js';
}

/**
 * Hack.
 * 
 * Also gross.
 * 
 * @author The Rehike Maintainers
 */
class ResourceContentsStore
{
    public static $resourceConstants;
}

$ytConstants = (object) [
    'pixelGif' => '//' . YTS_HOST . '/yts/img/pixel-vfl3z5WfW.gif',
    'css' => (object) [
        'www-core' => '//' . YTS_HOST . '/yts/cssbin/www-core-vflZ7bM6S.css',
        'www-pageframe' => '//' . YTS_HOST . '/yts/cssbin/www-pageframe-vflhkpWhK.css',
        'www-highcontrastmode' => '//' . YTS_HOST . '/yts/cssbin/www-highcontrastmode-vflCxtOoT.css',
        'www-guide' => '//' . YTS_HOST . '/yts/cssbin/www-guide-vflNDDMf7.css',
        'www-home-c4' => '//' . YTS_HOST . '/yts/cssbin/www-home-c4-vflopQeuE.css',
        'www-attribution' => '//' . YTS_HOST . '/yts/cssbin/www-attribution-vflhQnyPy.css',
        'www-results' => '//' . YTS_HOST . '/yts/cssbin/www-results-vfl67U2zJ.css',
        'www-account-settings'=> '//' .  YTS_HOST . '/yts/cssbin/www-account-settings-vfl9mNWIu.css',
        'www-creatorpage' => '//' . YTS_HOST . '/yts/cssbin/www-creatorpage-vflfgomxn.css',
        'www-error' => '//' . YTS_HOST . '/yts/cssbin/www-error-vflvD9R0Z.css'
    ],
    'css2x' => (object) [
        'www-core' => '//' . YTS_HOST . '/yts/cssbin/www-core-2x-vflNAXaGB.css',
        'www-pageframe' => '//' . YTS_HOST . '/yts/cssbin/www-pageframe-2x-vfl7zB5iD.css',
        'www-guide' => '//' . YTS_HOST . '/yts/cssbin/www-guide-2x-vflnLcFfr.css',
        'www-home-c4' => '//' . YTS_HOST . '/yts/cssbin/www-home-c4-2x-vflBmvDSR.css',
        'www-results' => '//' . YTS_HOST . '/yts/cssbin/www-results-2x-vflilAG3t.css'
    ],
    'jsModulesPath' => '//' . YTS_HOST . '/yts/jsbin/' . MODULAR_JS_PATH . '/',
    'js' => (object) [
        'scheduler/scheduler' => '//' . YTS_HOST . '/yts/jsbin/scheduler-vflyNP9EQ/scheduler.js',
        'spf/spf' => '//' . YTS_HOST . '/yts/jsbin/spf-vflRfjT3b/spf.js',
        'www-core/www-core' => '//' . YTS_HOST . '/yts/jsbin/www-core-vflWuPqdk/www-core.js',
        'www-searchbox/www-searchbox' => '//' . YTS_HOST . '/yts/jsbin/www-searchbox-vflV_B8yT/www-searchbox.js',
        'www-notfound/www-notfound' => '//' . YTS_HOST . '/yts/jsbin/www-notfound-vflsu8ylX/www-notfound.js',
        'www/base' => jsModuleUrl('base'),
        'www/common' => jsModuleUrl('common'),
        'www/angular_base' => jsModuleUrl('angular_base'),
        'www/channels_accountupload' => jsModuleUrl('channels_accountupload'),
        'www/channels' => jsModuleUrl('channels'),
        'www/dashboard' => jsModuleUrl('dashboard'),
        'www/downloadreports' => jsModuleUrl('downloadreports'),
        'www/experiments' => jsModuleUrl('experiments'),
        'www/feed' => jsModuleUrl('feed'),
        'www/instant' => jsModuleUrl('instant'),
        'www/legomap' => jsModuleUrl('legomap'),
        'www/promo_join_network' => jsModuleUrl('promo_join_network'),
        'www/results_harlemshake' => jsModuleUrl('results_harlemshake'),
        'www/results' => jsModuleUrl('results'),
        'www/results_starwars' => jsModuleUrl('results_starwars'),
        'www/subscriptionmanager' => jsModuleUrl('subscriptionmanager'),
        'www/unlimited' => jsModuleUrl('unlimited'),
        'www/watch' => jsModuleUrl('watch'),
        'www/ypc_bootstrap' => jsModuleUrl('ypc_bootstrap'),
        'www/ypc_core' => jsModuleUrl('ypc_core'),
        'www/channels_edit' => jsModuleUrl('channels_edit'),
        'www/live_dashboard' => jsModuleUrl('live_dashboard'),
        'www/videomanager' => jsModuleUrl('videomanager'),
        'www/watch_autoplayrenderer' => jsModuleUrl('watch_autoplayrenderer'),
        'www/watch_edit' => jsModuleUrl('watch_edit'),
        'www/watch_editor' => jsModuleUrl('watch_editor'),
        'www/watch_live' => jsModuleUrl('watch_live'),
        'www/watch_promos' => jsModuleUrl('watch_promos'),
        'www/watch_speedyg' => jsModuleUrl('watch_speedyg'),
        'www/watch_transcript' => jsModuleUrl('watch_transcript'),
        'www/watch_videoshelf' => jsModuleUrl('watch_videoshelf'),
        'www/ct_advancedsearch' => jsModuleUrl('www/ct_advancedsearch'),
        'www/my_videos' => jsModuleUrl('my_videos')
    ],
    'img' => (object) [
        'channels/c4/default_banner' => '//' . YTS_HOST . '/yts/img/channels/c4/default_banner-vfl7DRgTn.png',
        'channels/c4/default_banner_hq' => '//' . YTS_HOST . '/yts/img/channels/c4/default_banner_hq-vfl4dpY8T.png',
        "meh7" => "//" . YTS_HOST . "/yts/img/meh7-vflGevej7.png"
    ]
];

ResourceContentsStore::$resourceConstants = &$ytConstants;