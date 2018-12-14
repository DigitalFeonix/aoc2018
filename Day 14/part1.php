<?php

$target = $argv[1];

$recipes = [3,7];
$e1 = 0;
$e2 = 1;

while (count($recipes) < $target + 10)
{
    $s1 = $recipes[$e1];
    $s2 = $recipes[$e2];
    $score = $s1 + $s2;
    
    if (floor($score/10) > 0)
    {
        $recipes[] = floor($score/10);
    }
    
    $recipes[] = $score % 10;
    
    $len = count($recipes);
    
    $m1 = $s1 + 1;
    $m2 = $s2 + 1;
    
    $e1 = ($e1 + $m1) % $len;
    $e2 = ($e2 + $m2) % $len;
    
}

$final_score = substr(implode('', $recipes), $target, 10);

echo $final_score,"\n";
