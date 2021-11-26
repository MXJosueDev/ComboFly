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

    private const EMPTY_CACHE = ["§0\e", "§1\e", "§2\e", "§3\e", "§4\e", "§5\e", "§6\e", "§7\e", "§8\e", "§9\e", "§a\e", "§b\e", "§c\e", "§d\e", "§e\e"];

    private $scoreboardAPI;

    public function __construct() {
        self::setInstance($this);
        
        $this->scoreboardAPI = new ScoreboardAPI();
    }

    public function onRun(int $_) {
        $api = $this->getScoreboardAPI();

        $title = str_replace(["&"], ["§"], ConfigManager::getValue("scoreboard-title", "&l&bCombo&3Fly", "scoreboard.yml"));

        foreach(Arena::getInstance()->getPlayers() as $player) {
            $api->new($player, $player->getName(), $title);

            $lines = $this->getLines($player);
            $i = 0;

            foreach($lines as $line) {
                if($i < 15) {
                    $i++;
                    
                    $api->setLine($player, $i, $line);
                }
            }
        }

        foreach(Arena::getInstance()->getSpectators() as $player) {
            $api->new($player, $player->getName(), $title);

            $lines = $this->getLines($player, true);
            $i = 0;

            foreach($lines as $line) {
                if($i < 15) {
                    $i++;
                    
                    $api->setLine($player, $i, $line);
                }
            }
        }
    }

    public function getLines(Player $player, bool $spectator = false): array {
        $lines = [];

        if(!$spectator) {
            $lines = ConfigManager::getValue("scoreboard-lines", ["", "&cPlease configure me."], "scoreboard.yml");
        } else {
            $lines = ConfigManager::getValue("scoreboard-lines-spectator", ["", "&cPlease configure me."], "scoreboard.yml");
        }

        $replace = [
            "{date}"                => date("d/m/Y");
            "{player_kills}"        => Arena::getInstance()->getKills($player),
            "{player_deaths}"       => Arena::getInstance()->getDeaths($player),
            "{player_ping}"         => $player->getPing(),
            "{player_display_name}" => $player->getDisplayName(),
            "{player_real_name}"    => $player->getName(),
            "{playing}"             => count(Arena::getInstance()->getPlayers()),
            "{spectating}"          => count(Arena::getInstance()->getSpectators()),
            "{total_players}"       => count(Arena::getInstance()->getAllPlayers()),
            "&"                     => "§"
        ];

        foreach($lines as $line => $value) {
            if(empty($value)) 
                $value = self::EMPTY_CACHE[$line] ?? "";

            $lines[$line] = str_replace(array_keys($replace), array_values($replace), $value);
        }

        return $lines;
    }

    public function getScoreboardAPI(): ScoreboardAPI {
        return $this->scoreboardAPI;
    }
}