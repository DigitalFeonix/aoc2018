<?php

include_once('./Vector3.php');

$input  = file_get_contents("./input");

if (!empty($argv[1]))
{
    $input  = file_get_contents("./test");
}

$inputs = explode("\n", trim($input));

$bots = [];

foreach ($inputs as $dat)
{
    preg_match('/pos=<(\-?\d+),(\-?\d+),(\-?\d+)>, r=(\d+)/', trim($dat), $m);

    $nano = new Vector3($m[1], $m[2], $m[3]);
    $nano->radius = $m[4];

    $bots[] = $nano;
}

usort($bots, function($a,$b){
    return ($b->radius <=> $a->radius);
});

#print_r($bots);

$in_range = 0; // lead is in range of itself)
$lead = null;

foreach ($bots as $bot)
{
    if ($lead == null)
    {
        $lead = $bot;
    }

    if ($lead->distTo($bot) <= $lead->radius) $in_range++;
}

echo $in_range,"\n";
