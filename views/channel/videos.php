<?php
foreach ($ytdata->contents->twoColumnBrowseResultsRenderer->tabs as $tab) {
    if (isset($tab->tabRenderer->selected) and $tab->tabRenderer->selected == true) {
        $videosTabContent = $tab->tabRenderer->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->gridRenderer->items;
    }
}

$yt->videoList = $videosTabContent;