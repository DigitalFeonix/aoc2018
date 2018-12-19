<?php

function run_code(array $reg_start, $input)
{
    list($opcode, $A, $B, $C) = explode( ' ', trim($input) );

    $r = $reg_start; // convience

    switch ($opcode)
    {
        case 'addr':
            $r[ $C ] = $r[ $A ] + $r[ $B ];
            break;
        case 'addi':
            $r[ $C ] = $r[ $A ] + $B;
            break;

        case 'mulr':
            $r[ $C ] = $r[ $A ] * $r[ $B ];
            break;
        case 'muli':
            $r[ $C ] = $r[ $A ] * $B;
            break;

        case 'banr':
            $r[ $C ] = $r[ $A ] & $r[ $B ];
            break;
        case 'bani':
            $r[ $C ] = $r[ $A ] & $B;
            break;

        case 'borr':
            $r[ $C ] = $r[ $A ] | $r[ $B ];
            break;
        case 'bori':
            $r[ $C ] = $r[ $A ] | $B;
            break;

        case 'setr':
            $r[ $C ] = $r[ $A ];
            break;
        case 'seti':
            $r[ $C ] = $A;
            break;

        case 'gtir':
            $r[ $C ] = ( $A > $r[ $B ] ) ? 1 : 0;
            break;
        case 'gtri':
            $r[ $C ] = ( $r[ $A ] > $B ) ? 1 : 0;
            break;
        case 'gtrr':
            $r[ $C ] = ( $r[ $A ] > $r[ $B ] ) ? 1 : 0;
            break;

        case 'eqir':
            $r[ $C ] = ( $A == $r[ $B ] ) ? 1 : 0;
            break;
        case 'eqri':
            $r[ $C ] = ( $r[ $A ] == $B ) ? 1 : 0;
            break;
        case 'eqrr':
            $r[ $C ] = ( $r[ $A ] == $r[ $B ] ) ? 1 : 0;
            break;

    }

    return $r;
}

$input = file_get_contents("./input");
#$input = file_get_contents("./test");

$inputs = explode("\n", trim($input));

// get instruction pointer and program
$ip = 0;
$program = [];

foreach ($inputs as $instruction)
{
    if (preg_match('/#ip (\d+)/', trim($instruction), $match))
    {
        $ip = $match[1];
        continue;
    }

    $program[] = trim($instruction);
}

// start with blank registers
$regs = [1,0,0,0,0,0];
$regs = [1,3,10551000,0,10551261,20];
$regs = [1,3,502000,0,10551261,21];

$halted = FALSE;

while (!$halted)
{
    $cmd = $regs[ $ip ];

    if (!key_exists($cmd, $program))
    {
        $halted = TRUE;
        break;
    }

    // repeating lines
    //  3  mulr 5 2 3  ( r5 * r2 => r3) X * Y = Z (looking for this to equal 10051261) the number we are looking for factors for
    //  4  eqrr 3 4 3  ( r3 == r4 => r3) 0
    //  5  addr 3 1 1  ( r3 + r1 => r1) --> this would bump to 7 if above true (instruction 7; r5 + r0 => r0; summing the factors)
    //  6  addi 1 1 1  ( r1 + 1 => r1) jump to 8
    //  8  addi 2 1 2  ( r2 + 1 => r2) increments counter
    //  9  gtrr 2 4 3  ( r2 > r4 => r3) 0
    //  10 addr 1 3 1  ( r1 + r3 => r1) jump to 12 if above true (reset r2 for next uber )
    //  11 seti 2 7 1  ( 2 => r1) set IP to 2 (jump to 3)



    $regs = run_code($regs, $program[ $cmd ]);

    $regs[ $ip ]++;

    echo sprintf('[%s]]', implode(',', $regs)),"\n";
    #sleep(1);
}

echo $regs[0],"\n";


