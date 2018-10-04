<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>LuxSoft's - PHP info displayer</title>
</head>

<body>
<?php
echo "<h4>Hereafter you will see the PHP installation parameters on your server.</h4>";
echo "<div>LuxCal requires PHP 5.1 or higher - If lower, ask your ISP to upgrade.</div>";
echo "<br><br><br><br>".PHP_EOL;
phpinfo();
