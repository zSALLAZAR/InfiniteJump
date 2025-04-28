<?php

declare(strict_types=1);

namespace zsallazar\infinitejump;

use pocketmine\plugin\PluginBase;
use pocketmine\world\generator\GeneratorManager;
use zsallazar\infinitejump\generator\VoidGenerator;

final class InfiniteJump extends PluginBase{
    protected function onEnable(): void{
        GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, "Void", fn() => null);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }
}