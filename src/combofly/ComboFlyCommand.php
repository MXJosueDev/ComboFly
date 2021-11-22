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
use combofly\entity\EntityManager;
use combofly\form\JoinForm;
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
                §7=================== §l§bCombo§3Fly §r§7===================§r\n
                §r/{$label} help: §r§cGet help on the sub-commands.§r\n
                §r/{$label} join§7: §r§cJoin the arena by means of a command.§r\n
                §r/{$label} setarena§7: §r§cSet where players appear in the arena.§r\n
                §r/{$label} setlobby§7: §r§cSet where players appear when exiting the arena.§r\n
                §r/{$label} setkit§7: §r§cConfigure the kit with which the players appear in the arena (The kit will be configured with your inventory).§r\n
                §r/{$label} setjoin§7: §r§cPut the JoinNPC in your current location.§r\n
                §r/{$label} removejoin§7: §r§cRemove the JoinNPC (Hit it).§r\n
                §7=================== §l§bCombo§3Fly §r§7===================§r\n
                ");
                break;
            case "join":
                if(!$this->checkConsole($sender)) return;
                if(!$this->hasPermission($sender, "combofly.command.join.with.command")) return;

                (new JoinForm($sender));
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
            case "setjoin":
                if(!$this->checkConsole($sender)) return;
                if(!$this->hasPermission($sender, "combofly.command.setjoin")) return;

                EntityManager::setJoinNPC($sender);
                $sender->sendMessage(ConfigManager::getPrefix() . "§7The JoinNPC was successfully placed.");
                break;
            case "removejoin":
                if(!$this->checkConsole($sender)) return;
                if(!$this->hasPermission($sender, "combofly.command.removejoin")) return;

                EventListener::setRemoveEntity($sender);
                $sender->sendMessage(ConfigManager::getPrefix() . "§7Please hit the NPC you want to remove (Expires in 3 minutes).");
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