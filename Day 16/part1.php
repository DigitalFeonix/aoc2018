<?php

$input = file_get_contents("./input");
#$input = file_get_contents("./test");

$parts = explode("\n\n\n\n", trim($input));
$lines = explode("\n", trim($parts[0]));

function run_code(array $reg_start, $input, $opcode)
{
    list($op, $A, $B, $C) = explode( ' ', trim($input) );



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

$codes = ['addr','addi','mulr','muli','banr','bani','borr','bori','setr','seti','gtir','gtri','gtrr','eqir','eqri','eqrr'];
$pat1  = '/\[(\d+), (\d+), (\d+), (\d+)\]/';

$count = 0; $t = 0;

for ($i = 0, $len = count($lines); $i < $len; $i+=4)
{
    $t++;

    preg_match($pat1, $lines[$i], $matches_a);
    preg_match($pat1, $lines[$i+2], $matches_b);

    $a = array_slice($matches_a, 1);
    $b = array_slice($matches_b, 1);

    $cmd = $lines[ $i+1 ];

    $pos = 0;

    #echo sprintf('Seeking output `%s`', implode(' ', $b)),"\n";

    foreach ($codes as $code)
    {
        $out = run_code($a, $cmd, $code);

        #echo sprintf('%s produced output `%s`', $code, implode(' ', $out)),"\n";

        if ( $b == $out )
        {
            #echo sprintf('Matches %s opcode', $code),"\n";
            $pos++;
        }
    }

    if ($pos > 2)
    {
        $count++;
    }

    echo sprintf('Test #%d matched %d opcodes', $t, $pos),"\n";
}

echo $count,"\n";
