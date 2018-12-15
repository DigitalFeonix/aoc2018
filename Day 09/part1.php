<?php

$players = 410;
$last_marble = 72059;

$scores = array_fill(1, $players, 0);
$circle = [0];
$current = 0;

for ($i = 1; $i <= $last_marble; $i++)
{
    $player_turn = (($i % $players) == 0) ? $players : ($i % $players);

    // if multiple of 23, the player gets the marbles
    if ($i % 23 == 0)
    {
        $current -= 7;

        // just in case we go negative
        if ($current < 0) { $current += count($circle); }

        $spot = $current % count($circle);
        $current = $spot;

        $grab = array_splice($circle, $spot, 1);
        $scores[ $player_turn ] += $i;
        $scores[ $player_turn ] += $grab[0];
    }
    else
    {
        $current += 2;
        $spot = $current % count($circle);
        $current = $spot;

        array_splice($circle, $spot, 0, $i);
    }
}

$high_score = max($scores);

echo 'Part 1: ',$high_score,"\n";

