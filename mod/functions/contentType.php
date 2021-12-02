<?php

registerFunction('contentType', function($type) {
   header('Content-Type: ' . $type);
});