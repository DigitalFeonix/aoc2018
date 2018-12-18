<?php

function visualize_map($map)
{
    foreach ($map as $y => $r)
    {
        echo implode('', $r), "\n";
    }
}

function get_resource_value($map)
{
    $trees = 0; // the sand that got wet
    $lumber = 0; // water retained in the clay

    foreach ($map as $y => $row)
    {
        $vals = array_count_values($row);

        if (key_exists('|', $vals)) $trees += $vals['|'];
        if (key_exists('#', $vals)) $lumber += $vals['#'];
    }

    return $trees * $lumber;
}


$input = file_get_contents("./input");
#$input = file_get_contents("./test");

$inputs = explode("\n", trim($input));

$map = [];

foreach ($inputs as $line)
{
    $map[] = str_split( trim($line) );
}

// should be enough to get to stable loop
$end = pow(10,2) * 5;
$values = [];

for ($i = 0; $i < $end; $i++)
{
    $new = $map;

    // . => | if 3+ |
    // | => # if 3+ #
    // # => # if 1+ # AND 1+ | else => .

    for ($y = 0, $ylen = count($map);  $y < $ylen; $y++)
    {
        for ($x = 0, $xlen = count($map[$y]); $x < $xlen; $x++)
        {
            $_ = $map[$y][$x];

            // get neighbors
            $n = [];

            if ($y > 0 && $x > 0) { $n[] = $map[$y-1][$x-1]; }
            if ($y > 0) { $n[] = $map[$y-1][$x]; }
            if ($y > 0 && $x < $xlen - 1) { $n[] = $map[$y-1][$x+1]; }
            if ($x > 0) { $n[] = $map[$y][$x-1]; }
            if ($x < $xlen - 1) { $n[] = $map[$y][$x+1]; }
            if ($y < $ylen - 1 && $x > 0) { $n[] = $map[$y+1][$x-1]; }
            if ($y < $ylen - 1) { $n[] = $map[$y+1][$x]; }
            if ($y < $ylen - 1 && $x < $xlen - 1) { $n[] = $map[$y+1][$x+1]; }

            // count up the values of the neighbors
            $v = array_count_values($n);

            // run forest of life rules
            switch ($_)
            {
                case '.':
                    if (key_exists('|', $v) && $v['|'] >= 3) { $new[$y][$x] = '|'; }
                    break;
                case '|':
                    if (key_exists('#', $v) && $v['#'] >= 3) { $new[$y][$x] = '#'; }
                    break;
                case '#':
                    if (key_exists('|', $v) && $v['|'] >= 1 && key_exists('#', $v) && $v['#'] >= 1) { $new[$y][$x] = '#'; }
                    else { $new[$y][$x] = '.'; }
                    break;
            }
        }
    }

    $map = $new;

    $values[$i+1] = get_resource_value($map);
}

// get the last value and find when it first showed up
list($last_val) = array_slice($values, -1);
$first_key = array_search($last_val, $values);

// get the repeating loop
$loop = array_slice($values, $first_key);
$left = pow(10,9) - $end - 1; // offseting the loop

echo $loop[ $left % count($loop) ],"\n";
