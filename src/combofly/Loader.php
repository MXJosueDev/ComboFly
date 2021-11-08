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

use combofly\commands\CommandManager;
use combofly\utils\ConfigManager;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase {
    use SingletonTrait;

    private $economy = null;
    
    public function onLoad() {
        self::setInstance($this);        
    }

    public function onEnable() {
        ConfigManager::saveAll();
        CommandManager::registerAll();
        (new Arena());
    }
}