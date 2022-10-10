<?php
namespace Rehike\Model\Share;

use Rehike\i18n;
use Rehike\Model\Share\MShareTabBar;

class ShareBoxModel {
    public static function bake($videoId, $title) {
        $response = (object) [];
        $i18n = i18n::newNamespace("share");
        $i18n -> registerFromFolder("i18n/share");

        $shortUrl = "https://youtu.be/" . $videoId;
        $response -> shortUrl = $shortUrl;
        $response -> videoId = $videoId;

        $tabs = new MShareTabBar([
            (object) [
                "text" => $i18n -> get("tabShare"),
                "type" => "services",
                "active" => true
            ],
            (object) [
                "text" => $i18n -> get("tabEmbed"),
                "type" => "embed",
                "active" => false
            ],
            (object) [
                "text" => $i18n -> get("tabEmail"),
                "type" => "email",
                "active" => false
            ]
        ]);
        $response -> tabs = $tabs -> tabs;

        $services = [];
        $fullUrl = "https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3D$videoId%26feature%3Dshare";
        $thumbnail = "https%3A%2F%2Fi.ytimg.com%2Fvi%2F$videoId%2Fmaxresdefault.jpg";

        $services[] = (object) [
            "icon" => "facebook",
            "tooltip" => $i18n -> get("serviceTooltip", "Facebook"),
            "url" => "https://www.facebook.com/dialog/share?app_id=87741124305&href=$fullUrl&display=popup",
            "width" => 530,
            "height" => 560
        ];

        $services[] = (object) [
            "icon" => "twitter",
            "tooltip" => $i18n -> get("serviceTooltip", "Twitter"),
            "url" => "https://twitter.com/intent/tweet?url=$fullUrl&text=$title&via=YouTube&related=YouTube%2CYouTubeTrends%2CYTCreators",
            "width" => 550,
            "height" => 420
        ];

        $services[] = (object) [
            "icon" => "blogger",
            "tooltip" => $i18n -> get("serviceTooltip", "Blogger"),
            "url" => "https://www.blogger.com/blog-this.g?n=$title&source=youtube&b=%3Ciframe+width%3D%22480%22+height%3D%22270%22+src%3D%22https%3A%2F%2Fwww.youtube.com%2Fembed%2F$videoId%22+frameborder%3D%220%22+allow%3D%22accelerometer%3B+autoplay%3B+encrypted-media%3B+gyroscope%3B+picture-in-picture%22+allowfullscreen%3E%3C%2Fiframe%3E&eurl=$thumbnail&quot=",
            "width" => 768,
            "height" => 468
        ];

        $services[] = (object) [
            "icon" => "reddit",
            "tooltip" => $i18n -> get("serviceTooltip", "reddit"),
            "url" => "https://www.reddit.com/submit?url=$fullUrl&title=$title",
            "width" => 1024,
            "height" => 650
        ];
        
        $services[] = (object) [
            "icon" => "tumblr",
            "tooltip" => $i18n -> get("serviceTooltip", "Tumblr"),
            "url" => "https://www.tumblr.com/widgets/share/tool?shareSource=legacy&url=$fullUrl&posttype=video&content=$fullUrl&caption=$title",
            "width" => 1024,
            "height" => 650
        ];

        $services[] = (object) [
            "icon" => "pinterest",
            "tooltip" => $i18n -> get("serviceTooltip", "Pinterest"),
            "url" => "https://pinterest.com/pin/create/button/?url=$fullUrl&description=$title&is_video=true&media=$thumbnail",
            "width" => 1024,
            "height" => 650
        ];

        $services[] = (object) [
            "icon" => "vkontakte",
            "tooltip" => $i18n -> get("serviceTooltip", "ВКонтакте"),
            "url" => "https://vkontakte.ru/share.php?url=$fullUrl",
            "width" => 1024,
            "height" => 650
        ];

        $services[] = (object) [
            "icon" => "linkedin",
            "tooltip" => $i18n -> get("serviceTooltip", "LinkedIn"),
            "url" => "https://www.linkedin.com/shareArticle?url=$fullUrl&title=$title&summary=$title&source=Youtube",
            "width" => 1024,
            "height" => 650
        ];

        $response -> services = $services;

        return $response;
    }
}