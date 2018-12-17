<?php

include_once('./Vector.php');

function visualize_map($map)
{
    foreach ($map as $y => $r)
    {
        echo implode('', $r), "\n";
    }
}

$input = file_get_contents("./input");
#$input = file_get_contents("./test");

$inputs = explode("\n", trim($input));

$spring = new Vector(500,0);

$scans = [];

$minX = PHP_INT_MAX;
$maxX = PHP_INT_MIN;
$minY = PHP_INT_MAX;
$maxY = PHP_INT_MIN;

foreach ($inputs as $data_point)
{
    preg_match('/(x|y)=(\d+), [xy]=(\d+)\.\.(\d+)/', $data_point, $matches);

    if ($matches[1] == 'x')
    {
        $x1 = $matches[2];
        $x2 = $matches[2];

        $y1 = $matches[3];
        $y2 = $matches[4];
    }
    else // first axis is y
    {
        $y1 = $matches[2];
        $y2 = $matches[2];

        $x1 = $matches[3];
        $x2 = $matches[4];
    }

    $scans[] = compact('x1','x2','y1','y2');

    $minX = min($x1, $minX);
    $minY = min($y1, $minY);

    $maxX = max($x2, $maxX);
    $maxY = max($y2, $maxY);
}

$row = array_fill($minX - 1, ($maxX-$minX) + 3, '.');
$map = array_fill(0, $maxY + 1, $row);

$map[0][500] = '+';

foreach ($scans as $scan)
{
    for ($y = $scan['y1']; $y <= $scan['y2']; $y++)
    {
        for ($x = $scan['x1']; $x <= $scan['x2']; $x++)
        {
            $map[ $y ][ $x ] = '#';
        }
    }
}

$flow = ['.','|'];
$stop = ['#','~'];


function drip ($x,$y,&$map)
{
    global $flow, $stop, $minX, $maxX, $minY, $maxY;

    $flowing = TRUE;

    do
    {
        $y++;

        $spot = &$map[ $y ][ $x ];

        // if can flow
        if (in_array($spot, $flow))
        {
            $spot = '|';
        }

        // we've gone off the edge
        if ($y > $maxY - 1) break;

        $below = $map[ $y + 1 ][ $x ];

        if (in_array($below, $flow))
        {
            continue;
        }

        ## NEED TO TEST LEFT AND RIGHT TO SEE IF WE ARE IN SEALED CONTAINER
        $container = FALSE;

        // can I move left/right and does that have a floor?
        $sealed_left = FALSE;
        $sealed_right = FALSE;
        $left_edge;
        $right_edge;
        $xl = $x;
        $xr = $x;
        $yb = $y + 1;

        // check to the left (x--)
        while (TRUE)
        {
            $xl--;

            $left = &$map[ $y ][ $xl ];
            $down = $map[ $yb ][ $xl ];

            // we can't flow into the spot to the left
            if (in_array($left, $stop))
            {
                $sealed_left = true;
                $left_edge = $xl + 1;
                break;
            }

            // if the spot is flowable and has something solid below it
            if (in_array($left, $flow) && in_array($down, $stop))
            {
                $left = '|';
                continue;
            }

            // if the spot to the left does not have anything solid underneath it
            if (in_array($down, $flow))
            {
                // overflows the left side
                $left = '|';

                // change the X and continue the flow downward
                drip ($xl,$y,$map);
                break;
            }

            // emergency exit!
            if ($xl < $minX - 1) break;
        }

        // check to the right (x++)
        while (TRUE)
        {
            $xr++;

            $right = &$map[ $y ][ $xr ];
            $down  = $map[ $yb ][ $xr ];

            // we can't flow into the spot to the right
            if (in_array($right, $stop))
            {
                $sealed_right = true;
                $right_edge = $xr - 1;
                break;
            }

            // if the spot is flowable and has something solid below it check next spot
            if (in_array($right, $flow) && in_array($down, $stop))
            {
                $right = '|';
                continue;
            }

            // if the spot to the right does not have anything solid underneath it
            if (in_array($down, $flow))
            {
                // overflows the right side
                $right = '|';

                // change the X and continue the flow downward
                drip ($xr,$y,$map);
                break;
            }

            // emergency exit!
            if ($xr > $maxX + 1) break;
        }

        // we are in a container
        if ($sealed_left && $sealed_right)
        {
            for ($xf = $left_edge; $xf <= $right_edge; $xf++)
            {
                $map[ $y ][ $xf ] = '~';
            }
        }

        $flowing = FALSE;
    }
    while ($flowing);
}

