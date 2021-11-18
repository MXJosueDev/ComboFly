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

    public static function saveAll() {
        self::saveFile("config.yml");
        self::ensureDirectory("data");
    }

    public static function ensureDirectory(string $directory): void {
        mkdir(self::getPath($directory));
    }

    public static function getDataFolder(): string {
        return Loader::getInstace()->getDataFolder();
    }

    public static function getPath(string $path): string {
        return self::getDataFolder() . str_replace(["/"], [DIRECTORY_SEPARATOR], $path);
    }

    public static function getValue(string $key, $default = null, string $file = "config.yml") {
        return self::getConfig($file)->get($key, $default);
    }

    public static function setValue(string $key, $value, string $file = "config.yml") {
        self::getConfig($file)->set($key, $value);
        self::getConfig($file)->save();
    }

    public static function saveFile(string $filePath): void {
        Loader::getInstace()->saveResource($filePath);
    }

    public static function getConfig(string $filePath = "config.yml"): Config {
        return new Config(self::getDataFolder() . $filePath);
    }

    public static function getPrefix(): string {
        return str_replace(["§"], ["&"], self::getValue("prefix")) . " §r";
    }
}