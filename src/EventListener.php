<?php

declare(strict_types=1);

namespace zsallazar\infinitejump;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockDeathEvent;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\block\BlockMeltEvent;
use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\event\block\BlockTeleportEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\block\FarmlandHydrationChangeEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\entity\EntityTrampleFarmlandEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use zsallazar\infinitejump\session\Session;
use function str_starts_with;

final class EventListener implements Listener{
    public function onPlayerJoin(PlayerJoinEvent $event): void{
        $event->getPlayer()->getNetworkSession()->sendDataPacket(GameRulesChangedPacket::create([
            "doImmediateRespawn" => new BoolGameRule(true, false)
        ]));
    }

    public function onPlayerMove(PlayerMoveEvent $event): void{
        $player = $event->getPlayer();

        if ($this->isInWorld($player)) {
            $to = $event->getTo();

            if ($to->getY() <= 0) {
                Session::get($event->getPlayer())->die(true);
                return;
            }

            Session::get($player)->onJump($to);
        }
    }

    public function onPlayerExhaust(PlayerExhaustEvent $event): void{
        if ($this->isInWorld($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event): void{
        if ($this->isInWorld($event->getPlayer())) {
            $event->setKeepXp(true);
            $event->setKeepInventory(true);
        }
    }

    public function onPlayerRespawn(PlayerRespawnEvent $event): void{
        Session::get($event->getPlayer())->die(false);
    }

    public function onEntityTrampleFarmland(EntityTrampleFarmlandEvent $event): void{
        if ($this->isInWorld($event->getEntity())) {
            $event->cancel();
        }
    }

    public function onBlockUpdate(BlockUpdateEvent $event): void{
        if ($this->isInWorld($event->getBlock())) {
            $event->cancel();
        }
    }

    public function onBlockDeath(BlockDeathEvent $event): void{
        if ($this->isInWorld($event->getBlock())) {
            $event->cancel();
        }
    }

    public function onBlockForm(BlockFormEvent $event): void{
        if ($this->isInWorld($event->getBlock())) {
            $event->cancel();
        }
    }

    public function onBlockGrow(BlockGrowEvent $event): void{
        if ($this->isInWorld($event->getBlock())) {
            $event->cancel();
        }
    }

    public function onBlockMelt(BlockMeltEvent $event): void{
        if ($this->isInWorld($event->getBlock())) {
            $event->cancel();
        }
    }

    public function onBlockSpread(BlockSpreadEvent $event): void{
        if ($this->isInWorld($event->getBlock())) {
            $event->cancel();
        }
    }

    public function onBlockTeleport(BlockTeleportEvent $event): void{
        if ($this->isInWorld($event->getBlock())) {
            $event->cancel();
        }
    }

    public function onLeavesDecay(LeavesDecayEvent $event): void{
        if ($this->isInWorld($event->getBlock())) {
            $event->cancel();
        }
    }

    public function onFarmlandHydrationChange(FarmlandHydrationChangeEvent $event): void{
        if ($this->isInWorld($event->getBlock())) {
            $event->cancel();
        }
    }

    private function isInWorld(Entity|Block $object): bool{
        return str_starts_with($object->getPosition()->getWorld()->getFolderName(), "InfiniteJump-");
    }
}