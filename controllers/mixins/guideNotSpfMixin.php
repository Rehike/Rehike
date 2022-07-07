<?php
/**
 * Include guide on this page if it is
 * not being navigated to by SPF.
 */
if(!isset($yt->spf) or $yt->spf == false) {
    require "controllers/mixins/guideMixin.php";
}