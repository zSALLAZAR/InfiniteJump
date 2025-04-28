<?php

declare(strict_types=1);

namespace zsallazar\infinitejump\mode;

use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\Cauldron;
use pocketmine\block\Crops;
use pocketmine\block\Element;
use pocketmine\block\EndPortalFrame;
use pocketmine\block\Farmland;
use pocketmine\block\FillableCauldron;
use pocketmine\block\Furnace;
use pocketmine\block\Jukebox;
use pocketmine\block\PitcherCrop;
use pocketmine\block\PotionCauldron;
use pocketmine\block\RedstoneLamp;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\block\TNT;
use pocketmine\block\Trapdoor;
use pocketmine\block\utils\RecordType;
use pocketmine\block\utils\SlabType;
use pocketmine\block\utils\StairShape;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\world\sound\RecordSound;
use pocketmine\world\World;
use function array_rand;
use function mt_rand;

class NormalMode extends Mode{
    /** @var Block[] */
    private array $randomBlocks;

    public function __construct(World $world) {
        parent::__construct($world);

        $this->prepareRandomBlocks();
    }

    private function prepareRandomBlocks(): void{
        foreach (VanillaBlocks::getAll() as $block) {
            $allow = true;

            if (
                $block instanceof Element ||
                $block->getTypeId() === BlockTypeIds::BARRIER ||
                $block->getTypeId() === BlockTypeIds::INVISIBLE_BEDROCK ||
                !$block->isFullCube()
            ) {
                $allow = false;
            }

            if (
                $block instanceof Cauldron ||
                $block instanceof FillableCauldron ||
                $block instanceof Farmland
            ) {
                $allow = true;
            }

            if ($block instanceof Slab) {
                $block->setSlabType(SlabType::TOP);
                $allow = true;
            }

            if ($block instanceof Stair) {
                $block->setUpsideDown(true);
                $allow = true;
            }

            if ($block instanceof Trapdoor) {
                $block->setTop(true);
                $allow = true;
            }

            if ($block instanceof TNT) {
                $block->setUnstable(true);
                $allow = true;
            }

            if ($allow) {
                $this->randomBlocks[] = $block;
            }
        }
    }

    public function createSection(Vector3 $pos): Section{
        $facing = Facing::HORIZONTAL;
        $randomBool = mt_rand(0, 1) === 0;
        $randomFacing = $facing[array_rand($facing)];

        $block = $this->randomBlocks[array_rand($this->randomBlocks)];
        $section = new Section();

        //TODO: Random facing for all blocks that support facing

        if ($block instanceof Stair) {
            $allStairShapes = StairShape::cases();
            $block->setShape($allStairShapes[array_rand($allStairShapes)]);
            $block->setFacing($randomFacing);
        }

        if ($block instanceof EndPortalFrame) {
            $block->setEye($randomBool);
        }

        if ($block instanceof Furnace) {
            $block->setLit($randomBool);
        }

        if ($block instanceof RedstoneLamp) {
            $block->setPowered($randomBool);
        }

        if ($block instanceof Jukebox) {
            $allRecordTypes = RecordType::cases();
            $recordType = $allRecordTypes[array_rand($allRecordTypes)];
            foreach ($this->world->getPlayers() as $player) {
                $player->sendJukeboxPopup(KnownTranslationFactory::record_nowPlaying($recordType->getTranslatableName()));
            }
            $this->world->addSound($pos, new RecordSound($recordType));
        }

        if ($block instanceof FillableCauldron) {
            $block->setFillLevel(mt_rand(FillableCauldron::MIN_FILL_LEVEL, FillableCauldron::MAX_FILL_LEVEL));

            if ($block instanceof PotionCauldron) {
                $allPotionTypes = PotionType::cases();
                $block->setPotionItem(VanillaItems::POTION()->setType($allPotionTypes[array_rand($allPotionTypes)]));
            }
        }

        if ($block instanceof Farmland) {
            $block->setWetness(mt_rand(0, Farmland::MAX_WETNESS));

            $top = [
                VanillaBlocks::PITCHER_CROP()->setAge(mt_rand(0, PitcherCrop::MAX_AGE)),
                VanillaBlocks::MELON_STEM()->setAge(mt_rand(0, Crops::MAX_AGE))->setFacing($randomFacing),
                VanillaBlocks::PUMPKIN_STEM()->setAge(mt_rand(0, Crops::MAX_AGE))->setFacing($randomFacing),
                VanillaBlocks::BEETROOTS()->setAge(mt_rand(0, Crops::MAX_AGE)),
                VanillaBlocks::CARROTS()->setAge(mt_rand(0, Crops::MAX_AGE)),
                VanillaBlocks::POTATOES()->setAge(mt_rand(0, Crops::MAX_AGE)),
                VanillaBlocks::WHEAT()->setAge(mt_rand(0, Crops::MAX_AGE))
            ];
            //TODO: Find a better solution for this
            $section->add($pos, $block);
            $section->add($pos->add(0, 1, 0), $top[array_rand($top)]);
            return $section;
        }

        $section->add($pos, $block);
        return $section;
    }
}