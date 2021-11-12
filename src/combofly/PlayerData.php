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

    public $player;
    private $data;

    public function __construct(Player $player) {
        $this->player = $player;

        if(!is_file($this->getPath())) {
            file_put_contents($this->getPath(), self::generateBasicData()); // TODO basic data
            return;
        }

        $file = file_get_contents(ConfigManager::getPath("data/{$player->getXuid()}"));
        $this->data = json_decode($file, true);
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function getPath(): string {
        return ConfigManager::getPath("data/{$this->getPlayer()->getXuid()}");
    }

    public function set(string $key, $value): void {
        $this->data[$key] = $value;
        $this->save();
    }

    public function get(string $key) {
        return $this->data[$key];
    }

    public function save(): void {
        file_put_contents($this->getPath(), json_encode($this));
    }

    public function jsonSerialize() {
        return $data;
    }

    public function __destruct() {
        $this->save();
    }
}