<?php
include('../../config.php');
logDebug('backgroundTask');

//TODO: RUN THIS FROM A CRONJOB!!!!!!!

$cache = new ReadShowFiles($db);
$cache->runIt();
//this doesn't work for some reason
$cache2 = new GetMegaZips();
$cache2->runIt();
logDebug('backgroundTask complete');
exit;