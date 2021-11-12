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
use pocketmine\Player;

class PlayerData implements JsonSerializable {

    private $data;

    public function __construct(Player $player) {
        $file = file_get_contents(ConfigManager::getPath("data/{$player->getXuid()}"));
        $this->data = json_decode($file, true);
    }

    public function set(string $key, $value): void {
        $this->data[$key] = $value;
    }

    public function get(): void {

    }

    public function jsonSerialize() {
        return $data;
    }
}