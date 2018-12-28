<?php

class Vector4
{
    public $x;
    public $y;
    public $z;
    public $w;

    function __construct(int $x = 0, int $y = 0, int $z = 0, int $w = 0)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->w = $w;
    }

    public function distTo(Vector4 $other)
    {
        return abs($this->x - $other->x) + abs($this->y - $other->y) + abs($this->z - $other->z) + abs($this->w - $other->w);
    }

    public function __toString()
    {
        return sprintf('%d,%d,%d,%d', $this->x, $this->y, $this->z, $this->w);
    }
}
