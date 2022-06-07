<?php

\Rehike\TemplateFunctions::register('resolveBrowseIdUrl', function ($id) {
   if (!isset($id)) {
      return "";
   }
   $url = "";
   $idType = substr($id, 0, 2);
   $id = substr($id, 2, strlen($id));
   switch ($idType) {
      case 'UC':
         $url = '/channel/UC' . $id;
         break;
      case 'FE':
      default:
         $url = '/feed/' . $id;
         break;
   }
   return $url;
});