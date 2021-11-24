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

namespace combofly\tasks;

use combofly\Arena;
use combofly\api\scoreboard\ScoreboardAPI;
use combofly\utils\ConfigManager;
use pocketmine\scheduler\Task;
use pocketmine\utils\SingletonTrait;
use pocketmine\Player;

class ScoreboardTask extends Task {
    use SingletonTrait;

    private $scoreboardAPI;

    public function __construct() {
        self::setInstance($this);
        
        $this->scoreboardAPI = new ScoreboardAPI();
    }

    public function onRun(int $_) {
        // TODO:
        $api = $this->getScoreboardAPI();

        foreach(Arena::getInstance()->getAllPlayers() as $player) {
            $api->new($player, $player->getName(), str_replace(["&"], ["§"], ConfigManager::getValue("scoreboard-title", "&l&bCombo&3Fly", "scoreboard.yml")));

            $lines = $this->getLines($player);
            $i = 0;
                          
            foreach($lines as $line) {
                if($i < 15) {
                    $i++;
                    
                    $api->setLine($player, $i, $line);
                }
            }
        }
    }

    public function getLines(Player $player): array {
        $lines = ConfigManager::getValue("scoreboard-lines", ["", "&cPlease configure."], "scoreboard.yml");

        // TODO
    }

    public function getScoreboardAPI(): ScoreboardAPI {
        return $this->scoreboardAPI;
    }
}