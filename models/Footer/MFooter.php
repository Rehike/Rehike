<?php
namespace Rehike\Model\Footer;


class MFooter {
    /** @var MFooterLink[] */
    public $primaryLinks = [];

    /** @var MFooterLink[] */
    public $secondaryLinks = [];

    public function __construct() {
        $this -> primaryLinks = [
            new MFooterLink((object) [
                "text" => "About",
                "href" => "/yt/about/"
            ]),
            new MFooterLink((object) [
                "text" => "Press",
                "href" => "/yt/press/"
            ]),
            new MFooterLink((object) [
                "text" => "Copyright",
                "href" => "/yt/copyright/"
            ]),
            new MFooterLink((object) [
                "text" => "Creators",
                "href" => "/yt/creators/"
            ]),
            new MFooterLink((object) [
                "text" => "Advertise",
                "href" => "/yt/advertise/"
            ]),
            new MFooterLink((object) [
                "text" => "Developers",
                "href" => "/yt/dev/"
            ]),
        ];
        $this -> secondaryLinks = [
            new MFooterLink((object) [
                "text" => "Terms",
                "href" => "/t/terms"
            ]),
            new MFooterLink((object) [
                "text" => "Privacy",
                "href" => "//www.google.com/intl/en/policies/privacy/"
            ]),
            new MFooterLink((object) [
                "text" => "Policy & Safety",
                "href" => "/yt/policyandsafety/"
            ]),
            new MFooterLink((object) [
                "text" => "Send feedback",
                "href" => "//support.google.com/youtube/?hl=en"
            ]),
            new MFooterLink((object) [
                "text" => "Test new features",
                "href" => "/testtube"
            ])
        ];
    }
}