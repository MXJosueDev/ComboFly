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
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase {
    use SingletonTrait;
    
    public function onLoad(): void {
        self::setInstance($this);        
    }

    public function onEnable(): void {
        ConfigManager::init();

        $this->getServer()->getCommandMap()->register("combofly", new ComboFlyCommand());

        (new Arena());
    }

    public function onDisable(): void {
        Arena::getInstance()->shutdown();
    }
}