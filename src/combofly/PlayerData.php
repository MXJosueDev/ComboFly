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

class PlayerData implements \JsonSerializable {

    public static function generateBasicData(Player $player): array {
        return [
            "player" => $player->getName(),
            "uuid"   => $player->getUniqueId()->toString(),
            "kills"  => 0,
            "deaths" => 0
        ];
    }

    public $player;
    private $data;

    public function __construct(Player $player) {
        $this->player = $player;

        if(!is_file($this->getPath())) {
            $this->data = self::generateBasicData($player);
            $this->save();
        }

        $file = file_get_contents(ConfigManager::getPath("data/{$player->getUniqueId()->toString()}"));
        $this->data = json_decode($file, true);
        
        $this->updateData();
    }

    public function getUuid() {
        return $this->get("uuid");
    }

    public function updateData(): void {
        $player = $this->getPlayer();

        $this->set("player", $player->getName());
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function getPath(): string {
        return ConfigManager::getPath("data/{$this->getPlayer()->getUniqueId()->toString()}");
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