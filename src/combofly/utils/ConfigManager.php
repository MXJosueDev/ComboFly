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

namespace combofly\utils;

use combofly\Loader;
use pocketmine\utils\Config;

class ConfigManager {

    public static $defaultConfig;

    public static function saveAll() {
        self::saveFile("config.yml");
        self::saveFile("kit.yml");
        
        self::$defaultConfig = self::getConfig();
    }

    public static function getValue(string $key) {
        return self::getDefConfig()->get($key);
    }

    public static function saveFile(string $filePath): void {
        Loader::getInstace()->saveResource($filePath);
    }

    public static function getConfig(string $filePath = "config.yml"): Config {
        return new Config(Loader::getInstace()->getDataFolder() . $filePath);
    }

    public static function getDefConfig(): Config {
        return self::$defaultConfig;
    }

    public static function getPrefix(): string {
        return self::getDefConfig()->get("prefix");
    }
}