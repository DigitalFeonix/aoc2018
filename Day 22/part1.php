<?php

function visualize_map($map)
{
    foreach ($map as $y => $r)
    {
        $r = array_map(function($v){
            return $v['marker'];
        }, $r);

        echo implode('', $r), "\n";
    }
}

// $input  = file_get_contents("./input");
// $inputs = explode("\n", trim($input));

// depth: 11991
// target: 6,797

$depth = 11991;
$tx = 6;
$ty = 797;

if (!empty($argv[1]))
{
    $depth = 510;
    $tx = 10;
    $ty = 10;
}

$marks = ['.','=','|'];
$area = [];
$risk = 0;

for ($y = 0; $y <= $ty; $y++)
{
    $area[ $y ] = [];

    for ($x = 0; $x <= $tx; $x++)
    {
        $el = 0;
        $type = 0;
        $marker = '';

        // calculate Geologic Index
        if (($x == 0 && $y == 0) || ($x == $tx && $y == $ty))
        {
            $gi = 0;
        }
        else if ($y == 0)
        {
            $gi = $x * 16807;
        }
        else if ($x == 0)
        {
            $gi = $y * 48271;
        }
        else
        {
            $gi = $area[ $y - 1 ][ $x ]['el'] * $area[ $y ][ $x - 1 ]['el'];
        }

        // calculate the Erosion Level
        $el = ($gi + $depth) % 20183;
        $type = $el % 3;

        // for map making
        $marker = $marks[ $type ];

        if ($x == 0 && $y == 0) $marker = 'M';
        if ($x == $tx && $y == $ty) $marker = 'T';

        // by coincidence the type number is same as risk
        $risk += $type;

        // save info into area map
        $area[ $y ][ $x ] = compact('el', 'type', 'marker');
    }
}

#visualize_map($area);

echo $risk,"\n";
