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

use combofly\utils\ConfigManager;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;

class EventListener implements Listener {

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();

        if(!$player->isAuthenticated()) {
            $player->kick("%disconnectionScreen.notAuthenticated", false);
            return;
        }

        $playerData = new PlayerData($player);

        Arena::getInstance()->data[$player->getXuid()] = $playerData;  
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();

        if(Arena::getInstance()->isPlayer($player))
            Arena::getInstance()->quitPlayer($player, false);

        unset(Arena::getInstance()->data[$player->getXuid()]);
    }

    public function onPlayerExhaust(PlayerExhaustEvent $event): void {
        $player = $event->getPlayer();

        if(!Arena::getInstance()->isPlayer($player))
            return;

        $player->setFood($player->getMaxFood());
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event): void {
        $player = $event->getPlayer();

        if(!Arena::getInstance()->isPlayer($player))
            return;

        $event->setCancelled();
    }
    
    public function onPlayerDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();

        if(!Arena::getInstance()->isPlayer($player))
            return;

        Arena::getInstance()->quitPlayer($player, false);
    }

    public function onEntityLevelChange(EntityLevelChangeEvent $event): void {
        $player = $event->getEntity();

        if(!$player instanceof Player || !Arena::getInstance()->isPlayer($player))
            return;

        if($event->getTarget()->getFolderName() !== ConfigManager::getValue("arena-level")) {
            Arena::getInstance()->quitPlayer($player, false);
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void {
        // TODO:
        $player = $event->getEntity();

        if(!$player instanceof Player || !Arena::getInstance()->isPlayer($player))
            return;
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void {
        // TODO:
        $player = $event->getEntity();

        if(!$player instanceof Player || !Arena::getInstance()->isPlayer($player))
            return;
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();

        if(!Arena::getInstance()->isPlayer($player))
            return;

        $event->setCancelled();
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();

        if(!Arena::getInstance()->isPlayer($player))
            return;

        $event->setCancelled();
    }
}