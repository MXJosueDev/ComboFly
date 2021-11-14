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
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class Utils {

    public static function resetPlayer(Player $player): void {
        $player->setSprinting(false);
        $player->setSneaking(false);

        $player->extinguish();

        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();

        $player->removeAllEffects();
        $player->setHealth($player->getMaxHealth()) ;    
        
        $player->setXpAndProgress(0, 0);

        $player->setAllowFlight(false);
        $player->setFlying(false);
               
        $player->setGamemode(Player::SURVIVAL);
    }

    public static function addSound(string $sound, ?array $players = null) {
        // TODO
        $sound = new PlaySoundPacket();
		$sound->soundName = $sound;
		$sound->x = $p->getX();
		$sound->y = $p->getY();
		$sound->z = $p->getZ();
		$sound->volume = 1;
		$sound->pitch = 1;

		Loader::getServer()->broadcastPacket($level->getPlayers(), $sound);  
    } 

    public static function strikeLightning(Player $position): void{
		$level = $p->getLevel();

		$light = new AddActorPacket();
		$light->metadata = [];

		$light->type = AddActorPacket::LEGACY_ID_MAP_BC[93];
		$light->entityRuntimeId = Entity::$entityCount++;
		$light->entityUniqueId = 0;

		$light->position = $p->getPosition();
		$light->motion = new Vector3();

		$light->yaw = $p->getYaw();
		$light->pitch = $p->getPitch();

		Loader::getServer()->broadcastPacket($level->getPlayers(), $light);
		
		$sound = new PlaySoundPacket();
		$sound->soundName = "ambient.weather.thunder";
		$sound->x = $p->getX();
		$sound->y = $p->getY();
		$sound->z = $p->getZ();
		$sound->volume = 1;
		$sound->pitch = 1;
		Loader::getServer()->broadcastPacket($level->getPlayers(), $sound);
	}
}