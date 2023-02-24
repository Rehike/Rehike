<?php
return [
    "getFormattedDate" => function($date = 0) {
        return date("F j, Y, h:i", $date);
    },
    "brandName" => "Rehike",
    "versionHeader" => "Wersja %s",
    "nightly" => "Nightly",
    "nightlyInfoTooltip" => "To wydanie jest na granicy wytrzymałości i może zawierać nieregularne błędy.",
    "subheaderNightlyInfo" => "Obecne informacje o gałęzi",
    "nonGitNotice" => "To wydanie Rehike nie zawiera informacji o Git.",
    "nonGitExtended" => "Może to nastąpić, jeśli pobrałeś repozytorium bezpośrednio z GitHub, " .
                        "na przykład z funkcji \"Download ZIP\". Niektóre informacje o wersji mogą zostać utracone lub " .
                        "niedostępne.",
    "syncGithubButton" => "Synchronizuj z GitHubem",
    "failedNotice" => "Nie udało się uzyskać informacji o wersji.",
    "remoteFailedNotice" => "Nie udało się uzyskać informacji o wersji zdalnej.",
    "remoteFailedExtended" => "Informacje o wersji są ograniczone.",
    "noDotVersionNotice" => "Brakuje pliku .version lub jest on uszkodzony.",
    "noNewVersions" => "Brak dostępnych nowych wersji.",
    "oneNewVersion" => "Dostępna 1 nowa wersja.",
    "varNewVersions" => "Dostępne %s nowych wersji.",
    "unknownNewVersions" => "Ta wersja jest krytycznie przestarzała.",
    "headingVersionInfo" => "Informacje o wersji",
    "viewOnGithub" => "Zobacz na GitHubie",
    "extraInfo" => "Dodatkowe informacje",
    "operatingSystem" => "System operacyjny",
    "phpVersion" => "Wersja PHP"
];
