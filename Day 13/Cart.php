<?php

class Cart
{
    // have unique id
    static $acc = 1;

    public $id;
    public $x;
    public $y;
    private $d; // 0 = ^, 1 = >, 2 = v, 3 = <

    public $is_removed = FALSE;

    private $t = 'l';
    private $rot = [
        'l' => 's',
        's' => 'r',
        'r' => 'l'
    ];

    private $map;

    public function __construct($x, $y, $d, &$map)
    {
        $this->id = static::$acc++;

        $this->x = $x;
        $this->y = $y;
        $this->d = $d;
        $this->map = &$map; // should be a reference
    }

    public function move()
    {
        switch ($this->d)
        {
            case 0: $this->y--; break;
            case 1: $this->x++; break;
            case 2: $this->y++; break;
            case 3: $this->x--; break;
        }

        $track_piece = $this->map[ $this->y ][ $this->x ];

        #echo sprintf('Cart #%d is facing %d at %d,%d on a %s', $this->id, $this->d, $this->x, $this->y, $track_piece), "\n";

        switch($track_piece)
        {
            case '+':
                #echo sprintf('Cart #%d is turning at intersection', $this->id), "\n";

                // [s]traight is ignored
                if ($this->t == 'l') $this->d = ($this->d + 3) % 4;
                if ($this->t == 'r') $this->d = ($this->d + 1) % 4;

                // get next turn direction
                $this->t = $this->rot[ $this->t ];

                break;

            case '\\':
                #echo sprintf('Cart #%d is on a curve1', $this->id), "\n";
                // d0 > d3; d1 > d2; d2 > d1; d3 > d0;
                if ($this->d % 2 == 0) { $this->d = ($this->d + 3) % 4; }
                else { $this->d = ($this->d + 1) % 4; }

                break;

            case '/':
                #echo sprintf('Cart #%d is on a curve2', $this->id), "\n";

                // d0 > d1; d1 > d0; d2 > d3; d3 > d2
                if ($this->d % 2 == 0) { $this->d++; }
                else { $this->d--; }

                break;
        }

        #echo sprintf('Cart #%d is facing %d now', $this->id, $this->d), "\n";
    }

    public function has_crashed($carts)
    {
        // check against other carts
        $ret = FALSE;

        foreach ($carts as $cart)
        {
            // skips itself
            if ($this->id == $cart->id) continue;

            if ($this->x == $cart->x && $this->y == $cart->y)
            {
                // crashed, break out to skip more checks
                $ret = TRUE;

                $this->is_removed = TRUE;
                $cart->is_removed = TRUE;

                break;
            }
        }

        return $ret;
    }
}

