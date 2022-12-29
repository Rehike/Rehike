<?php
namespace Rehike\Model\Watch\Watch8;

use Rehike\TemplateFunctions;
use Rehike\ConfigManager\ConfigManager;
use Rehike\Util\ExtractUtils;
use Rehike\i18n;

use Rehike\Model\Watch\Watch8\{
    LikeButton\MLikeButtonRenderer,
    PrimaryInfo\MActionButton,
    PrimaryInfo\MOwner,
    PrimaryInfo\MSuperTitle
};

/**
 * Implement the model for the primary info renderer.
 * 
 * This is generally a restructuring of the Kevlar formatted data
 * returned by InnerTube with WEB v2 as parameters.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MVideoPrimaryInfoRenderer
{
    /** @var string */
    public $title = "";

    /** @var string */
    public $viewCount = "";

    /** @var object[] */
    public $superTitle;

    /** @var mixed */
    public $badges;

    /** @var MOwner */
    public $owner;

    /** @var MActionButton[] */
    public $actionButtons = [];

    /** @var MLikeButtonRenderer */
    public $likeButtonRenderer = [];

    // dataHost == get_called_class of caller
    public function __construct($dataHost, $videoId)
    {
        $info = &$dataHost::$primaryInfo ?? null;
        $i18n = i18n::newNamespace("watch/primary");
        $i18n->registerFromFolder("i18n/watch");

        if (!is_null($info))
        {
            $this->title = $info->title ?? null;

            // Also set title of the whole page from this property
            $dataHost::$title = TemplateFunctions::getText($this->title);

            if (isset($info->viewCount->videoViewCountRenderer))
            $this->viewCount = (true === ConfigManager::getConfigProp("appearance.noViewsText"))
            ? ExtractUtils::isolateViewCnt(TemplateFunctions::getText($info->viewCount->videoViewCountRenderer->viewCount))
            : TemplateFunctions::getText($info->viewCount->videoViewCountRenderer->viewCount) ?? null;
            $this->badges = $info->badges ?? null;
            $this->superTitle = isset($info->superTitleLink) ? new MSuperTitle($info->superTitleLink) : null;
            $this->likeButtonRenderer = new MLikeButtonRenderer($dataHost, $info->videoActions->menuRenderer, $videoId);
            $this->owner = new MOwner($dataHost);

            // Create action butttons
            $orderedButtonQueue = [];

            /*
             * Updated to just hardcode this (it's generally fine, any video can be added to a playlist)
             * 
             * To elaborate, the previous implementation iterated the available action buttons from
             * the InnerTube response, which usually worked.
             * 
             * However, as of August 2022, YouTube has been experimenting with a rewrite of the action
             * buttons architecture, seemingly in an attempt to fix the Kevlar frontend watch page
             * imploding cuz they have 12 action buttons with full labels on the screen and typically
             * only report and sometimes transcript in the overflow menu.
             * 
             * No, of course they didn't bother fixing this in the most intuitive ways, like having
             * hidden or dynamic label visibility (even though they always tell you what they are
             * if you hover over the buttons anyways so THERE IS NO FUCKING POINT TO THE ADDITIONAL
             * LABEL), instead they added a new type of button called "menuFlexibleItemRenderer" inside
             * a new container of "flexibleItems".
             * 
             * It seems that, instead of the most obvious change of going from:
             * 
             * 👍 12K 👎 DISLIKE ➡️ SHARE ⬇️ DOWNLOAD ❤️ THANKS ✂️ CLIP ➕ SAVE  ...
             * 
             * to:
             * 
             * 👍 12K 👎  ➡️  ⬇️  ❤️  ✂️  ➕   ...
             * 
             * or (they will never actually use the overflow menu tho):
             * 
             * 👍 12K 👎  ➡️  ➕   ...
             *          ┌───────────────┐
             *          │ 🚩 Report     │
             *          │ 🗒️ Transcript │
             *          │ ⬇️ Download   │
             *          │ ❤️ Thanks     │
             *          │ ✂️ Clip       │
             *          └───────────────┘
             * 
             * They instead decided to rework the API to "lazily" fix their broken ass frontend,
             * and this is why sometimes "add to" would randomly not render, because this experiment
             * only applies to ~1/10th of the unique visitor IDs.
             */
            if (!$dataHost::$isKidsVideo)
            {
                $orderedButtonQueue[] = MActionButton::buildAddtoButton($videoId);
            }

            // Share button should always be built unless this is a
            // Kids video
            if (!$dataHost::$isKidsVideo)
            {
                $shareButton = MActionButton::buildShareButton();

                if (null != $shareButton) $orderedButtonQueue[] = $shareButton;
            }

            // Report button shows as an action button for livestreams, rather than
            // residing in the overflow menu.
            if ($dataHost::$isLive)
            {
                $orderedButtonQueue[] = MActionButton::buildReportButton();
            }
            else
            {
                $orderedButtonQueue[] = MActionButton::buildMoreButton();
            }

            $this->actionButtons = &$orderedButtonQueue;
        }
    }
}