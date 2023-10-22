<?php

namespace Rehike\Model\Share;

use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;


// this *may* involve videos in playlists, but im not sure, since i could only find one screenshot of the email feature
// video showing the email feature: https://youtu.be/BiJzA9L3-Yk?t=172
class ShareEmailModel {

    public static function bake(string $videoId, string $title, string $userId, 
                                string $userName, string $desc) : object {


        $response = (object) [];
        $i18n = i18n::getNamespace("share");

        $response->strs = $i18n;
        
        $response->email_forms = [
            (object) [
                "placeholder" => $i18n->get("recipientsPlaceholder"),
                "title" => $i18n->get("recipientsAlt"),
                "class" => "share-email-recipients"
            ],
            (object) [
                "placeholder" => $i18n->get("optionalMessagePlaceholder"),
                "title" => $i18n->get("optionalMessageAlt"),
                "class" => "share-email-note"
            ]
        ];

        $response->videoId = $videoId;
        $response->title = $title;
        $response->user_name = $userName;
        $response->user_ucid = $userId;

        // assuming its the first 77 characters as shown in the video
        $response->desc = (strlen($desc) >= 77) ? (substr($desc, 0, 77) . "...") : $desc;


        
        // We need to use a mailto link since whatever servers YouTube used to email videos to users
        // (probably don't exist anymore. 
        // We have to manually incorporate the link and the text since you can't pass in HTML into
        // a mailto URL, meaning you can't write a link with "link text" in mailto.

        // All of this results in the final email being *very* butchered and clunky when compared to what
        // YouTube would originally send to the recipient(s), but...oh well.
        $response->send_email_btn = new MButton([
            "style" => "STYLE_PRIMARY",
            "targetId" => "send-email-button",
            "customAttributes" => [
                "target" => "_blank"
            ],

            // God this is so dirty, ugly, and any other adjective that can and should be used to describe this
            "onclick" => "yt.window.popup('mailto:' + document.querySelector('.share-email-recipients').value
            + '?subject=' + encodeURIComponent(document.querySelector('.share-email-preview-header').innerText)
            + '&body=' + encodeURIComponent('https://youtu.be/$videoId?feature=shared\\n\\n') 
            + encodeURIComponent(document.querySelector('span.share-email-preview-body').innerText));",

            "text" => (object) [
                "runs" => [
                    (object) [
                        "text" => $i18n->get("sendEmail")
                    ]
                ]
            ]
        ]);

        return $response;


    }
}