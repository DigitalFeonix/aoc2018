<?php

class Node
{
    public $x;
    public $y;
    public $t;

    function __construct(int $x = 0, int $y = 0, int $t = 0)
    {
        $this->x = $x;
        $this->y = $y;
        $this->t = $t;
    }

    static function getTaxiDist(Node $a, Node $b)
    {
        return abs($a->x - $b->x) + abs($a->y - $b->y);
    }

    public function __toString()
    {
        return sprintf('%d,%d,%d', $this->x, $this->y, $this->t);
    }
}
