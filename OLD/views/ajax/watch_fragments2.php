<?php
const FRAGS = 'watch_fragments2/';

$fragsId = $_GET['frags'] ?? '';
switch ($fragsId) {
    case 'comments':
        include(FRAGS.'comments.php');
        break;
    case 'guide':
        include(FRAGS.'guide.php');
        break;
    case '':
    default:
        break;
}