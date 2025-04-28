<?php

declare(strict_types=1);

namespace zsallazar\infinitejump\form;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use zsallazar\infinitejump\mode\NormalMode;
use zsallazar\infinitejump\mode\NoStopMode;
use zsallazar\infinitejump\session\Session;

final class MainForm extends CustomForm{
    public function __construct() {
        parent::__construct(TF::BOLD . TF::GREEN . "InfiniteJump", [
            new Dropdown("Mode", "", [
                "normal" => "Normal",
                "nostop" =>"NoStop"
            ])
        ], static function(Player $player, CustomFormResponse $data): void{
            $mode = match ($data->getString("Mode")) {
                "nostop" => new NoStopMode(),
                default => new NormalMode()
            };
            $session = Session::get($player);
            $session->setMode($mode);
            $session->load();
        });
    }
}