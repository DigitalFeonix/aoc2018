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

$pots = [-3 => '.', -2 => '.', -1 => '.'] + str_split($init);

for ($i = 0, $c = count($pots); $i < $c; $i++)
{
    $pots[] = '.';
}

echo ' 0: ',implode('', $pots),"\n";

for ($i = 1; $i <= 20; $i++)
{
    $next_gen = $pots;

    foreach ($pots as $idx => $pot)
    {
        $key = @($pots[$idx - 2] . $pots[$idx - 1] . $pots[$idx] . $pots[$idx + 1] . $pots[$idx + 2]);

        if (strlen($key) < 5)
        {
            #echo $idx,' too short',"\n";
            continue;
        }

        #echo $key,"\n";
        $next_gen[ $idx ] = key_exists($key, $rules) ? $rules[ $key ] : '.';
    }

    $pots = $next_gen;

    echo sprintf('% 2d: %s', $i ,implode('', $pots)),"\n";
}

$keys = array_keys($pots);
$sum = array_reduce($keys, function($carry, $idx) use ($pots) { return ($pots[$idx] == '#') ? $carry + $idx : $carry; }, 0);

echo 'Part 1: ',$sum,"\n";

