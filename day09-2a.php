<?php

$players = 410;
$last_marble = 72059 * 100;

$scores = array_fill(1, $players, 0);
$circle = new SplDoublyLinkedList();
$current = 0;

$circle->push(0);

for ($i = 1; $i <= $last_marble; $i++)
{
    $player_turn = (($i % $players) == 0) ? $players : ($i % $players);

    // if multiple of 23, the player gets the marbles
    if ($i % 23 == 0)
    {
        $current -= 7;

        // just in case we go negative
        if ($current < 0) { $current += $circle->count(); }

        $spot = $current % $circle->count();
        $current = $spot;

        $grab = $circle->offsetGet($spot);
        $circle->offsetUnset($spot);
        $scores[ $player_turn ] += $i;
        $scores[ $player_turn ] += $grab;
    }
    else
    {
        $current += 2;
        $spot = $current % $circle->count();
        $current = $spot;

        $circle->add($spot, $i);
    }
}

$high_score = max($scores);

echo 'Part 2: ',$high_score,"\n";

