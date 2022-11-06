<?php
namespace Rehike\Model\Masthead;

use Rehike\Model\Common\MButton;
use Rehike\i18n;
use Rehike\Signin\API as SignIn;
use Rehike\ConfigManager\ConfigManager as Config;
use Rehike\Model\Common\Thumb\MThumbSquare;
use Rehike\Util\ImageUtils;

class MMasthead {
    /** @var string */
    public $a11ySkipNav;

    /** @var MAppbarGuideToggle */
    public $guideToggle;

    /** @var object */
    public $logoTooltip;

    /** @var string */
    public $countryCode;

    /** @var MMastheadSearch */
    public $searchbox;

    /** @var MButton[] */
    public $buttons = [];

    public function __construct($appbarEnabled) {
        $i18n = i18n::newNamespace("masthead");
        $i18n -> registerFromFolder("i18n/masthead");

        $this -> a11ySkipNav = $i18n -> a11ySkipNav;

        if ($appbarEnabled)
            $this -> guideToggle = new MAppbarGuideToggle();

        $this -> logoTooltip = $i18n -> logoTooltip;
        $this -> searchbox = new MMastheadSearchbox();

        $this -> notificationStrings = (object) [
            "none" => $i18n -> notificationsNone,
            "singular" => $i18n -> notificationsSingular,
            "plural" => $i18n -> notificationsPlural,
        ];

        switch (Config::getConfigProp("uploadMenuType")) {
            case "BUTTON":
                $this -> buttons[] = new MUploadButton();
                break;
            case "ICON":
                $this -> buttons[] = new MUploadIconButton();
                break;
            default:
                $this -> buttons[] = new MCreationMenu();
                break;
        }

        if (SignIn::isSignedIn()) {
            $this -> buttons[] = new MNotificationButton();
            $this -> buttons[] = new MAccountPickerButton();
        } else {
            $this -> buttons[] = new MSignInButton();
        }
    }
}

class MAppbarGuideToggle extends MButton {
    public $style = "STYLE_TEXT";
    public $size = "SIZE_DEFAULT";
    public $targetId = "appbar-guide-button";
    public $class = [
        "appbar-guide-toggle",
        "appbar-guide-clickable-ancestor"
    ];

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $this -> accessibility = (object) [
            "accessibilityData" => (object) [
                "controls" => "appbar-guide-menu",
                "label" => $i18n -> appbarGuideLabel
            ]
        ];

        $this -> icon = (object) [
            "iconType" => "APPBAR_GUIDE"
        ];
    }
}

class MMastheadSearchbox {
    /** @var string */
    public $placeholder;

    /** @var MButton */
    public $button;

    /** @var bool */
    public $autofocus;

    /** @var string */
    public $query;

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");
    
        $this -> placeholder = $i18n -> searchboxPlaceholder;
        $this -> button = new MButton([
            "style" => "STYLE_DEFAULT",
            "size" => "SIZE_DEFAULT",
            "text" => (object) [
                "simpleText" => $i18n -> searchboxPlaceholder
            ],
            "targetId" => "search-btn",
            "class" => [
                "search-btn-component",
                "search-button"
            ],
            "customAttributes" => (object) [
                "type" => "submit",
                "onclick" => "if (document.getElementById('masthead-search-term').value == '') return false; document.getElementById('masthead-search').submit(); return false;;return true;",
                "tabindex" => "2",
                "dir" => "ltr"
            ]
        ]);
    }
}

class MSignInButton extends MButton {
    public $style = "STYLE_PRIMARY";
    public $href = "https://accounts.google.com/ServiceLogin?service=youtube&amp;uilel=3&amp;hl=en&amp;continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fapp%3Ddesktop%26action_handle_signin%3Dtrue%26hl%3Den%26next%3D%252F%26feature%3Dsign_in_button&amp;passive=true";

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $this -> text = (object) [
            "simpleText" => $i18n -> signInButton
        ];
    }
}

class MUploadButton extends MButton {
    public $targetId = "upload-btn";

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $signInInfo = (object) SignIn::getInfo();
        $hasChannel = SignIn::isSignedIn() && isset($signInInfo -> ucid);
        if ($hasChannel) $ucid = $signInInfo -> ucid;

        $this -> text = (object) [
            "simpleText" => $i18n -> uploadButton
        ];
        $this -> navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => $hasChannel ? "//studio.youtube.com/channel/$ucid/videos/upload" : "/create_channel?upsell=upload&next=/"
                ]
            ]
        ];
    }
}

