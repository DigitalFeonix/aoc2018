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

$m = 0;

while(count($carts) > 1)
{
    foreach ($carts as $i => $cart)
    {
        $cart->move();
        $cart->has_crashed($carts);
    }

    $carts = array_filter($carts, function($c){
        return !$c->is_removed;
    });

    usort($carts, 'cart_sort');

    $m++;
}

$win = $carts[0];

echo sprintf('After %d moves the final cart is at %d,%d', $m, $win->x, $win->y), "\n";


