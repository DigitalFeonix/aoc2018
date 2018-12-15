<?php

$input = file_get_contents('./inputs/input01-1.txt');
$inputs = explode("\n", trim($input));
$freq = 0;

foreach ($inputs as $adj)
{
    $freq += trim($adj);
}

echo $freq,"\n";
