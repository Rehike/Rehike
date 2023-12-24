<?php
namespace Rehike\Model\Rehike\Version;

use Rehike\i18n\i18n;
use Rehike\Version\VersionInfo;

class MNightlyInfoUpdate
{
    public string $title;
    public string $tagline;
    public MNightlyInfoItem $item;

    public function __construct(object $data, VersionInfo $root)
    {
        $i18n = i18n::getNamespace("rehike/version");

        $this->title = $i18n->get("updatesAvailable");
        $this->item = MNightlyInfoItem::fromGithubData($root, $data);

        $number = null;
        foreach ($root->remoteGit as $i => $item)
        {
            if (@$item->sha == $root->currentHash)
            {
                $number = $i;
                break;
            }
        }

        if ($number == 1)
        {
            $this->tagline = $i18n->get("oneNewVersion");
        }
        else if ($number == null)
        {
            if ($root->supportsDotGit && exec("git log origin/master..HEAD"))
            {
                $this->tagline = $i18n->get("gitManagerUnpushedChanges");
            }
            else
            {
                $this->tagline = $i18n->get("unknownNewVersions");
            }
        }
        else
        {
            $this->tagline = $i18n->format(
                "varNewVersions",
                $i18n->formatNumber($number)
            );
        }
    }
}