<?php

include_once('./Cart.php');

$input = file_get_contents('./input');
#$input = file_get_contents('./test');

// cart movements are calculated from top to bottom, left to right
// carts at intersections turn left, straight, right, repeat...

// take the input and replace the carts with track pieces, save as MAP
// take the cart locations and convert those into cart objects
// process the carts, then sort, repeat

function cart_sort($a, $b)
{
    if ($a->y == $b->y)
    {
        if ($a->x == $b->x) return 0;

        return $a->x <=> $b->x;
    }

    return $a->y <=> $b->y;
}

$map = [];
$carts = [];

$lines = explode("\n", $input);

foreach ($lines as $y => $line)
{
    for ($x = 0; $x < strlen($line); $x++)
    {
        $char = $line[ $x ];

        if (($dir = array_search($char, ['^','>','v','<'])) !== FALSE)
        {
            $carts[] = new Cart($x, $y, $dir, $map);

            $char = ($dir % 2 == 0) ? '|' : '-';
        }

        $map[$y][$x] = $char;
    }
}

$crash = false;
$crash_loc = [ 'x' => 0, 'y' => 0 ];

while(!$crash)
{
    foreach ($carts as $i => $cart)
    {
        $cart->move();

        if ($cart->has_crashed($carts))
        {
            // kinda not needed because of the break
            $crash = TRUE;

            $crash_loc['x'] = $cart->x;
            $crash_loc['y'] = $cart->y;
            break 2;
        }
    }

    usort($carts, 'cart_sort');
}

print_r($crash_loc);

