<?php
declare(strict_types=1);

/**
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
use combofly\api\dktapps\pmforms\MenuForm;
use combofly\api\dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class JoinForm {

    public function __construct(Player $player) 
    {
        $formData = ConfigManager::getValue("join-menu", "menus.yml");
        $formTitle = str_replace(["&"], ["§"], $formData["title"]);
        $formContent = str_replace(["&"], ["§"], $formData["content"]);
        $formButtonPlayer = str_replace(["&"], ["§"], $formData["buttons"]["player"] . "\n&r&7Click to select!");
        $formButtonSpectator = str_replace(["&"], ["§"], $formData["buttons"]["spectator"] . "\n&r&7Click to select!");

        $form = new MenuForm(
            $formTitle,
            $formContent,

            [
                new MenuOption($formButtonPlayer),
                new MenuOption($formButtonSpectator)
            ],
            
            function(Player $submitter, int $selected): void {
                switch($selected) {
                    case 0:
                        Arena::getInstance()->addPlayer($player);
                        break;
                    case 1:
                        Arena::getInstance()->addSpectator($player, false);
                        break;
                }
            }
        );

        $player->sendForm($form);
    }
}