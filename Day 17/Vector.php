<?php

class Vector
{
    public $x;
    public $y;

    public $path = [];

    function __construct(int $x, int $y)
    {
        $this->x = $x ?? 0;
        $this->y = $y ?? 0;
    }

    public function add(Vector $other)
    {
        $this->x = $this->x + $other->x;
        $this->y = $this->y + $other->y;
    }

    public function get()
    {
        return ['x' => $this->x, 'y' => $this->y];
    }
}
