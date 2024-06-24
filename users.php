<?php

use App\Models\Premiun;
require __DIR__ . '/vendor/autoload.php';
$notification = new Premiun();

echo $notification->awardTopWeek();