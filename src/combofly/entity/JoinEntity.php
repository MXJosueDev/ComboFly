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

use combofly\Arena;
use combofly\utils\ConfigManager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Human;

class JoinEntity extends Human {

    public function update(): void {
        $this->setNameTag($this->getNameTagCustom());
        $this->setNameTagAlwaysVisible(true);
        $this->setScale(1);
        $this->setImmobile(true);
    }

    public function getNameTagCustom(): string {
        $replace = [
            "{playing}"      => count(Arena::getInstance()->getAllPlayers()),
            "{arena_status}" => Arena::getInstance()->isArenaLoaded() ? "§aOnline" : "§cOffline",
            "{line}"         => "\n",
            "&"              => "§"
        ];

        return str_replace(array_keys($replace), array_values($replace), ConfigManager::getValue("join-npc-nametag", "&l&bCombo&3Fly{line}&r&fStatus&7: {arena_status}{line}&r&fPlaying&7: &c{playing}"));
    }

    public function attack(EntityDamageEvent $source): void {
        if($source->getCause() == EntityDamageEvent::CAUSE_FALL) {
            $this->close();
            return;
        }
        
        $source->setCancelled();
        parent::attack();
    }
}