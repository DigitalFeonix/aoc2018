<?php

$serial = 4842;

function get_power($x, $y)
{
    global $serial;

    $rack_id = $x + 10;
    $power = $rack_id * $y;
    $power += $serial;
    $power *= $rack_id;
    $power = floor($power / 100) % 10; // hundreds digits OR 0
    $power -= 5;

    return $power;
}

$cells = array_fill(1, 300, array_fill(1, 300, 0));

foreach ($cells as $y => $row)
{
    foreach ($row as $x => $cell)
    {
        $cells[$y][$x] = get_power($x,$y);
    }
}

$tot = [];

for ($y = 1; $y < count($cells) - 2; $y++)
{
    $row = $cells[$y];

    for ($x = 1; $x < count($row) -2; $x++)
    {
        $sum = $cells[$y][$x] + $cells[$y][$x+1] + $cells[$y][$x+2]
            + $cells[$y+1][$x] + $cells[$y+1][$x+1] + $cells[$y+1][$x+2]
            + $cells[$y+2][$x] + $cells[$y+2][$x+1] + $cells[$y+2][$x+2];
        $tot[sprintf('%d,%d', $x, $y)] = $sum;
    }
}

$max = max($tot);
$key = array_search($max, $tot);

echo $key,' => ',$max,"\n";
