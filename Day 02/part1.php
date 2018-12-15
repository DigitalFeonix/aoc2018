<?php

$input = file_get_contents('./input');
$inputs = explode("\n", trim($input));


$twos = 0;
$threes = 0;

foreach ($inputs as $id)
{
    $id = trim($id);
    $len = strlen($id);

    $ltrs = [];

    for ($i = 0; $i < $len; $i++)
    {
        $char = $id[ $i ];

        if (key_exists($char, $ltrs))
        {
            $ltrs[ $char ] += 1;
            continue;
        }

        $ltrs[ $char ] = 1;
    }

    if (array_search(2, $ltrs)) { $twos++; }
    if (array_search(3, $ltrs)) { $threes++; }
}

echo ($twos * $threes),"\n";
