<?php

$input = file_get_contents('./input');
$inputs = explode("\n", trim($input));
$freq = 0;

foreach ($inputs as $adj)
{
    $freq += trim($adj);
}

echo $freq,"\n";
