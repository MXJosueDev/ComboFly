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

namespace combofly\utils;

use combofly\Loader;
use pocketmine\player\Player;
use pocketmine\player\GameMode;
use pocketmine\math\Vector3;
use pocketmine\world\Location;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Utils {

    public static function resetPlayer(Player $player): void {
        $player->setSprinting(false);
        $player->setSneaking(false);

        $player->extinguish();

        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();

        $player->getEffects()->clear();
        $player->setHealth($player->getMaxHealth());    

        $player->setAllowFlight(false);
        $player->setFlying(false);
               
        $player->setGamemode(GameMode::SURVIVAL());
    }

    public static function addSound(Vector3 $vector, string $sound, ?array $players = null): PlaySoundPacket {
        $pk = PlaySoundPacket::create(
            $sound,
            $vector->getX(),
            $vector->getY(),
            $vector->getZ(),
            1,
            1
        );

        if(!is_null($players))
            Loader::getInstance()->getServer()->broadcastPackets($players, [$pk]);

        return $pk;
    } 

    public static function strikeLightning(Location $location, Player $killer): void {
        $level = $location->getWorld();

        $light = AddActorPacket::create(
            0,
            Entity::nextRuntimeId(),
            EntityIds::LIGHTNING_BOLT,
            $location->asPosition(),
            null,
            $location->getPitch(),
            $location->getYaw(),
            0.0,
            [],
            [],
            []
        );

        Loader::getInstance()->getServer()->broadcastPackets($level->getPlayers(), [$light]);
		
        $sound = self::addSound($location, "ambient.weather.thunder");

        foreach($level->getPlayers() as $player) {
            if($player->getUniqueId()->toString() !== $killer->getUniqueId()->toString())
                $player->getNetworkSession()->sendDataPacket($sound);
        }
    } 

    // Source from: https://github.com/larryTheCoder/SkyWarsForPE/blob/11d3f734a9c716a968c5747aaf5a427d960de97f/src/larryTheCoder/arena/api/Arena.php#L496
    public static function sendAdventureSettings(Player $player): void {
        $player->setAllowFlight(true);

        $pk = AdventureSettingsPacket::create(
            0,  
            ($player->getServer()->isOp($player->getName()) ? AdventureSettingsPacket::PERMISSION_OPERATOR : AdventureSettingsPacket::PERMISSION_NORMAL),
            -1,
            ($player->getServer()->isOp($player->getName()) ? PlayerPermissions::OPERATOR : PlayerPermissions::MEMBER),
            0,
            $player->getId()
        );

        $pk->setFlag(AdventureSettingsPacket::WORLD_IMMUTABLE, true);
        $pk->setFlag(AdventureSettingsPacket::NO_PVP, true);
        $pk->setFlag(AdventureSettingsPacket::AUTO_JUMP, $player->hasAutoJump());
        $pk->setFlag(AdventureSettingsPacket::ALLOW_FLIGHT, $player->getAllowFlight());
        $pk->setFlag(AdventureSettingsPacket::NO_CLIP, false);
        $pk->setFlag(AdventureSettingsPacket::FLYING, $player->isFlying());

        $player->getNetworkSession()->sendDataPacket($pk);
    }
}