class MUploadIconButton extends MButton {
    public $targetId = "upload-btn";
    public $style = "STYLE_OPACITY";

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $signInInfo = (object) SignIn::getInfo();
        $hasChannel = SignIn::isSignedIn() && isset($signInInfo -> ucid);
        if ($hasChannel) $ucid = $signInInfo -> ucid;

        $this -> tooltip = $i18n -> uploadButton;
        $this -> navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => $hasChannel ? "//studio.youtube.com/channel/$ucid/videos/upload" : "/create_channel?upsell=upload&next=/"
                ]
            ]
        ];
        $this -> icon = (object) [
            "iconType" => "MATERIAL_UPLOAD"
        ];
    }
}

class MCreationMenu extends MButton {
    public $targetId = "yt-masthead-creation-button";
    public $attributes = [
        "force-position" => "true",
        "position-fixed" => "true",
        "orientation" => "vertical",
        "position" => "bottomleft"
    ];

    public function __construct() {
        $this -> clickcard = new MCreationClickcard();
        $this -> icon = (object) [];
    }
}


class MCreationClickcard {
    public $template = "masthead_creation_menu";
    public $cardClass = [
        "yt-scrollbar",
        "yt-masthead-creation-clickcard"
    ];
    public $id = "yt-masthead-creation-menu";
    public $cardId = "yt-masthead-creation-clickcard";

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $signInInfo = (object) SignIn::getInfo();
        $hasChannel = SignIn::isSignedIn() && isset($signInInfo -> ucid);
        if ($hasChannel) $ucid = $signInInfo -> ucid;

        $items = [];

        $items[] = new MCreationMenuItem(
            "upload",
            $i18n -> creationUpload,
            $hasChannel ? "//studio.youtube.com/channel/$ucid/videos/upload" : "/create_channel?upsell=upload&next=/"
        );
        $items[] = new MCreationMenuItem(
            "live",
            $i18n -> creationLive,
            $hasChannel ? "//studio.youtube.com/channel/$ucid/livestreaming" : "/create_channel?upsell=livestreaming&next=/"
        );
        if ($hasChannel) $items[] = new MCreationMenuItem(
            "post",
            $i18n -> creationPost,
            $hasChannel ? "/channel/$ucid/community?show_create_dialog=1" : "/create_chanel?upsell=community&next=/"
        );

        $this -> content = (object) [
            "items" => $items
        ];
    }
}

class MCreationMenuItem extends MButton {
    public function __construct($type, $label, $url) {
        $this -> targetId = "creation-$type-menu-item";
        $this -> type = $type;
        $this -> icon = (object) [
            "iconType" => "CREATION_" . strtoupper($type)
        ];
        $this -> text = (object) [
            "simpleText" => $label
        ];
        $this -> navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => $url
                ]
            ]
        ];
    }
}

class MNotificationButton extends MButton {
    public $targetId = "yt-masthead-notifications-button";
    public $class = [
        "sb-notif-off"
    ];
    public $attributes = [
        "force-position" => "true",
        "position-fixed" => "true",
        "orientation" => "vertical",
        "position" => "bottomleft"
    ];

    public function __construct() {
        $this -> accessibility = (object) [
            "accessibilityData" => (object) [
                "haspopup" => "true"
            ]
        ];
        $this -> icon = (object) [
            "iconType" => "BELL"
        ];
        $this -> text = (object) [
            "simpleText" => "0"
        ];
        $this -> clickcard = new MNotificationClickcard();
    }
}

class MNotificationClickcard {
    public $template = "masthead_notifications";
    public $id = "yt-masthead-notifications";
    public $cardAction = "yt.www.notifications.inbox.handleNotificationsClick";
    public $cardClass = [
        "yt-scrollbar",
        "yt-notification-inbox-clickcard"
    ];
    public $cardId = "yt-masthead-notifications-clickcard";

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $this -> content = (object) [];
        $this -> content -> title = $i18n -> notificationsTitle;
        $this -> content -> button = new MNotificationSettingsButton();
    }
}

class MNotificationSettingsButton extends MButton {
    public $targetId = "yt-masthead-notifications-settings";
    public $style = "STYLE_OPACITY";

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $this -> accessibility = (object) [
            "accessibilityLabel" => (object) [
                "label" => $i18n -> notificationsSettings
            ]
        ];
        $this -> icon = (object) [
            "iconType" => "ICON_ACCOUNT_SETTINGS"
        ];
        $this -> navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => "/account_notifications"
                ]
            ]
        ];
    }
}

