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
use combofly\form\SpectatorForm;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\math\Vector2;

class EventListener implements Listener {

    private $cooldown = [];

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();

        $playerData = new PlayerData($player->getName());

        Arena::getInstance()->data[$player->getUniqueId()->toString()] = $playerData;  
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();

        if(Arena::getInstance()->isPlayer($player))
            Arena::getInstance()->quitPlayer($player, false);

        if(Arena::getInstance()->isSpectator($player))
            Arena::getInstance()->quitSpectator($player);

        unset(Arena::getInstance()->data[$player->getUniqueId()->toString()]);
    }

    public function onPlayerExhaust(PlayerExhaustEvent $event): void {
        $player = $event->getPlayer();

        if(!$player instanceof Player)
            return;

        if(Arena::getInstance()->isPlayer($player) || Arena::getInstance()->isSpectator($player)) {
            $player->setFood($player->getMaxFood());
        }
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event): void {
        $player = $event->getPlayer();

        if(Arena::getInstance()->isPlayer($player) || Arena::getInstance()->isSpectator($player)) {
            $event->setCancelled();
        }
    }
    
    public function onPlayerDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();

        if(Arena::getInstance()->isPlayer($player) || Arena::getInstance()->isSpectator($player)) {
            Arena::getInstance()->quitPlayer($player, false);
            Arena::getInstance()->quitSpectator($player);
        }
    }

    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();

        if(!ConfigManager::getValue("npc-rotation", "entities.yml"))
            return;

        $expandedBoundingBox = $player->getBoundingBox()->expandedCopy(15, 15, 15);
        
        foreach($player->getLevel()->getNearbyEntities($expandedBoundingBox, $player) as $entity) {
            if($entity instanceof JoinEntity) {
                $xdiff = $player->x - $entity->x;
                $zdiff = $player->z - $entity->z;
                $angle = atan2($zdiff, $xdiff);
                $yaw = (($angle * 180) / M_PI) - 90;
                $ydiff = $player->y - $entity->y;
                $v = new Vector2($entity->x, $entity->z);
                $dist = $v->distance($player->x, $player->z);
                $angle = atan2($dist, $ydiff);
                $pitch = (($angle * 180) / M_PI) - 90;
        
                $pk = new MovePlayerPacket();
                $pk->entityRuntimeId = $entity->getId();
                $pk->position = $entity->asVector3()->add(0, $entity->getEyeHeight(), 0);
                $pk->yaw = $yaw;
                $pk->pitch = $pitch;
                $pk->headYaw = $yaw;
                $pk->onGround = $entity->onGround;
                
                $player->batchDataPacket($pk);
            }
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();

        if(!Arena::getInstance()->isSpectator($player))
            return;
        
        if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK || $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR) {
            if(is_null($event->getItem()->getNamedTagEntry("spectator")))
                return;
            
            if(!$this->isCooldown($player)) {
                $this->setCooldown($player);
    
                (new SpectatorForm($player));
            }
        }
    }

    public function onEntityLevelChange(EntityLevelChangeEvent $event): void {
        $player = $event->getEntity();

        if(!$player instanceof Player)
            return;

        if(Arena::getInstance()->isPlayer($player) || Arena::getInstance()->isSpectator($player)) {
            if($event->getTarget()->getFolderName() !== ConfigManager::getValue("arena-level")) {
                Arena::getInstance()->quitPlayer($player, false);
                Arena::getInstance()->quitSpectator($player);
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void {
        $player = $event->getEntity();
        $cause = $event->getCause();

        if($player instanceof Player && Arena::getInstance()->isSpectator($player) && $cause === EntityDamageEvent::CAUSE_VOID) {
            $event->setCancelled();
            Arena::getInstance()->quitSpectator($player);
        }

        if(!$player instanceof Player || !Arena::getInstance()->isPlayer($player))
            return;

        switch($cause) {
            case EntityDamageEvent::CAUSE_FALL:
                $event->setCancelled();
                break;
            case EntityDamageEvent::CAUSE_VOID:
                $event->setCancelled();
                Arena::getInstance()->broadcast("§r§4{$player->getName()} §r§7was killed by the void.");
                Arena::getInstance()->quitPlayer($player);
                break;
        }

        if($event->isCancelled())
            return;

        if($player->getHealth() - $event->getFinalDamage() <= 0) {
            $event->setCancelled(true);

            $player->sendTitle("§l§cYou died!", "§7Good luck next time.");

            if($event instanceof EntityDamageByEntityEvent) {
                $damager = $event->getDamager();

                if(!$damager instanceof Player || !Arena::getInstance()->isPlayer($damager))
                    return;
                
                Utils::strikeLightning($player, $damager);
                $damager->batchDataPacket(Utils::addSound($damager, "random.pop"));

                Arena::getInstance()->addKill($damager, $player);

                Arena::getInstance()->broadcast("§r§4{$player->getName()} §r§7was killed by §r§c{$damager->getName()}");
                Arena::getInstance()->addSpectator($player);
            } else {
                Arena::getInstance()->broadcast("§r§4{$player->getName()} §r§7was die.");
                Arena::getInstance()->addSpectator($player);
            }

            return;
        }

        if($event instanceof EntityDamageByEntityEvent) {
            $event->setCancelled(false);
            $event->setKnockBack((int) ConfigManager::getValue("knockback"));
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();

        if(Arena::getInstance()->isPlayer($player) || Arena::getInstance()->isSpectator($player)) {
            $event->setCancelled();
        }
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();

        if(Arena::getInstance()->isPlayer($player) || Arena::getInstance()->isSpectator($player)) {
            $event->setCancelled();
        }
    }

    private function setCooldown(Player $player): void {
        $this->cooldown[$player->getUniqueId()->toString()] = time();
    }

    private function isCooldown(Player $player): bool {
        if(!isset($this->cooldown[$player->getUniqueId()->toString()]))
            return false;
        
        $cooldownTime = $this->cooldown[$player->getUniqueId()->toString()];

        if(time() - $cooldownTime < 2) {
            return true;
        }

        unset($this->cooldown[$player->getUniqueId()->toString()]);
        return false;
    }
}