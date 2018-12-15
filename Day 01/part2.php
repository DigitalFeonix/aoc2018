<?php

$input = file_get_contents('./input');
$inputs = explode("\n", trim($input));
$freq = 0;

$freq_list = [];
$found = false;

do
{
    foreach ($inputs as $adj)
    {
        $freq += trim($adj);

        if (in_array($freq, $freq_list))
        {
            $found = true;
            break;
        }

        $freq_list[] = $freq;
    }
}
while (!$found);

echo $freq,"\n";
