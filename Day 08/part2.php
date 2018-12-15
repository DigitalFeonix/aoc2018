<?php

$input = file_get_contents('./inputs/input08-1.txt');
#$input = '2 3 0 3 10 11 12 1 1 0 1 99 2 1 1 2';
$input = trim($input);

$inputs = explode(" ", $input);

// the first two numbers are children node count and meta data count
// child nodes come before the meta data

function get_nodes(&$in)
{
    $ret = 0;

    $cc = array_shift($in);
    $mc = array_shift($in);

    $cv = [];

    for ($c = 0; $c < $cc; $c++)
    {
        $cv[$c+1] = get_nodes($in);
    }

    if ($cc == 0)
    {
        for ($m = 0; $m < $mc; $m++)
        {
            $ret += array_shift($in);
        }
    }
    else
    {
        for ($m = 0; $m < $mc; $m++)
        {
            $i = array_shift($in);
            if (key_exists($i, $cv))
            {
                $ret += $cv[$i];
            }
        }
    }

    return $ret;
}

$sum = get_nodes($inputs);

echo 'Part 2: ',$sum,"\n";
