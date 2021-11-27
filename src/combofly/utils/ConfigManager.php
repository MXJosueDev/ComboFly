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
        self::ensureDirectory("data");
        self::saveFile("settings.yml");
        self::saveFile("entities.yml");
        self::saveFile("kit.yml");
        self::saveFile("menus.yml");
        self::saveFile("scoreboard.yml");
    }

    public static function ensureDirectory(string $directory): void {
        if(!is_dir(self::getPath($directory)))
            mkdir(self::getPath($directory));
    }

    public static function getDataFolder(): string {
        return Loader::getInstance()->getDataFolder();
    }

    public static function getPath(string $path): string {
        return self::getDataFolder() . str_replace(["/"], [DIRECTORY_SEPARATOR], $path);
    }

    public static function getValue(string $key, $default = null, string $file = "settings.yml") {
        return self::getConfig($file)->get($key, $default);
    }

    public static function setValue(string $key, $value, string $file = "settings.yml") {
        $config = self::getConfig($file);
        $config->set($key, $value);
        $config->save();
    }

    public static function saveFile(string $filePath): void {
        Loader::getInstance()->saveResource(str_replace(["/"], [DIRECTORY_SEPARATOR], $filePath));
    }

    public static function getConfig(string $filePath = "settings.yml"): Config {
        return new Config(self::getPath($filePath));
    }

    public static function getPrefix(): string {
        return str_replace(["&"], ["§"], self::getValue("prefix", "&l&f[&r&bCombo&3Fly&r&l&f]") . " §r");
    }
}