<?php

$input = file_get_contents('./input');
$input = trim($input);
#$input = 'dabAcCaCBAcCcaDA';

$letters = 'abcdefghijklmnopqrstuvwxyz';
$search = [];

// tried to use preg_replace() with backreferences, but couldn't figure out the case swap
for ($i = 0; $i < strlen($letters); $i++)
{
    $ltr = $letters[$i];
    $search[] = $ltr . strtoupper($ltr);
    $search[] = strtoupper($ltr) . $ltr;
}

while (($new = str_replace($search, '', $input)) != $input)
{
    #echo $input,"\n";
    $input = $new;
}

echo strlen($input),"\n";