class MAccountPickerButton extends MButton {
    public $noStyle = true;
    public $class = [
        "yt-masthead-user-icon"
    ];
    public $attributes = [
        "force-position" => "true",
        "position-fixed" => "true",
        "orientation" => "vertical",
        "position" => "bottomleft"
    ];

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");
        $signInInfo = (object) SignIn::getInfo();
        $this -> thumb = new MThumbSquare([
            "image" => ImageUtils::changeGgphtImageSize($signInInfo -> activeChannel["photo"], 27),
            "size" => 27,
            "delayload" => true
        ]);
        $this -> accessibility = (object) [
            "accessibilityData" => (object) [
                "label" => $i18n -> accountPickerLabel
            ]
        ];
        $this -> clickcard = new MAccountPickerClickcard();
    }
}

class MAccountPickerClickcard {
    public $template = "masthead_account_picker";
    public $id = "yt-masthead-account-picker";
    public $cardAction = "yt.www.masthead.handleAccountPickerClick";
    public $cardClass = [
        "yt-masthead-account-picker-card",
    ];
    public $class = "yt-masthead-account-picker";

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");
        $signInInfo = (object) SignIn::getInfo();
        $activeChannel = $signInInfo -> activeChannel;

        $this -> content = (object) [];
        $content = &$this -> content;

        $content -> email = (object) [
            "simpleText" => $signInInfo -> googleAccount["email"],
            "navigationEndpoint" => (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//myaccount.google.com/u/0"
                    ]
                ]
            ]
        ];
        $content -> username = $activeChannel["name"];
        $content -> subCount = $activeChannel["byline"];
        $content -> photo = (object) [
            "simpleText" => $i18n -> accountPickerPhotoChange,
            "navigationEndpoint" => (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//myaccount.google.com/u/0/profile#profile_photo"
                    ]
                ]
            ],
            "thumb" => new MThumbSquare([
                "image" => ImageUtils::changeGgphtImageSize($activeChannel["photo"], 64),
                "size" => 64,
                "delayload" => true
            ])
        ];
        $content -> buttons = [];
        $content -> buttons[] = new MAccountPickerStudioButton();
        $content -> buttons[] = new MAccountPickerSettingsButton();

        $content -> footer = [];
        $content -> footer[] = new MAccountPickerAddButton();
        $content -> footer[] = new MAccountPickerSignOutButton();
    }
}

class MAccountPickerStudioButton extends MButton {
    public $class = [
        "yt-masthead-picker-button",
        "yt-masthead-picker-button-primary"
    ];

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");
        $signInInfo = (object) SignIn::getInfo();
        $hasChannel = SignIn::isSignedIn() && isset($signInInfo -> ucid);

        if ($hasChannel) {
            $this -> text = (object) [
                "simpleText" => $i18n -> accountPickerStudio
            ];
            $this -> navigationEndpoint = (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//studio.youtube.com/"
                    ]
                ]
            ];
        } else {
            $this -> text = (object) [
                "simpleText" => $i18n -> accountPickerCreate
            ];
            $this -> navigationEndpoint = (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//studio.youtube.com/"
                    ]
                ]
            ];
        }
    }
}

class MAccountPickerSettingsButton extends MButton {
    public $class = [
        "yt-masthead-picker-button",
        "yt-masthead-picker-settings-button"
    ];

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $this -> navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => "/account"
                ]
            ]
        ];
        $this -> tooltip = $i18n -> accountPickerSettings;
        $this -> icon = (object) [
            "iconType" => "ICON_ACCOUNT_SETTINGS"
        ];
    }
}

class MAccountPickerAddButton extends MButton {
    public $class = ["yt-masthead-picker-button"];

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $this -> text = (object) [
            "simpleText" => $i18n -> accountPickerAddAccount
        ];
        $this -> navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => "//accounts.google.com/AddSession?passive=false&hl=en&continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fhl%3Den%26next%3D%252F%253Fdisable_polymer%253D1%26action_handle_signin%3Dtrue%26app%3Ddesktop&uilel=0&service=youtube"
                ]
            ]
        ];
    }
}

class MAccountPickerSignOutButton extends MButton {
    public $class = ["yt-masthead-picker-button"];

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $this -> text = (object) [
            "simpleText" => $i18n -> accountPickerSignOut
        ];
        $this -> navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => "/logout"
                ]
            ]
        ];
    }
}