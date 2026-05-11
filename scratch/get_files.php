<?php
$dir = 'assets/images/institutions/';
$files = array_diff(scandir($dir), array('.', '..', 'desktop.ini'));
foreach($files as $f) echo "$f\n";
