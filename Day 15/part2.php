<?php

include_once('./Vector.php');
include_once('./Unit.php');

function build_map($units, $map)
{
    // build current map
    foreach ($units as $unit)
    {
        if ($unit->is_dead()) continue;
        $pos = $unit->loc;
        $map[ $pos->y ][ $pos->x ] = $unit->type;
    }

    return $map;
}

function visualize_map($map, $units = [])
{
    $mobs = [];

    foreach ($units as $u)
    {
        if ($u->is_dead()) continue;
        $mobs[ $u->loc->y ][] = sprintf('%s(%d)', $u->type, $u->hp);
    }

    foreach ($map as $y => $r)
    {
        echo implode('', $r), ' ', (!empty($mobs[$y])?implode(', ', $mobs[$y]):''), "\n";
    }
}

function sort_units($a, $b)
{
    $apos = $a->loc;
    $bpos = $b->loc;

    if ($apos->y == $bpos->y)
    {
        return $apos->x <=> $bpos->x;
    }

    return $apos->y <=> $bpos->y;
}

$file = $argv[1];

$input = file_get_contents("./{$file}");
$lines = explode("\n", trim($input));

$acceptable = FALSE;
$ap = 3;

do
{
    echo '################ FIGHT! FIGHT! FIGHT! ################',"\n\n";

    $units = [];
    $map   = [];
    $elves = 0;

    $ap++;

    foreach ($lines as $y => $line)
    {
        $line = trim($line);

        $row = [];

        for ($x = 0, $len = strlen($line); $x < $len; $x++)
        {
            $c = $line[$x];

            if ($c == 'E')
            {
                $units[] = new Unit($x, $y, $c, $ap);
                $c = '.';
                $elves++;
            }
            else if ($c == 'G')
            {
                $units[] = new Unit($x, $y, $c);
                $c = '.';
            }

            $row[] = $c;
        }

        $map[] = $row;
    }

    //visualize_map( build_map($units, $map) );
    //echo "\n\n";

    $enemies_left = TRUE;
    $rounds = 0;

    $steps = $argv[2] ?? 2;

    while ($enemies_left)
    {
        foreach ($units as $unit)
        {
            if ($unit->is_dead()) continue;

            // acquire enemies
            $e = $unit->seek($units);

            if (empty($e))
            {
                $enemies_left = FALSE;
                break 2;
            }

            ## Move => single grid square in cardinal direction
            $unit->move($e, $units, $map);

            ## Combat
            $unit->fight($e);
        }

        $rounds++;

        echo sprintf('Finished Round #%d', $rounds),"\n";
        #visualize_map( build_map($units, $map), $units );

        // despawn the dead
        $units = array_filter($units, function($u){
            return !$u->is_dead();
        });

        // sort the order for next round
        usort($units, 'sort_units');
    }

    // despawn the dead
    $units = array_filter($units, function($u){
        return !$u->is_dead();
    });

    if ($units[0]->type == 'E' && count($units) == $elves)
    {
        $acceptable = TRUE;
    }

} while (!$acceptable);

################################################################################
#visualize_map( build_map($units, $map), $units );
################################################################################

$total_hp = array_reduce($units, function($carry, $u) { return (!$u->is_dead()) ? ($carry + $u->hp) : $carry; }, 0);

echo sprintf('With %d AP, after %d rounds, the still standing elves have %d HP for a score of %d', $ap, $rounds, $total_hp, ($rounds * $total_hp)),"\n";
