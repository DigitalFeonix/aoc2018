<?php

$input = file_get_contents('./inputs/input02-1.txt');
$inputs = explode("\n", trim($input));

$count = count($inputs);
$len = strlen($inputs[0]);

foreach ($inputs as $key => $id)
{
    $id1 = trim($id);

    for ($i = $key + 1; $i < $count; $i++)
    {
        $id2 = trim($inputs[ $i ]);

        $dif = 0;

        for ($j = 0; $j < $len; $j++)
        {
            if ($id1[ $j ] != $id2[ $j ])
            {
                $dif++;
            }
        }

        if ($dif == 1)
        {
            break 2;
        }
    }
}

$out = '';

// get the same chars from the two ids
for ($i = 0; $i < $len; $i++)
{
    if ($id1[ $i ] == $id2[ $i ])
    {
        $out .= $id1[ $i ];
    }
}

echo $out,"\n";
