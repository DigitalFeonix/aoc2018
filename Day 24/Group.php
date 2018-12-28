<?php

class Group
{
    // unique id accumulator
    static $acc = 1;

    public $id;
    public $type; // Immune System, Infection

    public $units; // how many units per group
    public $hp; // health points per unit
    public $ap; // attack power

    public $immunity = [];
    public $weakness = [];

    public $initiative;
    public $attack;

    public $is_dead = FALSE;

    private $target;
    private $is_targeted = FALSE;

    public function __construct($type, $units, $hp, $ap, $attack, $initiative, $immunity = [], $weakness = [])
    {
        $this->id = static::$acc++;

        $this->type = $type;
        $this->units = $units;
        $this->hp = $hp;
        $this->ap = $ap;
        $this->attack = $attack;
        $this->initiative = $initiative;

        $this->immunity = $immunity;
        $this->weakness = $weakness;
    }

    public function end_turn()
    {
        // reset target
        $this->target = null;
        $this->is_targeted = FALSE;
    }

    public function target($groups)
    {
        $enemies = [];

        foreach ($groups as $group)
        {
            if ($this->type == $group->type) continue;
            if ($group->is_dead()) continue;
            if ($group->is_targeted()) continue;

            $dmg = $this->calc_dmg($group);

            if ($dmg > 0)
            {
                $enemies[] = compact('group','dmg');
            }
        }

        if (empty($enemies))
        {
            return;
        }

        // sort by prefered attack order
        usort($enemies, function($a,$b){
            if ($a['dmg'] == $b['dmg']) {
                $a_ep = $a['group']->get_ep();
                $b_ep = $b['group']->get_ep();
                if ($a_ep == $b_ep) {
                    return $b['group']->initiative <=> $a['group']->initiative;
                }
                return $b_ep <=> $a_ep;
            }
            return $b['dmg'] <=> $a['dmg'];
        });

        $tar_groups = array_column($enemies, 'group');
        $target = array_shift( $tar_groups );
        #echo sprintf('%s group #%d picked target, %d DMG against %s group #%d', $this->type, $this->id, $dmg, $target->type, $target->id),"\n";
        $target->selected();
        $this->target = $target;
    }

    public function fight()
    {
        // no target
        if ($this->target == null) return;

        #echo sprintf('%s group #%d attacking %s group #%d', $this->type, $this->id, $this->target->type, $this->target->id),"\n";

        $this->attack($this->target);
    }

    private function attack($target)
    {
        $dmg = $this->calc_dmg($target);
        $target->take_dmg( $dmg, $this->attack );
    }

    public function take_dmg($d, $type)
    {
        $killed = min($this->units, floor( $d / $this->hp)); // how many units it kills

        $this->units -= $killed;

        #echo sprintf('%s group #%d takes %d damage, units killed = %d, left %d', $this->type, $this->id, $d, $killed, $this->units),"\n";

        if ($this->units <= 0)
        {
            #echo sprintf('%s unit #%d dies', $this->type, $this->id),"\n";
            $this->is_dead = TRUE;
        }
    }

    private function calc_dmg($target)
    {
        $dmg = 0;

        if (!in_array($this->attack, $target->immunity))
        {
            $dmg = $this->units * $this->ap;

            if (in_array($this->attack, $target->weakness))
            {
                $dmg *= 2;
            }
        }

        return $dmg;
    }

    public function is_dead()
    {
        return $this->is_dead;
    }

    public function is_targeted()
    {
        return $this->is_targeted;
    }

    public function selected()
    {
        return $this->is_targeted = TRUE;
    }

    public function get_ep()
    {
        return $this->units * $this->ap;
    }
}
