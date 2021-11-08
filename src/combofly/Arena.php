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

use combofly\tasks\ScoreboardTask;
use combofly\tasks\UpdateEntityTask;
use combofly\utils\ConfigManager;
use onebone\economyapi\EconomyAPI;
use pocketmine\Player;
use pocketmine\utils\SingletonTrait;

class Arena {
    use SingletonTrait;

    const MESSAGE = "message";
    const TITLE = "title";
    const SUBTITLE = "subtitle";
    const TIP = "tip";
    const POPUP = "popup";

    public $players = [];
    
    public function __construct() {
        self::setInstance($this);

        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ScoreboardTask(), ConfigManager::getValue("scoreboard-update-interval") * 20);
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new UpdateEntityTask(), 20);

        // TODO: Register entity & load level

        if(!is_null($this->getServer()->getPluginManager()->getPlugin("EconomyAPI"))) {
			self::$economy = EconomyAPI::getInstance();
		} else {
            Loader::getInstance()->getLogger()->alert(TF::RED . "The EconomyAPI dependency was not found!");
		}
    }

    public function addPlayer(Player $player): void {
        if($this->isPlayer($player)) return;

        $this->players[$player->getXuid()] = $player;
        
        $this->giveItems($player);

        $player->sendMessage("translate");
    }

    public function quitPlayer(Player $player): void {
        if(!$this->isPlayer($player)) return;

        unset($this->players[$player->getXuid()]);

        $player->sendMessage("translate");
    }

    public function isPlayer(Player $player): bool {
        return isset($this->players[$player->getXuid()]);
    }

    public function getAllPlayers(): array {
        $players = [];

        foreach($this->players as $xuid => $player) {
            $players[] = $player; 
        }

        return $players;
    }

    private function giveItems(Player $player): void {
        // TODO
    }

    public function broadcast(string $text, $type = self::MESSAGE): void {
        foreach($this->getAllPlayers() as $player) {
            switch($type) {
                case self::MESSAGE:
                    $player->sendMessage($text);
                    break;
                case self::TITLE:
                    $player->sendTitle($text);
                    break;
                case self::SUBTITLE:
                    $player->sendSubTitle($text);
                    break;
                case self::TIP:
                    $player->sendTip($text);
                    break;
                case self::POPUP:
                    $player->sendPopup($text);
                    break;
            }
        }
    }

    public function addKill(Player $killer, Player $died): void {  
        
    }
}