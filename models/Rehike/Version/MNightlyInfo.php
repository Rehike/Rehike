<?php
namespace Rehike\Model\Rehike\Version;

use Rehike\Controller\Version\GetVersionController as Controller;
use Rehike\i18n\i18n;
use Rehike\Version\VersionInfo;
use Rehike\i18n\Internal\DateTimeFormats;

class MNightlyInfo
{
    public string $headingText;
    public MNightlyInfoItem $primaryItem;
    public bool $updateAvailable = false;
    public ?MNightlyInfoUpdate $updateContainer = null;

    public function __construct(VersionInfo $data)
    {
        $strings = i18n::getNamespace("rehike/version");
        $this->headingText = $strings->get("subheaderNightlyInfo");
        $this->primaryItem = MNightlyInfoItem::fromVersionInfo($data);

        if (isset($data->remoteGit) && is_array($data->remoteGit))
        {
            if (isset($data->remoteGit[0]->commit->committer->date))
            {
                $base = $data->remoteGit[0];
                $latestRemoteTime = strtotime($base->commit->committer->date);
                $currentTime = 0;
                
                if ($output = shell_exec("git log -1 --pretty=%ct"))
                {
                    $currentTime = (int)$output;
                }
                else
                {
                    // If the git command line is unavailable, then use the
                    // less accurate timestamp from the .version file.
                    $currentTime = $data->time;
                }

                $latestRemoteSha = @$base->sha ?? "";

                // Sometimes the times can differ, but the hashes can be the
                // same. If it's the same hash, then of course the commits are
                // the same, so there is no update.
                $isDifferentHash = $data->currentHash != $latestRemoteSha;

                if ($latestRemoteTime > $currentTime && $isDifferentHash)
                {
                    $this->updateAvailable = true;
                    $this->updateContainer = new MNightlyInfoUpdate(
                        $base,
                        $data
                    );
                }
            }
        }
    }
}