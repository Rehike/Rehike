<?php
return [
    "getFormattedDate" => function($date = 0) {
        return date("F j, Y, h:i", $date);
    },
    "brandName" => "Rehike",
    "versionHeader" => "Version %s",
    "nightly" => "Nightly",
    "nightlyInfoTooltip" => "This release is bleeding edge and may contain irregular bugs.",
    "subheaderNightlyInfo" => "Current branch information",
    "nonGitNotice" => "This release of Rehike lacks Git information.",
    "nonGitExtended" => "This may occur if you downloaded the repository directly from GitHub, " .
                        "such as from the \"Download ZIP\" feature. Some version information may be lost or " .
                        "unavailable.",
    "syncGithubButton" => "Synchronize with GitHub",
    "failedNotice" => "Failed to get version information.",
    "remoteFailedNotice" => "Failed to get remote version information.",
    "remoteFailedExtended" => "Version information is limited.",
    "noDotVersionNotice" => "The .version file is missing or corrupted.",
    "noNewVersions" => "No new versions available.",
    "oneNewVersion" => "1 new version available.",
    "varNewVersions" => "%s new versions available.",
    "unknownNewVersions" => "This version is critically out of date.",
    "headingVersionInfo" => "Version information",
    "viewOnGithub" => "View on GitHub"
];