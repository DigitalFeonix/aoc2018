<?php

$input = file_get_contents('./inputs/input07-1.txt');
$input = trim($input);

$inputs = explode("\n", $input);

// usort did not work

/*
$order = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');

do
{
    $prev_order = implode('',$order);

    foreach ($inputs as $instruction)
    {
        preg_match('/Step ([A-Z]) must be finished before step ([A-Z]) can begin/', $instruction, $c);

        $k1 = array_search($c[1], $order);
        $k2 = array_search($c[2], $order);

        if ($k1 > $k2)
        {
            // the first character from it's current position
            array_splice($order, $k1, 1);
            // add it in front of the second character
            array_splice($order, $k2, 0, $c[1]);
        }
    }
}
while (implode('',$order) != $prev_order);

echo 'Part 1: ',implode('',$order),"\n";

// ZFRJDMIKUOBQAYHSGCNTXPEWVL first guess
// ZFRMOYTUBXJNDIKQAHSGCPEVWL second guess (added do/while)
*/

$pre = [];

foreach ($inputs as $instruction)
{
    preg_match('/Step ([A-Z]) must be finished before step ([A-Z]) can begin/', $instruction, $c);

    // add the requirements
    if (!key_exists($c[1], $pre)) $pre[ $c[1] ] = [];
    if (!key_exists($c[2], $pre)) $pre[ $c[2] ] = [];

    // step 1 is a prerequisite of step 2
    $pre[ $c[2] ][] = $c[1];
}

$out = [];
$que = [];

do
{
    // get all keys of $pre that are empty
    foreach ($pre as $k => $v)
    {
        if (empty($v)) $que[] = $k;
    }

    // alpha order
    sort($que);

    // remove from pre
    foreach ($que as $step) { unset($pre[ $step ]); }

    // this is the step we can do
    $step = array_shift($que);

    // remove the step from the req of any step that has it
    foreach ($pre as &$v)
    {
        $key = array_search($step, $v);
        if ($key !== FALSE) { unset($v[ $key ]); }
    }

    $out[] = $step;
}
while (count($out) < 26);

echo 'Part 1: ',implode('',$out),"\n";
