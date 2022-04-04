<?php

require('innertube.php');

$innertube = new Innertube\Environment();

$a = $innertube->request(
    'browse',
    (object) [
        'clientName' => 'WEB',
        'clientVersion' => '1.20200101.01.01'
    ]
);