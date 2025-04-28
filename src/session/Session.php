<?php

declare(strict_types=1);

namespace zsallazar\infinitejump\session;

use InvalidArgumentException;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\world\sound\XpCollectSound;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;
use WeakMap;
use zsallazar\infinitejump\generator\VoidGenerator;
use zsallazar\infinitejump\mode\Mode;
use zsallazar\infinitejump\mode\NoStopMode;
use zsallazar\infinitejump\mode\Section;

final class Session{
    /**
     * WeakMap ensures that the session is destroyed when the player is destroyed, without causing any memory leaks
     *
     * @var WeakMap
     * @phpstan-var WeakMap<Player, self>
     */
    private static WeakMap $sessions;

    public static function get(Player $player): self{
        if (!isset(self::$sessions)) {
            /** @phpstan-var WeakMap<Player, self> $map */
            $map = new WeakMap();
            self::$sessions = $map;
        }

        return self::$sessions[$player] ??= new self($player);
    }

    private Mode $mode;

    private World $world; //TODO: Is this needed? $player->getWorld()

    public function __construct(
        private readonly Player $player
    ) {}

    public function getMode(): Mode{ return $this->mode; }

    public function setMode(Mode $mode): void{
        $this->mode = $mode;
    }

    public function load(): void{
        $name = "InfiniteJump-" . $this->player->getName();
        $worldManager = $this->player->getServer()->getWorldManager();

        //TODO: Desto weiter desto schwiergier + config etc

        $worldManager->generateWorld(
            $name,
            WorldCreationOptions::create()
                ->setGeneratorClass(VoidGenerator::class)
                ->setDifficulty(World::DIFFICULTY_PEACEFUL)
                ->setSpawnPosition(new Vector3(0.5, 40, 0.5))
        );

        $world = $worldManager->getWorldByName($name);
        if ($world === null) {
            throw new InvalidArgumentException("World $name is not loaded");
        }
        $this->world = $world;

        $this->mode = new NoStopMode($this->world);
    }

    public function play(): void{
        $spawn = $this->mode->start();

        $this->player->setSpawn($spawn);
        $this->player->teleport($spawn);
        $this->player->setGamemode(GameMode::ADVENTURE);

        $xpManager = $this->player->getXpManager();
        $xpManager->setCanAttractXpOrbs(false);
        $xpManager->setXpLevel(0);
    }

    public function onJump(Location $to): void{
        $xpManager = $this->player->getXpManager();
        $xpLevel = $xpManager->getXpLevel();
        $currentBlockPos = $this->world->getBlock($to->subtract(0, 1, 0))->getPosition();

        foreach ($this->mode->getSections() as $level => $section) {
            if ($xpLevel >= $level) {
                continue;
            }

            foreach ($section->getParts() as $part) {
                if (!$currentBlockPos->equals($part[Section::POSITION])) {
                    continue;
                }

                for ($i = 1; $i <= $level - $xpLevel; $i++) {
                    $this->mode->placeNewSection();
                    $this->mode->removeFirstSection();
                }

                $this->player->setSpawn($currentBlockPos->add(0, 1, 0));

                $xpManager->setXpLevel($level);
                $this->player->broadcastSound(new XpCollectSound());
                return;
            }
        }
    }

    public function die(bool $teleport): void{
        $spawn = $this->player->getSpawn();

        if ($teleport) {
            $this->player->teleport($spawn);
        }
        //TODO: database
    }

    public function end(): void{
        //TODO
    }
}