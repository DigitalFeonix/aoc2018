<?php

include_once('./Vector4.php');

$input  = file_get_contents("./input");

if (!empty($argv[1]))
{
    $input  = file_get_contents("./{$argv[1]}");
}

$inputs = explode("\n", trim($input));

$const_dist = 3;

$points = [];

foreach ($inputs as $dat)
{
    preg_match('/(\-?\d+),(\-?\d+),(\-?\d+),(\-?\d+)/', trim($dat), $m);

    $points[] = new Vector4($m[1], $m[2], $m[3], $m[4]);
}

/*$in_const = [];

$point_len = count($points);

for ($i = 0; $i < $point_len - 1; $i++)
{
    $p1 = $points[ $i ];

    for ($j = $i + 1; $j < $point_len; $j++)
    {
        $p2 = $points[ $j ];

        if ($p1->distTo($p2) <= $const_dist)
        {
            if (!in_array($p1, $in_const))
                $in_const[] = $p1;

            if (!in_array($p2, $in_const))
                $in_const[] = $p2;
        }
    }
}

echo count($points),"\n";
echo count($in_const),"\n";
*/

$in_const = $points;

$constellations = [];

$c = 0;

while (!empty($in_const))
{
    $seed = array_pop( $in_const );
    $constellations[++$c] = [ $seed ];

    do
    {
        $const = &$constellations[$c];
        $const_count = count($const);

        foreach ($const as $member)
        {
            foreach ($in_const as $point)
            {
                if ($member->distTo($point) <= $const_dist)
                {
                    if (!in_array($point, $const))
                        $const[] = $point;
                }
            }
        }

        $in_const = array_filter($in_const, function($item) use ($const){
            return !in_array($item, $const);
        });

    } while ($const_count < count($const));
}

#print_r($constellations);
echo count($constellations),"\n";
