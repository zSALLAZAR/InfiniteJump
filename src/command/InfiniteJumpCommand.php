<?php

declare(strict_types=1);

namespace zsallazar\infinitejump\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use zsallazar\infinitejump\form\MainForm;

final class InfiniteJumpCommand extends Command{
    public function __construct() {
        parent::__construct("infinitejump", "Open the InfiniteJump form", aliases: ["jump"]);

        $this->setPermission("infinitejump.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("Please run this command in-game.");
            return;
        }

        $sender->sendForm(new MainForm());
    }
}