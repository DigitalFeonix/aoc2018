<?php

$input = file_get_contents("./input");
$parts = explode("\n\n\n\n", trim($input));
$lines = explode("\n", trim($parts[0]));

function run_code(array $reg_start, $input, $opcode = null)
{
    list($op, $A, $B, $C) = explode( ' ', trim($input) );

    $r = $reg_start; // convience

    if (!is_null($opcode))
    {
        $code = $opcode;
    }
    else
    {
        $code = $GLOBALS['rosetta'][ $op ];
    }

    switch ($code)
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

$codes = ['addr','addi','mulr','muli','banr','bani','borr','bori','setr','seti','gtir','gtri','gtrr','eqir','eqri','eqrr'];
$pat1  = '/\[(\d+), (\d+), (\d+), (\d+)\]/';

$rosetta = [];

$t = 0;

for ($i = 0, $len = count($lines); $i < $len; $i+=4)
{
    preg_match($pat1, $lines[$i], $matches_a);
    preg_match($pat1, $lines[$i+2], $matches_b);

    $a = array_slice($matches_a, 1);
    $b = array_slice($matches_b, 1);

    $cmd = $lines[ $i+1 ];
    $pos = [];

    foreach ($codes as $code)
    {
        $out = run_code($a, $cmd, $code);

        if ( $b == $out )
        {
            $pos[] = $code;
        }
    }

    if (count($pos) == 1)
    {
        list($op, $A, $B, $C) = explode( ' ', trim($cmd) );
        unset($codes[ array_search($pos[0], $codes) ]);

        $rosetta[$op] = $pos[0];
    }
}

ksort($rosetta);
print_r($rosetta);

#### WORK OUT THE PROGRAM ON THE SECOND HALF

$cmds = explode("\n", trim($parts[1]));

// assuming start with blank registers?
$regs = [0,0,0,0];

foreach ($cmds as $cmd)
{
    $regs = run_code($regs, trim($cmd));
}

echo sprintf('output `%s`', implode(' ', $regs)),"\n";
