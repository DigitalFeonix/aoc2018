<?php

ini_set('memory_limit', '4096M');

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
            $this->nodes[$value] = ['prev' => $value, 'next' => $value, 'value' => $value];
            $this->cur = $value;
        }
        else
        {
            $node = $this->nodes[ $this->cur ];

            $next_key = $node['next'];
            $prev_key = $this->cur; // prev_key on the next node

            $this->nodes[ $value ] = [
                'prev'  => $this->cur,
                'next'  => $next_key,
                'value' => $value
            ];

            $ins_key = $value;

            $this->nodes[ $next_key ]['prev'] = $ins_key;
            $this->nodes[ $prev_key ]['next'] = $ins_key;

            $this->cur = $ins_key;
        }
    }

    function current()
    {
        $current_node = $this->nodes[ $this->cur ];

        return $current_node['value'];
    }

    function del()
    {
        $node = $this->nodes[ $this->cur ];

        $prev_key = $node['prev'];
        $next_key = $node['next'];

        $this->nodes[ $prev_key ]['next'] = $next_key;
        $this->nodes[ $next_key ]['prev'] = $prev_key;

        unset($this->nodes[ $this->cur ]);

        $this->cur = $next_key;
    }

    function rotate($num)
    {
        for ($i = 0; $i < abs($num); $i++)
        {
            $node = $this->nodes[ $this->cur ];

            $this->cur = ($num > 0) ? $node['next'] : $node['prev'];
        }
    }

    function output()
    {
        $o = [];
        $c = count($this->nodes);
        $p = $this->cur;

        for ($i = 0; $i < $c; $i++)
        {
            $n = $this->nodes[ $p ];
            $p = $n['next'];
            $o[] = $n['value'];
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
