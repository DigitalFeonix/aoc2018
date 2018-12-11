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

function get_group_power($x,$y,$s)
{
    global $cells;

    $x1 = $x - 1;
    $y1 = $y - 1;

    $sum = 0;

    if ($y > 1 && $x > 1) { $sum += $cells[$y1][$x1]; }
    if ($x > 1) { $sum -= $cells[$y1+$s][$x1]; }
    if ($y > 1) { $sum -= $cells[$y1][$x1+$s]; }

    $sum += $cells[$y1+$s][$x1+$s];

    //$sum = $cells[$y][$x] - $cells[$y+$s][$x] - $cells[$y][$x+$s] + $cells[$y+$s][$x+$s];

    return $sum;
}

$cells = array_fill(1, 300, array_fill(1, 300, 0));

foreach ($cells as $y => $row)
{
    $row_intergral = 0;

    foreach ($row as $x => $cell)
    {
        // get value for same X from previous row
        $adj = ($y > 1) ? $cells[$y-1][$x] : 0;
        $row_intergral += get_power($x,$y);
        $cells[$y][$x] = $row_intergral + $adj;
    }
}

$best_key = '';
$best_val = 0;

for ($s = 1; $s <= 300; $s++)
{
    for ($y = 1; $y < count($cells) - ($s - 1); $y++)
    {
        $row = $cells[$y];

        for ($x = 1; $x < count($row) - ($s - 1); $x++)
        {
            $val = get_group_power($x, $y, $s);
            if ($val > $best_val)
            {
                $best_val = $val;
                $best_key = sprintf('%d,%d,%d', $x, $y, $s);
            }
        }
    }

    // echo $s,' - ',$best_val,"\n";
}

echo $best_key,' => ',$best_val,"\n";
