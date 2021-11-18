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

namespace combofly\tasks;

use combofly\Loader;
use combofly\entity\JoinEntity;
use pocketmine\scheduler\Task;

class UpdateEntityTask extends Task {

    public function onRun(int $_) {
        foreach(Loader::getInstance()->getServer()->getLevels() as $level) {
            foreach($level->getEntities() as $entity) {
                if($entity instanceof JoinEntity)
                    $entity->update();
            }
        }
    }
}