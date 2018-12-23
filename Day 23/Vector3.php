<?php

class Vector3
{
    public $x;
    public $y;
    public $z;

    function __construct(int $x = 0, int $y = 0, int $z = 0)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function distTo(Vector3 $other)
    {
        return abs($this->x - $other->x) + abs($this->y - $other->y) + abs($this->z - $other->z);
    }

    public function __toString()
    {
        return sprintf('%d,%d,%d', $this->x, $this->y, $this->z);
    }
}