$prev_map = [];

// start emitting water
#for ($w = 0; $w < 2; $w++)
while ($prev_map != $map)
{
    // water spawns at 500,0 at the spring and flows downward
    // it flows downwards until it hits clay (#) or water (~)
    // it will fill left and right while below is clay or water until blocked by clay
    // if the spot below is sand, continue flowing downward

    $x = 500; $y = 0;

    $prev_map = $map;

    drip($x,$y,$map);


    /*
    $flowing = TRUE;

    do
    {
        $y++;

        $spot  = &$map[ $y ][ $x ];

        // if can flow
        if (in_array($spot, $flow))
        {
            $spot = '|';
        }

        // we've gone off the edge
        if ($y > $maxY - 1) break;

        $below = $map[ $y + 1 ][ $x ];

        if (in_array($below, $flow))
        {
            continue;
        }

        #echo sprintf('drop at %d,%d', $x, $y),"\n";
        ## BELOW US IS SOMETHING THAT IMPEDS FLOW (SAND OR WATER)

        //$left  = $map[ $y ][ $x - 1 ];
        //$right = $map[ $y ][ $x + 1 ];


        ## NEED TO TEST LEFT AND RIGHT TO SEE IF WE ARE IN SEALED CONTAINER
        $container = FALSE;

        // can I move left/right and does that have a floor?
        $sealed_left = FALSE;
        $sealed_right = FALSE;
        $left_edge;
        $right_edge;
        $xl = $x;
        $xr = $x;
        $yb = $y + 1;

        // check to the left (x--)
        while (TRUE)
        {
            $xl--;

            $left = &$map[ $y ][ $xl ];
            $down = $map[ $yb ][ $xl ];

            // we can't flow into the spot to the left
            if (in_array($left, $stop))
            {
                $sealed_left = true;
                $left_edge = $xl + 1;
                break;
            }

            // if the spot is flowable and has something solid below it
            if (in_array($left, $flow) && in_array($down, $stop))
            {
                $left = '|';
                continue;
            }

            // if the spot to the left does not have anything solid underneath it
            if (in_array($down, $flow))
            {
                // overflows the left side
                $left = '|';

                // change the X and continue the flow downward
                $x = $xl;
                continue 2;
            }

            // emergency exit!
            if ($xl < $minX - 1) break;
        }

        // check to the right (x++)
        while (TRUE)
        {
            $xr++;

            $right = &$map[ $y ][ $xr ];
            $down  = $map[ $yb ][ $xr ];

            // we can't flow into the spot to the right
            if (in_array($right, $stop))
            {
                $sealed_right = true;
                $right_edge = $xr - 1;
                break;
            }

            // if the spot is flowable and has something solid below it check next spot
            if (in_array($right, $flow) && in_array($down, $stop))
            {
                $right = '|';
                continue;
            }

            // if the spot to the right does not have anything solid underneath it
            if (in_array($down, $flow))
            {
                // overflows the right side
                $right = '|';

                // change the X and continue the flow downward
                $x = $xr;
                continue 2;
            }

            // emergency exit!
            if ($xr > $maxX + 1) break;
        }

        // we are in a container
        if ($sealed_left && $sealed_right)
        {
            for ($xf = $left_edge; $xf <= $right_edge; $xf++)
            {
                $map[ $y ][ $xf ] = '~';
            }
        }

        $flowing = FALSE;
    }
    while ($flowing);
    */


}

visualize_map($map);

$wet = 0; // the sand that got wet
$water = 0; // water retained in the clay

foreach ($map as $y => $row)
{
    if ($y < $minY) continue;
    if ($y > $maxY) continue;

    $vals = array_count_values($row);

    if (key_exists('|', $vals)) $wet += $vals['|'];
    if (key_exists('~', $vals)) $water += $vals['~'];
}

echo 'Part 1: ',($wet + $water),"\n";
echo 'Part 2: ',$water,"\n";



