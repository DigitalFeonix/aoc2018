<?php

class Unit
{
    // unique id accumulator
    static $acc = 1;

    public $id;
    public $loc;
    public $type;

    public $hp = 200;
    public $ap = 3;

    public $is_dead = FALSE;

    public function __construct($x, $y, $type)
    {
        $this->id   = static::$acc++;
        $this->loc  = new Vector($x, $y);
        $this->type = $type;
    }

    // NOTE: reading order breaks ALL ties

    public function seek($units)
    {
        // search the map for ALIVE enemies
        $enemies = [];

        foreach ($units as $unit)
        {
            if ($unit->type == $this->type) continue;
            if ($unit->is_dead()) continue;
            $enemies[] = $unit;
        }

        return $enemies;
    }

    private function pathfinding($start, $goal, $map)
    {
        $goal_pos = $goal->get();

        $que = [ $start ];

        while (count($que) > 0)
        {
            $cur = array_shift($que);
            $pos = $cur->get();
            $pth = $cur->path;

            // check neighbors and add to que if valid
            $checks = [
                [$pos['x'], $pos['y'] - 1],
                [$pos['x'] - 1, $pos['y']],
                [$pos['x'] + 1, $pos['y']],
                [$pos['x'], $pos['y'] + 1]
            ];

            foreach ($checks as list($x,$y))
            {
                $new = new Vector($x,$y);
                $new->path   = $pth;
                $new->path[] = $new;

                // if thi
                if ($new->get() == $goal_pos)
                {
                    return $new->path;
                }

                // if valid spot
                if ($map[$y][$x] == '.')
                {
                    // add to que
                    $que[] = $new;

                    // mark as visited on map
                    $map[$y][$x] = 'v';
                }
            }
        }

        return FALSE;
    }

    private function get_near_spots(Vector $vec)
    {
        $pos = $vec->get();

        return [
            [$pos['x'], $pos['y'] - 1],
            [$pos['x'] - 1, $pos['y']],
            [$pos['x'] + 1, $pos['y']],
            [$pos['x'], $pos['y'] + 1]
        ];
    }

    public function move($enemies, $units, $map)
    {
        // we're finished if no where to go
        if (count($enemies) == 0)
        {
            echo sprintf('%s unit #%d is confused by lack of enemies', $this->type, $this->id),"\n";
            return;
        }

        // build current map
        foreach ($units as $unit)
        {
            if ($unit->is_dead()) continue;
            $pos = $unit->loc;
            $map[ $pos->y ][ $pos->x ] = $unit->type;
        }

        #visualize_map($map);

        // check to see if unit has an enemy around it
        $attacks = $this->get_near_spots($this->loc);

        foreach ($attacks as list($x,$y))
        {
            // if the spot has an enemy
            if ($map[$y][$x] != '#' && $map[$y][$x] != '.' && $map[$y][$x] != $this->type)
            {
                echo sprintf('%s unit #%d is in attacking position', $this->type, $this->id),"\n";
                return;
            }
        }

        // now check spots in range of enemies
        $spots = [];

        foreach ($enemies as $enemy)
        {
            $checks = $this->get_near_spots($enemy->loc);

            foreach ($checks as list($x,$y))
            {
                // if the spot is open, we can add it to the list
                if ($map[$y][$x] == '.')
                {
                    $spots[] = new Vector($x,$y);
                }
            }
        }

        // we're finished if no where to go
        if (count($spots) == 0)
        {
            echo sprintf('%s unit #%d found no spots in range of enemies', $this->type, $this->id),"\n";
            return;
        }

        // get shortest path(s) of $reachable

        // NOW DETERMINE IF THE SPOTS ARE REACHABLE
        // can only move in cardinal directions
        // cannot move through units
        // don't move if no open spots around enemies

        $reachable = [];

        foreach ($spots as $spot)
        {
            $path = $this->pathfinding($this->loc, $spot, $map);

            if ($path !== FALSE)
            {
                $reachable[] = compact('spot','path');
            }
        }

        // we're finished if none are reacheable
        if (count($reachable) == 0)
        {
            echo sprintf('%s unit #%d found no spots reachable', $this->type, $this->id),"\n";
            return;
        }

        // get shortest path(s) of $reachable
        usort($reachable, function($a,$b){

            // read order tie breaker
            if (count($a['path']) == count($b['path']))
            {
                if ($a['spot']->y == $b['spot']->y)
                {
                    return $a['spot']->x <=> $b['spot']->x;
                }

                return $a['spot']->y <=> $b['spot']->y;
            }

            return (count($a['path']) <=> count($b['path']));

        });

        // this is the path we want, move to the first location on the path
        $move = $reachable[0]['path'][0]->get();

        echo sprintf('%s unit #%d moving to %d,%d', $this->type, $this->id, $move['x'], $move['y']),"\n";

        // we can finally move (doing this to strip path from the Vector)
        $this->loc = new Vector($move['x'], $move['y']);
    }

    public function fight($enemies)
    {
        // sort through the enemies and attack the first in range
        $my_range = $this->get_near_spots( $this->loc );

        $targets = [];

        foreach ($enemies as $enemy)
        {
            $e_loc = $enemy->loc->get();

            foreach ($my_range as list($x,$y))
            {
                if ($e_loc['x'] == $x && $e_loc['y'] == $y)
                {
                    $targets[] = $enemy;
                    break; // no need to check my other spots
                }
            }
        }

        if (empty($targets)) return;

        // sort by hit points AND read order
        usort($targets, function($a, $b){

            if ($a->hp == $b->hp)
            {
                $apos = $a->loc;
                $bpos = $b->loc;

                if ($apos->y == $bpos->y)
                {
                    return $apos->x <=> $bpos->x;
                }

                return $apos->y <=> $bpos->y;
            }

            return $a->hp <=> $b->hp;
        });

        $target = $targets[0];

        echo sprintf('%s unit #%d attacking %s unit #%d', $this->type, $this->id, $target->type, $target->id),"\n";
        $this->attack($target);

    }

    private function attack($unit)
    {
        // attack
        $unit->take_dmg( $this->ap );
    }

    public function take_dmg($d)
    {
        $this->hp -= $d;

        echo sprintf('%s unit #%d takes %d damage, HP = %d', $this->type, $this->id, $d, $this->hp),"\n";


        if ($this->hp <= 0)
        {
            echo sprintf('%s unit #%d dies', $this->type, $this->id),"\n";
            $this->is_dead = TRUE;
        }
    }

    public function is_dead()
    {
        return $this->is_dead;
    }
}
