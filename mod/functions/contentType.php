<?php

RehikeRegisterSharedFunction('contentType', function($type) {
   header('Content-Type: ' . $type);
});