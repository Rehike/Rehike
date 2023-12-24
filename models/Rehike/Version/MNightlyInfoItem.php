<?php
namespace Rehike\Model\Rehike\Version;

use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;
use Rehike\Version\VersionInfo;
use YukisCoffee\CoffeeTranslation\DateTimeFormats;

use const Rehike\Constants\GH_ENABLED;
use const Rehike\Constants\GH_REPO;

class MNightlyInfoItem
{
    public string $headingText;
    public string $branch = "";
    public string $commitHash = "";
    public string $fullCommitHash = "";
    public bool $isPreviousHash = false;
    public string $commitName = "";
    public string $commitDateTime = "";
    public object $ghButton;
    public array $buttons = [];

    public function __construct()
    {
        $strings = i18n::getNamespace("rehike/version");
        $this->headingText = $strings->get("subheaderNightlyInfo");
    }

    public static function fromVersionInfo(VersionInfo $data): self
    {
        $strings = i18n::getNamespace("rehike/version");
        $me = new self;

        if ($branch = @$data->branch)
        {
            $me->branch = $branch;
        }

        if ($hash = @$data->currentHash)
        {
            $me->commitHash = self::trimHash($hash);
            $me->fullCommitHash = $hash;
        }
        else if ($hash = @$data->previousHash)
        {
            $me->commitHash = self::trimHash($hash) . "+1";
            $me->fullCommitHash = $hash . "+1";
            $me->isPreviousHash = true;
        }

        if ($name = @$data->subject)
        {
            $me->commitName = $name;
        }

        // Attempt to retrieve the commit name from Git if available.
        // This behavior is only implemented on the version page, since it's
        // more efficient to only call it when it's displayed.
        if ($data->supportsDotGit && ($output = shell_exec("git log -1 --pretty=%B")))
        {
            $me->commitName = explode("\n", $output)[0];
        }

        if ($time = @$data->time)
        {
            $me->commitDateTime = $strings->formatDate(
                DateTimeFormats::EXPANDED_DATE_WITH_TIME,
                $time
            );
        }

        if ($data->supportsDotGit && ($output = shell_exec("git log -1 --pretty=%ct")))
        {
            $me->commitDateTime = $strings->formatDate(
                DateTimeFormats::EXPANDED_DATE_WITH_TIME,
                (int)$output
            );
        }

        if (GH_ENABLED && @$me->fullCommitHash)
        {
            $me->ghButton = (object)[];
            $me->ghButton->label = $strings->get("viewOnGithub");
            $me->ghButton->endpoint = "//github.com/" . GH_REPO . "/tree/{$me->fullCommitHash}";
        }

        return $me;
    }

    public static function fromGithubData(VersionInfo $versionInfo, object $data): self
    {
        $strings = i18n::getNamespace("rehike/version");
        $me = new self;

        if ($hash = @$data->sha)
        {
            $me->commitHash = self::trimHash($hash);
            $me->fullCommitHash = $hash;
        }

        if ($name = @$data->commit->message)
        {
            $me->commitName = explode("\n", $name)[0];
        }

        if ($time = @$data->commit->committer->date)
        {
            $me->commitDateTime = $strings->formatDate(
                DateTimeFormats::EXPANDED_DATE_WITH_TIME,
                strtotime($data->commit->committer->date)
            );
        }

        if (GH_ENABLED && @$me->fullCommitHash)
        {
            $me->ghButton = (object)[];
            $me->ghButton->label = $strings->get("viewOnGithub");
            $me->ghButton->endpoint = "//github.com/" . GH_REPO . "/tree/{$me->fullCommitHash}";
        }

		if (@$versionInfo->supportsDotGit)
		{
			$me->buttons[] = new class($strings) extends MButton {
				public function __construct($strings)
				{
					$this->setText($strings->get("gitManagerPullButton"));
					$this->size = "small";
                    $this->class[] = "git-manager-pull-button";
				}
			};
		}

        return $me;
    }

    private static function trimHash($hash)
    {
        return substr($hash, 0, 7);
    }
}