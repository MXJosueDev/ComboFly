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

namespace combofly;

use combofly\utils\ConfigManager;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class ComboFlyCommand extends PluginCommand {

    public function __construct() {
        parent::__construct("combofly", Loader::getInstance());

        $this->setDescription("Look at the available commands and get help from the command.");
		$this->setAliases(["cf"]);
    }

    public function execute(CommandSender $sender, string $label, array $args): void {
        $subCommand = $args[0];

        // TODO:
        switch($subCommand) {
            case "help":
                $sender->sendMessage("");
                break;
            default:
                $sender->sendMessage("");
                break;
        }
    }
}