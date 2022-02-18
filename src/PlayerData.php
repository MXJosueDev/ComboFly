<?php
declare(strict_types=1);

/**
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
use pocketmine\player\Player;

class PlayerData implements \JsonSerializable {

    public static function getPlayerDataByName(string $name): ?PlayerData {
        foreach(glob(ConfigManager::getPath("data/*")) as $dataUuid) {
            $file = file_get_contents(ConfigManager::getPath("data/{$dataUuid}"));
            $data = json_decode($file, true);

            if($data["player"] == $name)
                return (new PlayerData($name, $data["uuid"]));
        }

        return null;
    }

    public static function generateBasicData(string $player = null, $uuid = null, string $key = null) {
        $def = [
            "player" => "Unknown",
            "uuid"   => "Unknown",
            "kills"  => 0,
            "deaths" => 0
        ];

        if(!is_null($player)) 
            $def["player"] = $player;

        if(!is_null($uuid))
            $def["uuid"] = $uuid;


        if(is_null($key))
            return $def;

        return $def[$key];
    }

    public $player;
    public $uuid;

    private $data;

    public function __construct(string $player, $uuid) {
        $this->player = $player;
        $this->uuid = $uuid;

        $player = $this->getPlayer();

        if(!is_file($this->getPath())) {
            $this->data = self::generateBasicData($player, $uuid);
            $this->save();
        }

        $file = file_get_contents(ConfigManager::getPath("data/{$uuid}"));
        $this->data = json_decode($file, true);
        
        $this->updateData();
    }

    public function updateData(): void {
        $player = $this->getPlayer();

        $updateKeys = ["player"];

        foreach($updateKeys as $key) {
            $this->set($key, self::generateBasicData($player, $key));
        }
    }

    public function getUsername(): ?string {
        return $this->player;
    }

    public function getUuid() {
        return $this->uuid;
    }

    public function getPlayer(): ?Player {
        return Loader::getInstance()->getServer()->getPlayerExact($this->getUsername());
    }

    public function getPath(): string {
        $uuid = $this->getUuid();

        return ConfigManager::getPath("data/{$uuid}");
    }

    public function set(string $key, $value): void {
        $this->data[$key] = $value;
        $this->save();
    }

    public function get(string $key) {
        return isset($this->data[$key]) ? $this->data[$key] : self::generateBasicData(null, null, $key);
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