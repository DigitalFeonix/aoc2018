<?php

$input = file_get_contents('./inputs/input05-1.txt');
$input = trim($input);
#$input = 'dabAcCaCBAcCcaDA';

$letters = 'abcdefghijklmnopqrstuvwxyz';
$search = [];

// tried to use preg_replace() with backreferences, but couldn't figure out the case swap
for ($i = 0; $i < 26; $i++)
{
    $ltr = $letters[$i];
    $search[] = $ltr . strtoupper($ltr);
    $search[] = strtoupper($ltr) . $ltr;
}

$reactions = [];

for ($i = 0; $i < 26; $i++)
{
    $ltr = $letters[ $i ];

    $alt_input = str_ireplace($ltr, '', $input, $count);

    while (($new = str_replace($search, '', $alt_input)) != $alt_input)
    {
        #echo $input,"\n";
        $alt_input = $new;
    }

    $reactions[ $ltr ] = strlen($alt_input);
}

echo min( $reactions ),"\n";
