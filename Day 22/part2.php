<?php

ini_set('memory_limit', '1024M');

function visualize_map($map)
{
    foreach ($map as $y => $r)
    {
        $r = array_map(function($v){
            return $v['m'];
        }, $r);

        echo implode('', $r), "\n";
    }
}

include_once('./Node.php');

function astar_pathfind($start, $goal, $map)
{
    $types = ['rocky','wet','narrow'];
    $tools = ['neither','torch','climbing gear'];

    //$start->type = $map[ $start->y ][ $start->x ]['t'];
    //$goal->type  = 0;

    $h = count($map);
    $w = count($map[0]);

    $camefrom = [];

    $open   = [ "{$start}" => $start ];
    $closed = [];
    $gscore = [ "{$start}" => 0 ];
    $fscore = [ "{$start}" => Node::getTaxiDist($start, $goal) ];

    while (!empty($open))
    {
        $lowest = PHP_INT_MAX;

        // get open node with lowest fscore
        foreach ($open as $key => $val)
        {
            $score = $fscore[ $key ];

            if ($score < $lowest)
            {
                $lowest = $score;
                $curr = $val;
            }
        }

        #$node = array_search( min($fscore), $fscore );
        #$curr = $open[ $node ];

        // EXIT CHECK
        if ("{$curr}" == "{$goal}")
        {
            echo 'Winner => ',$fscore["{$goal}"],"\n";

            // at goal, if not right tool add the swap time
            $minutes = 0; //($curr->t != $goal->t) ? 7 : 0;
            $route   = [];//["{$curr}"];

            while (key_exists("{$curr}", $camefrom))
            {
                #echo $curr,' - ',$minutes,"\n";

                $minutes += $camefrom[ "{$curr}" ]['cost'];
                $curr = $camefrom[ "{$curr}" ]['node'];

                array_unshift($route, $curr);
            }

            return [$route,$minutes];
        }

        // move from open set to closed set
        unset($open["{$curr}"]);
        $closed["{$curr}"] = $curr;

        // get current rooom type
        $curr_room = $map[ $curr->y ][ $curr->x ]['t'];

        // check neighbors and add to que if valid
        $neighbors = [
            [$curr->x + 1, $curr->y],
            [$curr->x, $curr->y + 1],
            [$curr->x - 1, $curr->y],
            [$curr->x, $curr->y - 1],
        ];

        foreach ($neighbors as list($x, $y))
        {
            // check bounds
            if ($x < 0) continue;
            if ($y < 0) continue;
            if ($x == $w) continue;
            if ($y == $h) continue;

            // we are either going to move (x,y) or update tool (t)

            // get next room type
            $next_room = $map[ $y ][ $x ]['t'];

            // calculate cost to next state (node)
            $cost = 0;

            if ($next_room != $curr_room && $curr->t == $next_room)
            {
                // terrain types
                // 0 = rocky (can't use neither)
                // 1 = wet (can't use torch)
                // 2 = narrow (can't use climbing gear)

                // tool types
                // 0 = neither (swap to climbing for wet [1], swap to torch for narrow [2])
                // 1 = torch (swap to climbing for rocky [0], swap to neither for narrow [2])
                // 2 = climbing gear (swap to torch for rocky [0], swap to neither for wet [1])

                // if current tool will NOT work
                #if ($curr->t == $next_room)
                #{
                    $cost += 7;

                    // now update tool
                    $next = new Node($curr->x, $curr->y, ($curr_room | $next_room) ^ 3);

                    #$next->t = ($curr_room | $next_room) ^ 3;
                #}

                /*echo sprintf(
                    'moving from %s to %s, swapping %s for %s, cost of %d',
                    $types[ $curr->type ],
                    $types[ $next->type ],
                    $tools[ $curr->tool ],
                    $tools[ $next->tool ],
                    $cost
                ),"\n";*/
            }
            else
            {
                $next = new Node($x, $y, $curr->t);
                $cost += 1;
            }

            // tenative score
            $t_gscore = $gscore[ "{$curr}" ] + $cost;

            // already evaluated, but check if score lower from this path
            if (key_exists("{$next}", $closed) && !($t_gscore < $gscore["{$next}"]))
            {
                continue;
            }

            if (!key_exists("{$next}", $open))
            {
                $open[ "{$next}" ] = $next;
            }
            else if ($t_gscore >= $gscore[ "{$next}" ])
            {
                // better path found elsewhere
                continue;
            }

            $node = "{$curr}";

            $camefrom[ "{$next}" ] = compact('node','cost');

            $gscore[ "{$next}" ] = $t_gscore;
            $fscore[ "{$next}" ] = $t_gscore + Node::getTaxiDist($next, $goal);
        }
    }
}

// $input  = file_get_contents("./input");
// $inputs = explode("\n", trim($input));

// depth: 11991
// target: 6,797


$depth = 11991;
$tx = 6;
$ty = 797;
$w = 500;
$h = 1000;

if (!empty($argv[1]))
{
    $depth = 510;
    $tx = 10;
    $ty = 10;
    $w = 15;
    $h = 15;
}

$marks = ['.','=','|'];
$area = [];
$mins = 0;

for ($y = 0; $y <= $h; $y++)
{
    $area[ $y ] = [];

    for ($x = 0; $x <= $w; $x++)
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
            $gi = $area[ $y - 1 ][ $x ]['e'] * $area[ $y ][ $x - 1 ]['e'];
        }

        // calculate the Erosion Level
        $el = ($gi + $depth) % 20183;
        $type = $el % 3;

        // for map making
        $marker = $marks[ $type ];

        if ($x == 0 && $y == 0) $marker = 'M';
        if ($x == $tx && $y == $ty) $marker = 'T';

        // save info into area map
        $area[ $y ][ $x ] = ['e' => $el, 't' => $type, 'm' => $marker];
    }
}

#visualize_map($area);

$mouth  = new Node(0, 0, 1);
$victim = new Node($tx, $ty, 1);

$ret = astar_pathfind($mouth, $victim, $area);

#print_r($ret[0]);
echo $ret[1],"\n";


