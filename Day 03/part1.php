<?php

$input = file_get_contents('./inputs/input03-1.txt');
$inputs = explode("\n", trim($input));

$total = count($inputs);
$regex = '/#(?<id>\d+) @ (?<x>\d+),(?<y>\d+): (?<w>\d+)x(?<h>\d+)/';

$overlaps = 0;

$tmp = array_fill(0, 1000, '.');
$fab = array_fill(0, 1000, $tmp);

function draw_pattern(&$fab, $pat)
{
    for ($i = 0; $i < $pat['h']; $i++)
    {
        $y = $pat['y'] + $i;

        for ($j = 0; $j < $pat['w']; $j++)
        {
            $x = $pat['x'] + $j;

            $content = $fab[$y][$x];

            $fab[$y][$x] = ($content != '.')
                ? 'X'
                : '#';
        }
    }
}

foreach ($inputs as $input)
{
    preg_match($regex, $input, $claim);
    draw_pattern($fab, $claim);
}

foreach ($fab as $row)
{
    $count = array_count_values($row);

    if (key_exists('X', $count))
    {
        $overlaps += $count['X'];
    }
}

echo $overlaps,"\n";
