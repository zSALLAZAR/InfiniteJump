<?php

declare(strict_types=1);

namespace zsallazar\infinitejump;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\generator\GeneratorManager;
use zsallazar\infinitejump\command\InfiniteJumpCommand;
use zsallazar\infinitejump\generator\VoidGenerator;

final class InfiniteJump extends PluginBase{
    use SingletonTrait {
        setInstance as private;
        reset as private;
    }

    protected function onEnable(): void{
        GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, "Void", fn() => null);

        $server = $this->getServer();
        $server->getPluginManager()->registerEvents(new EventListener(), $this);
        $server->getCommandMap()->register($this->getName(), new InfiniteJumpCommand());
    }
}