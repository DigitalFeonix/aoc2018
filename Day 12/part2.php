<?php

$input = file_get_contents('./inputs/input12.txt');
#$input = file_get_contents('./inputs/test');
$input = trim($input);

$inputs = explode("\n", $input);

$init = '';
$rules = [];

foreach ($inputs as $line)
{
    if (empty($init) && preg_match('/initial state: ([\.#]+)/', $line, $match))
    {
        $init = $match[1];
    }

    if (preg_match('/([\.#]{5}) => ([\.#])/', $line, $match))
    {
        $rules[ $match[1] ] = $match[2];
    }
}

$pots = '...' . $init . '...';

echo ' 0: ',$pots,"\n";

$gens = 5 * pow(10,10);
$pointer = -3;
$dif = 0;

for ($i = 1; $i <= $gens; $i++)
{
    $prev_pointer = $pointer;
    $next_gen = $pots;

    for ($idx = 0, $len = strlen($pots) - 4; $idx < $len; $idx++)
    {
        $key = substr($pots, $idx, 5);
        $next_gen[ $idx + 2 ] = key_exists($key, $rules) ? $rules[ $key ] : '.';
    }

    while (substr($next_gen, 0, 4) == '....')
    {
        $next_gen = substr($next_gen, 1);
        $pointer++;
    }

    while (substr($next_gen, 0, 3) != '...')
    {
        $next_gen = '.' . $next_gen;
        $pointer--;
    }

    while (substr($next_gen, -3) != '...')
    {
        $next_gen .= '.';
    }

    if ($next_gen == $pots)
    {
        echo 'stability reach after generation #', $i,"\n";
        $dif = $pointer - $prev_pointer;
        break;
    }

    $pots = $next_gen;
}

// stability reached.. now to fast forward
$left = $gens - $i;
$pointer += ($left * $dif);

$keys = range($pointer, $pointer + strlen($pots) - 1, 1);
$vals = str_split($pots);
$arr = array_combine($keys, $vals);

$sum = array_reduce($keys, function($carry, $idx) use ($arr) { return ($arr[$idx] == '#') ? $carry + $idx : $carry; }, 0);

echo 'Part 2: ',$sum,"\n";

