<?php

include_once('./Group.php');

$input  = file_get_contents("./input");

if (!empty($argv[1]))
{
    $input  = file_get_contents("./test");
}

$inputs = explode("\n", trim($input));


## Build out the groups

$type = '';

foreach ($inputs as $dat)
{
    if (trim($dat) == '') continue;

    if (preg_match('/^(.*):$/', trim($dat), $m))
    {
        $type = $m[1];
        continue;
    }

    preg_match('/(\d+) units each with (\d+) hit points( \(.+\))? with an attack that does (\d+) (.+) damage at initiative (\d+)/', trim($dat), $m);

    #print_r($m);

    $immunity = [];
    $weakness = [];

    if (!empty($m[3]))
    {
        if (preg_match('/immune to ([^;)]+)/', trim($m[3]), $mat))
        {
            $immunity = explode(', ', $mat[1]);
        }
        if (preg_match('/weak to ([^;)]+)/', trim($m[3]), $mat))
        {
            $weakness = explode(', ', $mat[1]);
        }
    }

    $groups[] = new Group($type, $m[1], $m[2], $m[4], $m[5], $m[6], $immunity, $weakness);
}

#print_r($groups);


## Now the combat
$in_combat = TRUE;
$i = 0;

while ($in_combat)
{
    #echo 'checking if combat conditions still active...',"\n";

    $units_left = [];

    foreach ($groups as $group)
    {
        if (!key_exists($group->type, $units_left))
        {
            $units_left[ $group->type ] = 0;
        }

        $units_left[ $group->type ] += $group->units;
    }

    if (count($units_left) == 1)
    {
        $in_combat = FALSE;
        break;
    }

    #echo 'groups selecting targets...',"\n";

    // group with the highest effective power gets to target first
    usort($groups, function($a,$b){
        $a_ep = $a->units * $a->ap;
        $b_ep = $b->units * $b->ap;
        if ($a_ep == $b_ep) {
            return $b->initiative <=> $a->initiative;
        }
        return $b_ep <=> $a_ep;
    });

    foreach ($groups as $group)
    {
        $group->target($groups);
    }

    echo 'combat starting...',"\n";

    // combat in initiative order
    usort($groups, function($a,$b){
        return $b->initiative <=> $a->initiative;
    });

    foreach ($groups as $group)
    {
        if ($group->is_dead()) continue;

        $group->fight();
    }

    #echo 'turns ending...',"\n";

    // end turn and repeat
    foreach ($groups as $group)
    {
        $group->end_turn();
    }

    #echo 'despawning dead...',"\n";

    // despawn the dead
    $groups = array_filter($groups, function($g){
        return !$g->is_dead();
    });

    $i++;
}

$sum = array_reduce($groups, function($units, $group){
    return $units += $group->units;
}, 0);

foreach ($groups as $group)
{
    echo sprintf('%s group #%d has %d units remaining', $group->type, $group->id, $group->units),"\n";
}

echo $sum,"\n";

