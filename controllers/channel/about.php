<?php
foreach ($ytdata->contents->twoColumnBrowseResultsRenderer->tabs as $tab) {
    if (isset($tab->tabRenderer->selected) and $tab->tabRenderer->selected == true) {
        $aboutTabContent = $tab->tabRenderer->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->channelAboutFullMetadataRenderer;
    }
}

$yt->page->subCount = @$yt->page->header->subscriptionButton->shortSubscriberCountText;
$yt->page->viewCount = ExtractUtils::isolateViewCnt(\Rehike\TemplateFunctions::getText(@$aboutTabContent->viewCountText));
$yt->page->joinDate = ExtractUtils::isolateViewCnt(\Rehike\TemplateFunctions::getText(@$aboutTabContent->joinedDateText));
$yt->page->aboutDescription = @$aboutTabContent->description;
$yt->page->aboutCountry = ExtractUtils::isolateViewCnt(\Rehike\TemplateFunctions::getText(@$aboutTabContent->country));
$yt->page->primaryLinks = @$aboutTabContent->primaryLinks;