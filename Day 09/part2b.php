<?php

ini_set('memory_limit', '3072M');

$a = -microtime(true);

class LinkList
{
    public $cur;
    public $nodes = [];

    function __constructor()
    {

    }

    function add($value)
    {
        if (count($this->nodes) == 0)
        {
            $this->nodes[ $value ] = ['p' => $value, 'n' => $value];
            $this->cur = $value;
        }
        else
        {
            $node = $this->nodes[ $this->cur ];

            $next_key = $node['n'];
            $prev_key = $this->cur; // prev_key on the next node

            $this->nodes[ $value ] = [
                'p'  => $prev_key,
                'n'  => $next_key
            ];

            $this->nodes[ $next_key ]['p'] = $value;
            $this->nodes[ $prev_key ]['n'] = $value;

            $this->cur = $value;
        }
    }

    function current()
    {
        return $this->cur;
    }

    function del()
    {
        $node = $this->nodes[ $this->cur ];

        $prev_key = $node['p'];
        $next_key = $node['n'];

        $this->nodes[ $prev_key ]['n'] = $next_key;
        $this->nodes[ $next_key ]['p'] = $prev_key;

        unset($this->nodes[ $this->cur ]);

        $this->cur = $next_key;
    }

    function rotate($num)
    {
        $dir = ($num < 0) ? 'p' : 'n';

        for ($i = 0; $i < abs($num); $i++)
        {
            $node = $this->nodes[ $this->cur ];
            $this->cur = $node[ $dir ];
        }
    }

    function output()
    {
        $o = [];
        $c = count($this->nodes);
        $p = $this->cur;

        for ($i = 0; $i < $c; $i++)
        {
            $o[] = $p;
            $n = $this->nodes[ $p ];
            $p = $n['n'];
        }

        return $o;
    }

}

$players = 410;
$last_marble = 72059 * 100;

$scores = array_fill(0, $players, 0);
$circle = new LinkList();
$circle->add(0);

for ($i = 1; $i <= $last_marble; $i++)
{
    $player_turn = $i % $players;

    // if multiple of 23, the player gets the marbles
    if ($i % 23 == 0)
    {
        $circle->rotate(-7);
        $grab = $circle->current();
        $circle->del();
        $scores[ $player_turn ] += ( $i + $grab );
    }
    else
    {
        $circle->rotate(1);
        $circle->add($i);
    }
}

$high_score = max($scores);

echo 'Part 2: ',$high_score,"\n";

$a += microtime(true);

echo sprintf('run took %0.05f seconds', $a),"\n";
