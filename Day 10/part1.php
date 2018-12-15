<?php

$input = file_get_contents('./inputs/input10.txt');
$input = trim($input);

$inputs = explode("\n", $input);

class Vector
{
    protected $x;
    protected $y;

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

class Light
{
    private $pos;
    private $vel;

    function __construct(Vector $pos, Vector $vel)
    {
        $this->pos = $pos ?? new Vector(0,0);
        $this->vel = $vel ?? new Vector(0,0);
    }

    public function move()
    {
        $this->pos->add( $this->vel );
    }

    public function getPos()
    {
        return $this->pos->get();
    }
}

function write_message($view)
{
    foreach ($view as $row)
    {
        echo implode('',$row),"\n";
    }
}

function check_message($view)
{
    $ret = false;

    $lines = 0;

    if (count($view) > 300) return false;

    // we want only a group of 10 rows with data
    foreach ($view as $row)
    {
        $v = array_count_values($row);

        $has_data = key_exists('#', $v);

        if ($has_data)
        {
            $lines++;
        }

        if ($lines > 10) return false;
    }

    // TODO: refactor to check for smallest boundary
    if ($lines == 10) $ret = true;

    return $ret;
}

$blank_view = array_fill(0, 300, array_fill(0, 300, '.'));

$lights = [];

foreach ($inputs as $light)
{
    preg_match('#position=<(.*), (.*)> velocity=<(.*), (.*)>#', $light, $match);

    $pos = new Vector( trim($match[1]), trim($match[2]) );
    $vel = new Vector( trim($match[3]), trim($match[4]) );

    $lights[] = new Light($pos, $vel);
}

$i = 0;

do
{
    $i++;

    $view = $blank_view;

    foreach ($lights as $lite)
    {
        $lite->move();
        $pos = $lite->getPos();
        $view[ $pos['y'] ][ $pos['x'] ] = '#';
    }

    if ($i % 100 == 0)
    {
        $y = array_keys($view);
    }
}
while ( !check_message($view) );

echo sprintf('After %d seconds:', $i),"\n";

write_message($view);

echo "\n";



