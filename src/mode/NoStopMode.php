<?php

declare(strict_types=1);

namespace zsallazar\infinitejump\mode;

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use function array_rand;

class NoStopMode extends Mode{
    public function createSection(Vector3 $pos): Section{
        $facing = Facing::HORIZONTAL;
        $section =  new Section();
        $section->add($pos, VanillaBlocks::BIG_DRIPLEAF_HEAD()->setFacing($facing[array_rand($facing)]));
        return $section;
    }
}