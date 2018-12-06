<?php

$input = file_get_contents('./inputs/input06-1.txt');
$input = trim($input);

$side = 400;

$coords = explode("\n", $input);

$area = 0;

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

        $sum = array_sum($dist);

        if ($sum < 10000)
        {
            $area++;
        }
    }
}

echo 'Part 2: ',$area,"\n";

