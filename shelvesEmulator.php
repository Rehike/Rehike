<?php

// 8 shelves per response
// prefer cloud chip bar, but also use channels occasionally

class ShelvesEmulator {
   public $cfg;
   
   public function processBaseResponse($base) {
      $index = getBrowseIndex($base);
      $index = $index->richGridRenderer;
      
      $baseData = (object) [];
      
      // this should never occur in 2021, but if it does
      if (!isset($index->header->feedFilterChipBarRenderer)) {
         $baseData->chipsCount = 0;
      } else {
         $chipsIndex = $index->header->feedFilterChipBarRenderer->contents;
         $baseData->chipsCount = count($chipsIndex) - 1;
         
         for ($i = 1; $i < $baseData->chipsCount; $i++) {
            
         }
         
      }
   }
   
   public function getBaseResponse() {
      $baseResponse = $this->youtubei('browse', 'FEwhat_to_watch');
      return $baseResponse;
   }
   
   public function getShelves() {
      // Get base response (this orientates the rest of the process)
      $base = getBaseResponse();
   }
   
   public function getBrowseIndex($browse) {
      if (isset($browse->response) && isset($browse->response->contents->twoColumnBrowseResultsRenderer)) {
         return $browse->response->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content;
      }
   }
   
   public function youtubei($baseApi = 'browse', $browseId = 'FEwhat_to_watch') {
      $apiUrl = 'https://www.youtube.com/youtubei/v1/'.$baseApi.'?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';
      $innertubeBody = generateInnertubeInfoBase('WEB', '2.20200101.01.01', $this->cfg->visitorData);
      $innertubeBody->browseId = $browseId;
      $ch = curl_init($apiUrl);
      
      curl_setopt_array($ch, [
          CURLOPT_HTTPHEADER => ['Content-Type: application/json',
          'x-goog-visitor-id: ' . urlencode(encryptVisitorData($visitor))],
          CURLOPT_POST => 1,
          CURLOPT_POSTFIELDS => $yticfg,
          CURLOPT_FOLLOWLOCATION => 0,
          CURLOPT_HEADER => 0,
          CURLOPT_RETURNTRANSFER => 1
      ]);
      
      $response = curl_exec($ch);
      $response = json_decode($response);
      
      curl_close($ch);
      return $response;
   }
   
   public function defaultiseCfg($key, $val) {
      if (!isset($cfg->{$key})) {
         $cfg->{$key} = $val;
      }
   }
   
   public function init($cfg) {
      if (!is_object($cfg)) return 'ERR_INVALID_CFG';
      $this->cfg = $cfg;
      
      $this->defaultiseCfg('hl', 'en');
      $this->defaultiseCfg('gl', 'US');
      $this->defaultiseCfg('visitorData', '');
   }
   
   public function __construct($cfg) {
      $this->init($cfg);
   }
}