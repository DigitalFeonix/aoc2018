<?php

$input = file_get_contents('./input');
$input = trim($input);

$inputs = explode("\n", $input);

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

$workers = array_fill(0, 5, '');

$sec = 0;
$out = [];

do
{
    #echo 'PRE => ',print_r($pre,true);

    // get all keys of $pre that are empty
    foreach ($pre as $k1 => $v1)
    {
        if (empty($v1)) $que[] = $k1;
    }

    // remove from pre
    foreach ($que as $s1) { unset($pre[ $s1 ]); }

    // alpha order
    sort($que);

    #echo 'QUE => ',print_r($que,true);

    foreach ($workers as $num => &$worker)
    {
        // if worker is available
        if (empty($worker[$sec]))
        {
            // if there is a job available
            if (!empty($que))
            {
                // this is the step we can assign to worker
                $new_step = array_shift($que);
                $len = ord($new_step) - 4; // backwards/cheaty way to get 60 + offset

                #echo '#',$num,' STARTING ',$new_step,"\n";

                // assign the job
                $worker .= str_repeat($new_step, $len);
            }
            else
            {
                // idle
                $worker .= '.';
            }
        }

        // check if they just finished a job
        if (($sec > 0) && (strlen($worker) == $sec + 1) && ($worker[$sec] != '.'))
        {
            $fin_step = $worker[$sec];

            #echo '#',$num,' COMPLETES ', $fin_step ,"\n";

            // mark as completed
            $out[] = $fin_step;

            // remove the step from the req of any step that has it
            foreach ($pre as &$v2)
            {
                $fin_key = array_search($fin_step, $v2);
                if ($fin_key !== FALSE) { unset($v2[ $fin_key ]); }
            }
        }
    }

    $sec++;
}
while(count($out) < 26);

//print_r($out);
print_r($workers);

echo 'Part 2: ',($sec);
