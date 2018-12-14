<?php

$target = (string) $argv[1];
$tlen = strlen($target);

$recipes = '37';
$e1 = 0;
$e2 = 1;

$len = 2;

do
{
    $s1 = $recipes[$e1];
    $s2 = $recipes[$e2];
    
    $score = $s1 + $s2;

    $new = '';

    if (floor($score/10) > 0)
    {
        $new .= floor($score/10);
        $len++;
    }
    
    $new .= $score % 10;
    $recipes .= $new;
    
    $len++;
    
    $m1 = $s1 + 1;
    $m2 = $s2 + 1;
    
    $e1 = ($e1 + $m1) % $len;
    $e2 = ($e2 + $m2) % $len;
    
    $sub = substr($recipes, -($tlen + 1));
    $pos = strpos($sub, $target);
    $found = ($pos !== FALSE);
}
while (!$found);

// we have all the numbers we need, no need for an expensive lookup
$pos += $len - ($tlen + 1);
echo sprintf('%s found after %d recipes', $target, $pos),"\n";
