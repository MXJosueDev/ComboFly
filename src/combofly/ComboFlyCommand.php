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
        if(!isset($args[0])) {
            $sender->sendMessage(ConfigManager::getPrefix() . "§7Please use §r'/{$label} help' §7to get help for the command.");
            return;
        }

        $subCommand = $args[0];

        switch($subCommand) {
            case "help":
                $sender->sendMessage("
                §7============ §bCombo§3Fly §7============§r\n
                §r/{$label} help: §r§c
                §r/{$label} join§7: §r§c
                §r/{$label} join§7: §r§c 
                §r/{$label} join§7: §r§c
                §r/{$label} join§7: §r§c
                §7============ §bCombo§3Fly §7============§r\n
                ");
                break;
            case "join":
                if(!$this->checkConsole($sender)) return;
                if(!$this->hasPermission($sender, "combofly.command.join.with.command")) return;

                Arena::getInstance()->addPlayer($sender);
                break;
            case "setarena":
                if(!$this->checkConsole($sender)) return;
                if(!$this->hasPermission($sender, "combofly.command.setarena")) return;

                Arena::getInstance()->setArena($sender);
                $sender->sendMessage(ConfigManager::getPrefix() . "§7The arena location was configured correctly.");
                break;
            case "setlobby":
                if(!$this->checkConsole($sender)) return;
                if(!$this->hasPermission($sender, "combofly.command.setlobby")) return;

                Arena::getInstance()->setLobby($sender);
                $sender->sendMessage(ConfigManager::getPrefix() . "§7The lobby location was configured correctly.");
                break;
            case "setkit":
                if(!$this->checkConsole($sender)) return;
                if(!$this->hasPermission($sender, "combofly.command.setkit")) return;

                Arena::getInstance()->setKit($sender);
                $sender->sendMessage(ConfigManager::getPrefix() . "§7The arena kit was configured correctly.");
                break;
            default:
                $sender->sendMessage(ConfigManager::getPrefix() . "§7Please use §r'/{$label} help' §7to get help for the command.");
                break;
        }
    }

    private function hasPermission(Player $player, string $permission): bool {
        if(!$player->hasPermission($permission)) {
            $player->sendMessage(ConfigManager::getPrefix() . "§cYou do not have permission to use this command!");
            return false;
        }

        return true;
    }

    private function checkConsole(Player $player): bool {
        if(!$player instanceof Player) {
            $player->sendMessage(ConfigManager::getPrefix() . "§cPlease use this command within the game.");
            return false;
        }

        return true;
    }
}