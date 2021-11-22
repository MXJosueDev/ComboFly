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

class SpectatorForm {

    public function __construct(Player $player) {
        $form = new SimpleForm(function(Player $player, int $data = null) {
            if(is_null($data))
                return;
            
            switch($data) {
                case 0:
                    // NOOP
                    break;
                case 1:
                    Arena::getInstance()->respawn($player);
                    break;
                case 3:
                    Arena::getInstance()->quitSpectator($player);
                    break;
            }
        });

        $formData = ConfigManager::getValue("spectator-menu", ["title" => "&l&bSpectator", "content" => "&7You want to do?", "buttons" => ["continue" => "&cContinue Spectating", "respawn" => "&cRespawn", "go-to-lobby" => "&cGo to lobby"]], "menus.yml");
        $formTitle = str_replace(["&"], ["§"], $formData["title"]);
        $formContent = str_replace(["&"], ["§"], $formData["content"]);
        $formButtonContinue = str_replace(["&"], ["§"], $formData["buttons"]["continue"] . "\n&r&7Click to select!");
        $formButtonRespawn = str_replace(["&"], ["§"], $formData["buttons"]["respawn"] . "\n&r&7Click to select!");
        $formButtonLobby = str_replace(["&"], ["§"], $formData["buttons"]["go-to-lobby"] . "\n&r&7Click to select!");

        $form->setTitle($formTitle);
        $form->setContent($formContent);

        $form->addButton($formButtonContinue);
        $form->addButton($formButtonRespawn);
        $form->addButton($formButtonLobby);

        $form->sendToPlayer($player);
    }
}