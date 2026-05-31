<?php
namespace Rehike\Model\Masthead\AccountPicker;

use Rehike\i18n\i18n;
use Rehike\SignInV2\SignIn;
use Rehike\Model\Common\Thumb\MThumbSquare;
use Rehike\Util\ImageUtils;

class MAccountPickerClickcard
{
    public string $template = "masthead_account_picker";
    public string $id = "yt-masthead-account-picker";
    public string $cardAction = "yt.www.masthead.handleAccountPickerClick";
    public array $cardClass = [
        "yt-masthead-account-picker-card",
    ];
    public string $class = "yt-masthead-account-picker";
    public object $content;

    public function __construct()
    {
        $i18n = i18n::getNamespace("masthead");
        $signInInfo = SignIn::getSessionInfo();
        $activeChannel = $signInInfo->getCurrentChannel();

        $this->content = (object) [];
        $content = &$this->content;

        $content->email = (object) [
            "simpleText" => $signInInfo->getCurrentGoogleAccount()->getAccountEmail(),
            "navigationEndpoint" => (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//myaccount.google.com/u/" . $signInInfo->getCurrentGoogleAccount()->getAuthUserId(),
                    ]
                ]
            ]
        ];
        $channelAuthUserId = $activeChannel->getOwnerAccount()->getAuthUserId();
        $content->username = $activeChannel->getDisplayName();
        $content->subCount = $activeChannel->getLocalizedSubscriberCount();
        $content->photo = (object) [
            "simpleText" => $i18n->get("accountPickerPhotoChange"),
            "navigationEndpoint" => (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//myaccount.google.com/u/$channelAuthUserId/profile#profile_photo"
                    ]
                ]
            ],
            "thumb" => new MThumbSquare([
                "image" => ImageUtils::changeSize($activeChannel->getAvatarUrl(), 64),
                "size" => 64,
                "delayload" => true
            ])
        ];
        $content->buttons = [];
        $content->buttons[] = new MAccountPickerStudioButton();
        $content->buttons[] = new MAccountPickerSettingsButton();

        $content->footer = [];
        $content->footer[] = new MAccountPickerAddButton();
        $content->footer[] = new MAccountPickerSignOutButton();
        
        $googleAccounts = $signInInfo->getGoogleAccounts();
        if (count($googleAccounts) > 1)
        {
            $this->cardClass[] = "yt-masthead-multilogin";
            
            $content->otherAccounts = [];
            
            foreach ($googleAccounts as $acc)
            {
                if ($acc->isActive())
                    continue;
                
                $defaultChannel = $acc->getYoutubeChannels()[0] ?? null;
                
                if ($defaultChannel)
                {
                    $content->otherAccounts[] = new MAccountPickerDelegateAccountItem($defaultChannel);
                }
                else
                {
                    \Rehike\Logging\DebugLogger::print("[MAccountPickerClickcard] Could not find YouTube channels for Google account \""
                        . $acc->getAccountEmail() ?? $acc->getGaiaId() ?? "<unknown>" . "\"");
                }
            }
        }
    }
}