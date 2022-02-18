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

class SpectatorForm {

    public function __construct(Player $player) 
    {
        $formData = ConfigManager::getValue("spectator-menu", "menus.yml");
        $formTitle = str_replace(["&"], ["§"], $formData["title"]);
        $formContent = str_replace(["&"], ["§"], $formData["content"]);
        $formButtonContinue = str_replace(["&"], ["§"], $formData["buttons"]["continue"] . "\n&r&7Click to select!");
        $formButtonRespawn = str_replace(["&"], ["§"], $formData["buttons"]["respawn"] . "\n&r&7Click to select!");
        $formButtonLobby = str_replace(["&"], ["§"], $formData["buttons"]["go-to-lobby"] . "\n&r&7Click to select!");

        $form = new MenuForm(
            $formTitle,
            $formContent,

            [
                new MenuOption($formButtonContinue),
                new MenuOption($formButtonRespawn),
                new MenuOption($formButtonLobby)
            ],
            
            function(Player $submitter, int $selected): void {
                switch($selected) {
                    case 0:
                        // NOOP
                        break;
                    case 1:
                        Arena::getInstance()->respawn($player);
                        break;
                    case 2:
                        Arena::getInstance()->quitSpectator($player);
                        break;
                }
            }
        );

        $player->sendForm($form);
    }
}