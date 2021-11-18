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

use combofly\utils\Utils;
use combofly\utils\ConfigManager;
use combofly\entity\JoinEntity;
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

    public static $remove = [];

    public static function setRemoveEntity(Player $player): void {
        self::$remove[$player->getUniqueId()->toString()] = $player;
    }

    public static function unsetRemoveEntity(Player $player): void {
        if(self::isRemoveEntity($player))
            unset(self::$remove[$player->getUniqueId()->toString()]);
    }

    public static function isRemoveEntity(Player $player): bool {
        return isset(self::$remove[$player->getUniqueId()->toString()]);
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();

        $playerData = new PlayerData($player);

        Arena::getInstance()->data[$player->getUniqueId()->toString()] = $playerData;  
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();

        if(Arena::getInstance()->isPlayer($player))
            Arena::getInstance()->quitPlayer($player, false);

        unset(Arena::getInstance()->data[$player->getUniqueId()->toString()]);
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

        if($event->getTarget()->getFolderName() !== ConfigManager::getValue("arena-level", false)) {
            Arena::getInstance()->quitPlayer($player, false);
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void {
        $player = $event->getEntity();
        $cause = $event->getCause();

        if(!$player instanceof Player || !Arena::getInstance()->isPlayer($player))
            return;

        switch($cause) {
            case EntityDamageEvent::CAUSE_FALL:
                $event->setCancelled();
                break;
            case EntityDamageEvent::CAUSE_VOID:
                $event->setCancelled();
                Arena::getInstance()->quitPlayer($player);
                Arena::getInstance()->broadcast("§r§4{$player->getName()} §r§7was killed by the void.");
                break;
        }

        if(!$event instanceof EntityDamageByEntityEvent)
            return;
        $damager = $event->getDamager();

        if(!$damager instanceof Player || !Arena::getInstance()->isPlayer($damager))
            return;

        $event->setCancelled(false);
        $event->setKnockBack((int) ConfigManager::getValue("knockback", 0.25));

        if($player->getHealth() - $event->getFinalDamage() <= 0) {
            Arena::getInstance()->quitPlayer($player);
            Arena::getInstance()->broadcast("§r§4{$player->getName()} §r§7was killed by §r§c{$damager->getName()}");

            Arena::getInstance()->addKill($damager, $player);

            Utils::strikeLightning($player, $damager);
            $damager->dataPacket(Utils::addSound($damager, "random.pop"));
            
            $player->sendTitle("§l§cYou died!", "§7Good luck next time.");
        }
    }

    public function onEntityRemove(EntityDamageByEntityEvent $event): void {
        $entity = $event->getEntity();
        $player = $event->getDamager();
        
        if(!$player instanceof Player || !self::isRemoveEntity($player))
            return;

        if($entity instanceof JoinEntity) {
            $entity->close();
            $this->unsetRemoveEntity($player);
            $player->sendMessage(ConfigManager::getPrefix() . "§aThe NPC removed successfully.");
        }
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