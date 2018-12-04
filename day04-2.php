<?php

$input = file_get_contents('./inputs/input04-1.txt');
$inputs = explode("\n", trim($input));

// convert to chronological order
sort($inputs);

$guards = [];
$times = [];
$guard_id = null;
$sleep = null;

foreach ($inputs as $log)
{
    preg_match('/\[(.*)] (.*)/', trim($log), $matches);

    $ts = $matches[1];
    $action = $matches[2];

    // New Guard (new day)
    if (preg_match('/Guard #(\d+) begins shift/', $action, $matches2))
    {
        $guard_id = $matches2[1];

        if (!key_exists($guard_id, $guards))
        {
            $guards[ $guard_id ] = 0;
            $times[ $guard_id ] = array_fill(0,60,0);
        }

        continue;
    }

    if ($action == 'falls asleep')
    {
        $sleep = date_create($ts);
    }

    if ($action == 'wakes up')
    {
        $wake = date_create($ts);
        $diff = date_diff( $sleep, $wake );
        $mins = (int) $diff->format('%i');
        $guards[ $guard_id ] += $mins;

        // track their sleep times
        for ($i = (int) $sleep->format('i'); $i < (int) $wake->format('i'); $i++)
        {
            $times[ $guard_id ][ $i ]++;
        }
    }
}

$max = 0;
$gid = null;
$mid = null;

foreach ($times as $guard => $sleep)
{
    if (($new = max($sleep)) > $max)
    {
        $max = $new;
        $gid = $guard;
        $mid = array_search($max, $sleep);
    }
}

echo ($gid * $mid),"\n";
