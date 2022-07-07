<?php

\Rehike\TemplateFunctions::register('getText', function ($obj) {
   if (isset($obj->runs)) {
      //return '';
      $runs = $obj->runs;
      $response = '';
      for ($i = 0, $j = count($runs); $i < $j; $i++) {
         $response .= $runs[$i]->text;
      }
      return $response;
   } else if (isset($obj->simpleText)) {
      return $obj->simpleText;
   } else {
      return '';
   }
});