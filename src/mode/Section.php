<?php

declare(strict_types=1);

namespace zsallazar\infinitejump\mode;

use pocketmine\block\Block;
use pocketmine\math\Vector3;

final class Section{
    public const string POSITION = "pos";
    public const string BLOCK = "block";

    /**
     * @phpstan-var array<array{
     *      pos: Vector3,
     *      block: Block
     *  }>
     */
    protected array $parts;

    /**
     * @phpstan-return array<array{
     *     pos: Vector3,
     *     block: Block
     * }>
     */
    public function getParts(): array{ return $this->parts; }

    public function getFirstPosition(): Vector3{
        return $this->parts[0][self::POSITION];
    }

    public function add(Vector3 $pos, Block $block): void{
        $this->parts[] = [self::POSITION => $pos, self::BLOCK => $block];
    }
}