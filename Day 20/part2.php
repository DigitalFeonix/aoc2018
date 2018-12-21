<?php

ini_set('memory_limit', '4096M');

function visualize_map($map)
{
    foreach ($map as $y => $r)
    {
        echo implode('', $r), "\n";
    }
}

function pathfind($start, $goal, $map)
{
    $que = new SplQueue;

    $que[] = $start;

    while (count($que) > 0)
    {
        #$cur = array_shift($que); // XXX: this is a bottle neck for very long searches
        $cur = $que->dequeue(); // XXX: not much faster
        $pth = $cur['path'];

        // check neighbors and add to que if valid
        $checks = [
            [$cur['x'], $cur['y'] - 1],
            [$cur['x'] - 1, $cur['y']],
            [$cur['x'] + 1, $cur['y']],
            [$cur['x'], $cur['y'] + 1]
        ];

        foreach ($checks as list($x,$y))
        {
            $new = compact('x','y');
            $new['path'] = $pth;
            $new['path']++;

            // compare x,y
            if ($new['x'] == $goal['x'] && $new['y'] == $goal['y'])
            {
                return $new['path'];
            }

            // if valid spot
            if ($map[$y][$x] != '#')
            {
                // add to que
                $que[] = $new;

                // mark as visited on map
                $map[$y][$x] = '#';
            }

            unset($new);
        }
    }

    return FALSE;
}


#$input = file_get_contents("./input");
$input = file_get_contents("./{$argv[1]}");

// trim it up
$input = trim($input);

// Regardless of which option is taken, the route continues from the position it is left at after taking those steps.

// ^ENWWW(NEEE|SSE(EE|N))$
// ^ENNWSWW(NEWS|)SSSEEN(WNSE|)EE(SWEN|)NNN$
// ^ESSWWN(E|NNENN(EESS(WNSE|)SSS|WWWSSSSE(SW|NNNE)))$
// ^WSSEESWWWNW(S|NENNEEEENN(ESSSSW(NWSW|SSEN)|WSWWN(E|WWS(E|SS))))$

// ( save location
// | return to saved location
// ) remove last saved location from list and return

// initialize map with player location at 0,0
$x = 0;
$y = 0;
$map = [];
$map[$y][$x] = 'X';

$stack = [];

// follow "regex" and add to $map
$len = strlen($input);

for ($c = 0; $c < $len; $c++)
{
    $dir = $input[ $c ];

    switch ($dir)
    {
        case 'N': $y--; $map[$y][$x] = '-'; $y--; $map[$y][$x] = '.'; break;
        case 'E': $x++; $map[$y][$x] = '|'; $x++; $map[$y][$x] = '.'; break;
        case 'S': $y++; $map[$y][$x] = '-'; $y++; $map[$y][$x] = '.'; break;
        case 'W': $x--; $map[$y][$x] = '|'; $x--; $map[$y][$x] = '.'; break;

        case '(':
            $stack[] = [$x,$y];
            break;

        case '|':
            #print_r($stack);
            $loc = array_slice($stack, -1);
            list($x,$y) = $loc[0];
            break;

        case ')':
            list($x,$y) = array_pop($stack);
            break;

    }

    #echo sprintf('%s %d,%d', $dir, $x, $y),"\n";
}

unset($input);

#print_r(array_keys($map)); exit();


$minY = min( array_keys($map) );
$maxY = max( array_keys($map) );
$minX = PHP_INT_MAX;
$maxX = PHP_INT_MIN;

foreach ($map as $row)
{
    $minX = min($minX, min( array_keys($row) ));
    $maxX = max($maxX, max( array_keys($row) ));
}

// once map is built, rewrite to clean map of appropriate size (width+2, height+2) and initialized with #
$new = array_fill( 0, ($maxY - $minY) + 3, array_fill(0, ($maxX - $minX) + 3, '#') );

// minX and minY should map to 1,1
$xOff = 1 - $minX;
$yOff = 1 - $minY;

$player = [];

foreach ($new as $y => $row)
{
    foreach ($row as $x => $val)
    {
        $oY = $y - $yOff;
        $oX = $x - $xOff;

        if (key_exists($oY, $map) && key_exists($oX, $map[$oY]))
        {
            $str = $map[ $oY ][ $oX ];
            $new[ $y ][ $x ] = $str;

            // find translated position of X
            $path = 0; // use later
            if ($str == 'X') { $player = compact('x','y','path'); }
        }
    }
}

unset($map);

#visualize_map($new);
#print_r($player);

$least1000 = 0;

foreach ($new as $y => $row)
{
    #echo 'row ',$y,"\n";

    foreach ($row as $x => $val)
    {
        if ($val == '.')
        {
            echo 'pathfinding... ';
            $routeLen = pathfind($player, compact('x','y'), $new);
            if ($routeLen / 2 >= 1000) $least1000++;
            echo $len,"\n";
        }
    }
}

echo $least1000;

// find furthest room (shortest path to room from X is the longest)
// use pathfinding to find route to the room (# are only thing inpassible)
// divide path length by 2 to get doors passed through
