<?php

include_once('./Vector3.php');

$input  = file_get_contents("./input");

if (!empty($argv[1]))
{
    $input  = file_get_contents("./test2");
}

$inputs = explode("\n", trim($input));

$bots = [];

$minX = PHP_INT_MAX;
$minY = PHP_INT_MAX;
$minZ = PHP_INT_MAX;

$maxX = PHP_INT_MIN;
$maxY = PHP_INT_MIN;
$maxZ = PHP_INT_MIN;

foreach ($inputs as $dat)
{
    preg_match('/pos=<(\-?\d+),(\-?\d+),(\-?\d+)>, r=(\d+)/', trim($dat), $m);

    $minX = min($minX, $m[1]);
    $minY = min($minY, $m[2]);
    $minZ = min($minZ, $m[3]);

    $maxX = max($maxX, $m[1]);
    $maxY = max($maxY, $m[2]);
    $maxZ = max($maxZ, $m[3]);

    $nano = new Vector3($m[1], $m[2], $m[3]);
    $nano->radius = $m[4];

    $bots[] = $nano;
}

$origin = new Vector3(0,0,0);
$best = new Vector3(0,0,0);
$most = PHP_INT_MIN;
$delta = pow(2,22); // 4,194,304
$shortest = PHP_INT_MAX;

// first pass will take the longest, after that it should run much faster as it becomes
// a much smaller search area and searches per loop
while ($delta >= 1)
{
    echo 'Loop with delta = ',$delta,"\n";

    for ($x = $minX; $x < $maxX; $x += $delta)
    {
        for ($y = $minY; $y < $maxY; $y += $delta)
        {
            for ($z = $minZ; $z < $maxZ; $z += $delta)
            {
                $spot  = new Vector3($x,$y,$z);
                $count = 0;

                foreach ($bots as $bot)
                {
                    if ($spot->distTo($bot) <= $bot->radius) $count++;
                }

                if ($count > 0)
                {
                    if (($count > $most) || ($count == $most && $spot->distTo($origin) < $shortest))
                    {
                        $best = $spot;
                        $most = $count;
                        $shortest = $spot->distTo($origin);

                        echo sprintf('new best! %s with %d', $spot, $count),"\n";
                    }
                }
            }
        }
    }

    $minX = $best->x - $delta;
    $minY = $best->y - $delta;
    $minZ = $best->z - $delta;

    $maxX = $best->x + $delta;
    $maxY = $best->y + $delta;
    $maxZ = $best->z + $delta;

    $delta /= 2;
}

echo $best,"\n";
echo $best->distTo( $origin ),"\n";
echo $shortest,"\n";
