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

$halted = FALSE;

while (!$halted)
{
    $cmd = $regs[ $ip ];

    if (!key_exists($cmd, $program))
    {
        $halted = TRUE;
        break;
    }

    $regs = run_code($regs, $program[ $cmd ]);

    $regs[ $ip ]++;
}

echo $regs[0],"\n";


