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
        self::saveFile("scoreboard.yml");
        self::saveFile("entities.yml");
        self::saveFile("kit.yml");
        self::saveFile("menus.yml");
    }

    public static function getDefault(string $key, string $file): string {
        $defaults = [];

        switch($file) {
            case "settings.yml":
                $defaults = [
                    'prefix' => '&l&f[&r&bCombo&3Fly&r&l&f]',
                    'provider' => 'json',
                    'money-reward' => 20,
                    'knockback' => 0.25,
                    'arena-level' => false,
                    'arena-pos' => [
                      'x' => 0,
                      'y' => 0,
                      'z' => 0,
                    ],
                    'spectator-item' => [
                      'slot' => 4,
                      'meta' => 0,
                      'id' => 345,
                      'name' => '&r&l&cNavigator',
                      'lore' => 'Right click to open the menu.',
                    ],
                    'lobby-level' => false,
                    'lobby-pos' => [
                      'x' => 0,
                      'y' => 0,
                      'z' => 0,
                    ],
                  ];
                break;
            case "entities.yml":
                $defaults = [
                    'join-npc-nametag' => '&l&bCombo&3Fly{line}&fStatus&7: {arena_status}{line}&fPlaying&7: &c{playing}{line}&fSpectating&7: &c{spectating}{line}&eClick to join!',
                    'npc-rotation' => true,
                ];
                break;
            case "kit.yml":
                $defaults = [
                    'inventory' => [
                      0 => [
                        'id' => 276,
                        'nbt_b64' => 'CgAACgcAZGlzcGxheQgEAE5hbWUdAMKnbMKnYkNvbWJvwqczRmx5IMKncsKnN1N3b3JkAAA=',
                      ],
                      1 => [
                        'id' => 368,
                        'count' => 16,
                      ],
                      2 => [
                        'id' => 466,
                        'count' => 16,
                      ],
                      3 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      4 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      5 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      6 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      7 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      8 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      12 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      13 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      14 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      15 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      16 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      17 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      21 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      22 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      23 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      24 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      25 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      26 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      30 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      31 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      32 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      33 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      34 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                      35 => [
                        'id' => 373,
                        'damage' => 16,
                      ],
                    ],
                    'armorInventory' => [
                      0 => [
                        'id' => 310,
                        'nbt_b64' => 'CgAACgcAZGlzcGxheQgEAE5hbWUeAMKnbMKnYkNvbWJvwqczRmx5IMKncsKnN0hlbG1ldAAA',
                      ],
                      1 => [
                        'id' => 311,
                        'nbt_b64' => 'CgAACgcAZGlzcGxheQgEAE5hbWUiAMKnbMKnYkNvbWJvwqczRmx5IMKncsKnN0NoZXN0cGxhdGUAAA==',
                      ],
                      2 => [
                        'id' => 312,
                        'nbt_b64' => 'CgAACgcAZGlzcGxheQgEAE5hbWUgAMKnbMKnYkNvbWJvwqczRmx5IMKncsKnN0xlZ2dpbmdzAAA=',
                      ],
                      3 => [
                        'id' => 313,
                        'nbt_b64' => 'CgAACgcAZGlzcGxheQgEAE5hbWUdAMKnbMKnYkNvbWJvwqczRmx5IMKncsKnN0Jvb3RzAAA=',
                      ],
                    ],
                    'slot' => 0,
                ];
                break;
            case "menus.yml":
                $defaults = [
                    'join-menu' => [
                      'title' => '&l&bCombo&3Fly',
                      'content' => '&7How do you want to join the arena?',
                      'buttons' => [
                        'player' => '&cPlayer',
                        'spectator' => '&cSpectator',
                      ],
                    ],
                    'spectator-menu' => [
                      'title' => '&l&bSpectator',
                      'content' => '&7You want to do?',
                      'buttons' => [
                        'continue' => '&cContinue Spectating',
                        'respawn' => '&cRespawn',
                        'go-to-lobby' => '&cGo to lobby',
                      ],
                    ],
                ];
                break;
            case "scoreboard.yml":
                $defaults = [
                    'scoreboard-update-interval' => 1,
                    'scoreboard-title' => '&l&bCombo&3Fly',
                    'scoreboard-lines' => [
                      0 => '',
                      1 => '&fPing&7: &c{player_ping}',
                      2 => '',
                      3 => '&fKills&7: &c{player_kills}',
                      4 => '&fDeaths&7: &c{player_deaths}',
                      5 => '',
                      6 => '&fPlayers&7: &c{playing}',
                      7 => '&fSpectators&7: &c{spectating}',
                    ],
                    'scoreboard-lines-spectator' => [
                      0 => '&cYou are dead!',
                      1 => '',
                      2 => '&fPing&7: &c{player_ping}',
                      3 => '',
                      4 => '&fKills&7: &c{player_kills}',
                      5 => '&fDeaths&7: &c{player_deaths}',
                      6 => '',
                      7 => '&fPlayers&7: &c{playing}',
                      8 => '&fSpectators&7: &c{spectating}',
                    ],
                ];
                break;
        }
 
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
        return self::getConfig($file)->get($key, self::getDefault($key, $file));
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
        return str_replace(["&"], ["§"], self::getValue("prefix") . " §r");
    }
}