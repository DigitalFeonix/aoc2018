<?php

$input = file_get_contents('./inputs/input03-1.txt');
$inputs = explode("\n", trim($input));

$total = count($inputs);
$regex = '/#(?<id>\d+) @ (?<x>\d+),(?<y>\d+): (?<w>\d+)x(?<h>\d+)/';

$claims = array_fill(1, $total, FALSE);

for ($i = 0; $i < $total; $i++)
{
    preg_match($regex, $inputs[$i], $claim1);

    for ($j = $i + 1; $j < $total; $j++)
    {
        preg_match($regex, $inputs[$j], $claim2);

        if ($claim1['x'] < $claim2['x'] + $claim2['w']
            && $claim1['x'] + $claim1['w'] > $claim2['x']
            && $claim1['y'] < $claim2['y'] + $claim2['h']
            && $claim1['y'] + $claim1['h'] > $claim2['y'])
        {
            $claims[ $claim1['id'] ] = TRUE;
            $claims[ $claim2['id'] ] = TRUE;
        }
    }
}

$good_claim = array_search(FALSE, $claims);

echo $good_claim,"\n";
