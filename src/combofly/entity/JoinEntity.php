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

use pocketmine\entity\Human;

class JoinEntity extends Human {

    public function update(): void {
        $entity->setNameTag($this::getNameTagCustom());
        $entity->setNameTagAlwaysVisible(true);
        $entity->setScale(1);
    }

    public function getNameTagCustom(): string {
        // TODO
    } 
}