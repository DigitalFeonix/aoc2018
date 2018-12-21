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
            $r[ $C ] = $r[ $A ] & (int) $B;
            break;

        case 'borr':
            $r[ $C ] = $r[ $A ] | $r[ $B ];
            break;
        case 'bori':
            $r[ $C ] = $r[ $A ] | (int) $B;
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

$input  = file_get_contents("./input");
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
$regs = [0,0,0,0,0,0];

/*

    #ip 3

    // check the BANI opcode
    00  seti 123 0 1        123 => r1
    01  bani 1 456 1        r1 & 456 => r1 (72)
    02  eqri 1 72 1         r1 == 72 => r1 (1)
    03  addr 1 3 3          r1 + r3 => r3 (4) (JUMP to 5)
    04  seti 0 0 3          0 => r3 (JUMP to 1)

    // initial state
    05  seti 0 7 1          0 => r1

    06  bori 1 65536 4      r1 | 65536 => r4 (65536)
    07  seti 3798839 3 1    3798839 => r1
    08  bani 4 255 5        r4 & 255 => r5 (0)               (single byte)
    09  addr 1 5 1          r1 + r5 => r1 (3798839)
    10  bani 1 16777215 1   r1 & 16777215 => r1 (3798839)    (keep in 3 bytes)
    11  muli 1 65899 1      r1 * 65889 => r1 (250339691261)
    12  bani 1 16777215 1   r1 & 16777215 => r1 (6851325)    (keep in 3 bytes)
    13  gtir 256 4 5        256 > r4 => r5 (0)
    14  addr 5 3 3          r5 + r3 => r3 ( JUMP to 16 if 256 > r4)

    15  addi 3 1 3          r3 + 1 => r3 ( JUMP to 17 )
    16  seti 27 6 3         27 => r3 ( JUMP to 28 )

    // set accumulator to 0
    17  seti 0 2 5          0 => r5

    // accumulator++ * 256
    18  addi 5 1 2          r5 + 1 => r2 (1)
    19  muli 2 256 2        r2 * 256 => r2 (256)
    20  gtrr 2 4 2          r2 > r4 => r2 (0|1)
    21  addr 2 3 3          r2 + r3 => r3 ( JUMP to 23 if r2 > r4 )
    22  addi 3 1 3          r3 + 1 => r3
    23  seti 25 3 3         25 => r3 ( JUMP to 26 )
    24  addi 5 1 5          r5 + 1 => r5
    25  seti 17 1 3         17 => r3 ( JUMP back to 18 )

    26  setr 5 6 4          r5 => r4
    27  seti 7 8 3          7 => r3 ( JUMP back to 8 )
    28  eqrr 1 0 5          r1 == r0 => r5 (0|1)
    29  addr 5 3 3          r5 + r3 => r3 ( HALT if 28 true )
    30  seti 5 6 3          5 => r3 ( JUMP to 6 )

*/

$halted = FALSE;

$interrupts = [];

while (!$halted)
{
    $cmd = $regs[ $ip ];

    if ($cmd == 28)
    {
        $num = $regs[1];

        if (key_exists($num, $interrupts))
        {
            echo array_pop($interrupts),"\n";
            break;
        }

        $interrupts[$num] = $num;
    }

    if (!key_exists($cmd, $program))
    {
        $halted = TRUE;
        break;
    }

    $regs = run_code($regs, $program[ $cmd ]);

    $regs[ $ip ]++;
}

