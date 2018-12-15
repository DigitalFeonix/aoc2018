<?php

$input = file_get_contents('./inputs/input06-1.txt');
$input = trim($input);

$side = 400;

$coords = explode("\n", $input);

$infinite = [];
$areas = array_fill(0, count($coords), 0);

for ($i = 0; $i < $side; $i++)
{
    for ($j = 0; $j < $side; $j++)
    {
        // grid_y = $i, grid_x = $j;

        $dist = [];

        foreach ($coords as $key => $coord)
        {
            list($x,$y) = explode(', ', trim($coord));
            $dist[] = abs($x - $j) + abs($y - $i);
        }

        // get shortest distance
        $tmp = array_count_values($dist);
        ksort($tmp);

        // get all the keys (the distances) first
        $keys = array_keys($tmp);

        // if $win = 1, then a single coord has shortest
        // if $win > 1, then it is a tie
        $win = array_shift($tmp);

        if ($win == 1)
        {
            // $min is the distance to a nearest point(s)
            $min = array_shift($keys);

            // which coord it is closest to
            $c = array_search($min, $dist);
            $areas[$c]++;

            // we are on an edge, so they have "infinite" area"
            if ($i == 0 || $j == 0 || $i == $side - 1 || $j == $side - 1)
            {
                if (!in_array($c, $infinite))
                {
                    $infinite[] = $c;
                }
            }
        }
    }
}

arsort($areas);

// get largest that is not "infinite"
foreach ($areas as $key => $area)
{
    if (!in_array($key, $infinite))
    {
        break;
    }
}

echo 'Part 1: ',$area,"\n";

