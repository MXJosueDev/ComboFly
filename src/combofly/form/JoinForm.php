<?php
declare(strict_types=1);

/*
 *   _____                _           ______ _       
 *  / ____|              | |         |  ____| |      
 * | |     ___  _ __ ___ | |__   ___ | |__  | |_   _ 
 * | |    / _ \| '_ ` _ \| '_ \ / _ \|  __| | | | | |
 * | |___| (_) | | | | | | |_) | (_) | |    | | |_| |
 *  \_____\___/|_| |_| |_|_.__/ \___/|_|    |_|\__, |
 *                                             __/ |
 *                                            |___/ 
 */

namespace combofly\form;

use combofly\Arena;
use combofly\utils\ConfigManager;
use combofly\api\form\jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;

class JoinForm {

    public function __construct(Player $player) {
        $form = new SimpleForm(function(Player $player, int $data = null) {
            if(is_null($data))
                return;
            
            switch($data) {
                case 0:
                    Arena::getInstance()->addPlayer($player);
                    break;
                case 1:
                    Arena::getInstance()->addSpectator($player, false);
                    break;
            }
        });

        $formData = ConfigManager::getValue("join-menu", ["title" => "&l&bCombo&3Fly", "content" => "&7How do you want to join the arena?", "buttons" => ["player" => "&cPlayer", "spectator" => "&cSpectator"]], "menus.yml");
        $formTitle = str_replace(["&"], ["§"], $formData["title"]);
        $formContent = str_replace(["&"], ["§"], $formData["content"]);
        $formButtonPlayer = str_replace(["&"], ["§"], $formData["buttons"]["player"] . "\n&r&7Click to select!");
        $formButtonSpectator = str_replace(["&"], ["§"], $formData["buttons"]["spectator"] . "\n&r&7Click to select!");

        $form->setTitle($formTitle);
        $form->setContent($formContent);

        $form->addButton($formButtonPlayer);
        $form->addButton($formButtonSpectator);

        $form->sendToPlayer($player);
    }
}