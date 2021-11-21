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

namespace combofly\entity;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;

class EntityManager {

    public static function setJoinNPC(Player $player): void {
        $player->getLevel()->loadChunk((int) $player->getX(), (int) $player->getZ());

        $nbt = Entity::createBaseNBT(new Vector3($player->getX(), $player->getY(), $player->getZ()));
        $nbt->setTag(clone $player->namedtag->getCompoundTag("Skin"));
        
        $human = new JoinEntity($player->getLevel(), $nbt);
        $human->setNameTag("");
        $human->setNameTagVisible(true);
        $human->setNameTagAlwaysVisible(true);
        $human->yaw = $player->getYaw();
        $human->pitch = $player->getPitch();
        
        $human->spawnToAll();
    }
}