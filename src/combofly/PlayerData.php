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

    public static function generateBasicData(Player $player = null, string $key = null) {
        $def = [
            "player" => "Unknown",
            "uuid"   => "Unknown",
            "kills"  => 0,
            "deaths" => 0
        ];

        if(!is_null($player)) {
            $def["player"] = $player->getName();
            $def["uuid"] = $player->getUniqueId()->toString();
        }

        if(is_null($key))
            return $def;

        return $def[$key];
    }

    public $player;
    private $data;

    public function __construct(string $player) {
        $this->player = $player;

        if(!is_file($this->getPath())) {
            $this->data = self::generateBasicData($player);
            $this->save();
        }

        $uuid = $player->getUniqueId()->toString();

        $file = file_get_contents(ConfigManager::getPath("data/{$uuid}"));
        $this->data = json_decode($file, true);
        
        $this->updateData();
    }

    public function getUuid() {
        return $this->get("uuid");
    }

    public function updateData(): void {
        $player = $this->getPlayer();

        $updateKeys = ["player"];

        foreach($updateKeys as $key) {
            $this->set($key, self::generateBasicData($player, $key));
        }
    }

    public function getPlayer(): ?Player {
        $player = Loader::getInstance()->getServer()->getPlayerExact($this->player);

        if(is_null($player)) {
            throw new \Exception("The player must be online!");
        }

        return $player;
    }

    public function getPath(): string {
        return ConfigManager::getPath("data/{$this->getPlayer()->getUniqueId()->toString()}");
    }

    public function set(string $key, $value): void {
        $this->data[$key] = $value;
        $this->save();
    }

    public function get(string $key) {
        return isset($this->data[$key]) ? $this->data[$key] : self::generateBasicData(null, $key);
    }

    public function save(): void {
        file_put_contents($this->getPath(), json_encode($this));
    }

    public function jsonSerialize() {
        return $this->data;
    }

    public function __destruct() {
        $this->save();
    }
}