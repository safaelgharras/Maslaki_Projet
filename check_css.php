<?php
$css = file_get_contents('assets/css/style.css');
$d = 0;
$line = 1;
for($i = 0; $i < strlen($css); $i++) {
    if($css[$i] == '{') $d++;
    if($css[$i] == '}') $d--;
    if($css[$i] == "\n") $line++;
    if($d < 0) {
        echo "Extra } at line $line\n";
        break;
    }
}
if($d > 0) echo "Missing $d closing braces. Ended at line $line\n";
if($d == 0) echo "Braces are balanced.\n";
