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

use pocketmine\entity\Entity;

class EntityManager {

    public function setJoinNPC(Player $player): void {
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

    public function removeJoinNPC(JoinEntity $entity): void {
        $entity->close();
    }
}