<?php

declare(strict_types=1);

namespace zsallazar\infinitejump\mode;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\Position;
use pocketmine\world\World;
use function array_key_first;
use function array_key_last;
use function mt_rand;

abstract class Mode{
    protected World $world;

    /** @phpstan-var array<int, Section> */
    private array $sections;

    /**
     * @phpstan-return array<int, Section>
     */
    final public function getSections(): array{ return $this->sections; }

    abstract protected function createSection(Vector3 $pos): Section;

    protected function calculateNewSectionPos(Section $lastSection): Vector3{
        $x = mt_rand(3, 5);
        $y = 1 - (($x >= 5) ? 2 : mt_rand(0, 1));
        $z = mt_rand($x >= 4 ? -1 : -2, $x >= 4 ? 1 : 2);

        return $lastSection->getFirstPosition()->add($x, $y, $z);
    }

    final public function start(World $world): Position{
        $this->world = $world;

        $spawn = $world->getSpawnLocation();
        $blockPos = $spawn->subtract(0, 1, 0);
        $startBlock = VanillaBlocks::BEDROCK();

        $section = new Section();
        $section->add($blockPos, $startBlock);

        $world->setBlock($blockPos, $startBlock);
        $this->sections[] = $section;

        for ($i = 0; $i < 5; $i++) {
            $this->placeNewSection();
        }

        return $spawn;
    }

    final public function placeNewSection(): void{
        $pos = $this->calculateNewSectionPos($this->sections[array_key_last($this->sections)]);
        $section = $this->createSection($pos);
        $this->sections[] = $section;

        foreach ($section->getParts() as $block) {
            $this->world->setBlock($block[Section::POSITION], $block[Section::BLOCK]);
        }
    }

    final public function removeFirstSection(bool $breakBlocks = true): void{
        if ($breakBlocks) {
            foreach ($this->sections[array_key_first($this->sections)]->getParts() as $part) {
                $pos = $part[Section::POSITION];
                $block = $this->world->getBlock($pos);

                //This needs silk touch so ice block don't place water
                $block->onBreak(VanillaItems::AIR()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SILK_TOUCH())));
                $this->world->addParticle($pos, new BlockBreakParticle($block));
            }
        }

        unset($this->sections[array_key_first($this->sections)]);
    }
}