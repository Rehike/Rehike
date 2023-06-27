<?php
namespace Rehike\Model\Footer;

use Rehike\i18n;
use Rehike\Model\Common\MButton;
use Rehike\ConfigManager\ConfigManager;
use Rehike\Player\Configurable;

class MPickerLanguageButton extends MButton {
    public $targetId = "yt-picker-language-button";
    public $hasArrow = true;
}