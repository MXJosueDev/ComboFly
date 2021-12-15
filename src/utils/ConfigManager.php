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

    public static function init() {
        self::ensureDirectory("data");
        self::saveFile("settings.yml");
        self::saveFile("scoreboard.yml");
        self::saveFile("entities.yml");
        self::saveFile("kit.yml");
        self::saveFile("menus.yml");
        self::fixConfigs();
    }

    public static function fixConfigs(): void {
        $configs = ["settings.yml" => ConfigVersions::SETTINGS, "scoreboard.yml" => ConfigVersions::SCOREBOARD, "entities.yml" => ConfigVersions::ENTITIES, "kit.yml" => ConfigVersions::KIT, "menus.yml" => ConfigVersions::MENUS];

        foreach($configs as $configFile => $configVersion) {
            $config = self::getAllValues($configFile);

            if(!array_key_exists("config-version", $config) || !is_string($version = $config["config-version"]) || version_compare($version, $configVersion) < 0) {
                @unlink(self::getPath($configFile) . ".old");
                @rename(self::getPath($configFile), self::getPath($configFile) . ".old");
    
                self::setAllValues(self::getDefault(null, $configFile, true), $configFile);
    
                Loader::getInstance()->getLogger()->notice("The {$configFile} configuration is outdated, it was renamed to {$configFile}.old and the {$configFile} file was created with the default values of the current version.");
            }
        }
    }

    public static function getDefault(?string $key, string $file, bool $all = false) {
        $defaults = [];

        switch($file) {
            case "settings.yml":
                $defaults = ConfigDefaults::SETTINGS;
                break;
            case "entities.yml":
                $defaults = ConfigDefaults::ENTITIES;
                break;
            case "kit.yml":
                $defaults = ConfigDefaults::KIT;
                break;
            case "menus.yml":
                $defaults = ConfigDefaults::MENUS;
                break;
            case "scoreboard.yml":
                $defaults = ConfigDefaults::SCOREBOARD;
                break;
        }

        if($all || is_null($key))
            return $defaults;
 
        return isset($defaults[$key]) ? $defaults[$key] : '';
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

    public static function getValue(string $key, string $file = "settings.yml") {
        if($key === "config-version")
            return self::getConfig($file)->get($key);

        return self::getConfig($file)->get($key, self::getDefault($key, $file));
    }

    public static function getAllValues(string $file = "settings.yml") {
        return self::getConfig($file)->getAll();
    }

    public static function setValue(string $key, $value, string $file = "settings.yml") {
        $config = self::getConfig($file);
        $config->set($key, $value);
        $config->save();
    }

    public static function setAllValues(array $values, string $file = "settings.yml") {
        $config = self::getConfig($file);
        $config->setAll($values);
        $config->save();
    }

    public static function saveFile(string $filePath): void {
        Loader::getInstance()->saveResource(str_replace(["/"], [DIRECTORY_SEPARATOR], $filePath));
    }

    public static function getConfig(string $filePath = "settings.yml"): Config {
        return new Config(self::getPath($filePath));
    }

    public static function getPrefix(): string {
        return str_replace(["&"], ["§"], self::getValue("prefix") . " §r");
    }
}