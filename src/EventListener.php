<?php

declare(strict_types=1);

namespace zsallazar\infinitejump;

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
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use zsallazar\infinitejump\session\Session;

final class EventListener implements Listener{
    public function onPlayerLogin(PlayerLoginEvent $event): void{
        Session::get($event->getPlayer())->load();
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void{
        $player = $event->getPlayer();

        $player->getNetworkSession()->sendDataPacket(GameRulesChangedPacket::create([
            "doImmediateRespawn" => new BoolGameRule(true, false)
        ]));

        Session::get($player)->play();
    }

    public function onPlayerMove(PlayerMoveEvent $event): void{
        $to = $event->getTo();
        $player = $event->getPlayer();


        if ($to->getY() <= 0) {
            Session::get($event->getPlayer())->die(true);
            return;
        }

        Session::get($player)->onJump($to);
    }

    public function onPlayerExhaust(PlayerExhaustEvent $event): void{
        $event->cancel();
    }

    public function onPlayerDeath(PlayerDeathEvent $event): void{
        $event->setKeepXp(true);
        $event->setKeepInventory(true);
    }

    public function onPlayerRespawn(PlayerRespawnEvent $event): void{
        Session::get($event->getPlayer())->die(false);
    }

    public function onBlockUpdate(BlockUpdateEvent $event): void{
        $event->cancel();
    }

    public function onBlockDeath(BlockDeathEvent $event): void{
        $event->cancel();
    }

    public function onBlockForm(BlockFormEvent $event): void{
        $event->cancel();
    }

    public function onBlockGrow(BlockGrowEvent $event): void{
        $event->cancel();
    }

    public function onBlockMelt(BlockMeltEvent $event): void{
        $event->cancel();
    }

    public function onBlockSpread(BlockSpreadEvent $event): void{
        $event->cancel();
    }

    public function onBlockTeleport(BlockTeleportEvent $event): void{
        $event->cancel();
    }

    public function onEntityTrampleFarmland(EntityTrampleFarmlandEvent $event): void{
        $event->cancel();
    }

    public function onFarmlandHydrationChange(FarmlandHydrationChangeEvent $event): void{
        $event->cancel();
    }

    public function onLeavesDecay(LeavesDecayEvent $event): void{
        $event->cancel();
    }
}