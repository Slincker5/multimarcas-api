<?php

use App\Models\Notification;
require __DIR__ . '/vendor/autoload.php';
$notification = new Notification();

$notification->cronNotification();