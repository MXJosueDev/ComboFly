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

namespace combofly\entity;

use combofly\Arena;
use combofly\form\JoinForm;
use combofly\utils\ConfigManager;
use pocketmine\player\Player;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\entity\Human;

class JoinEntity extends Human {

    private $cooldown = [];

    protected function entityBaseTick(int $tickDiff = 1): bool 
    {
        if($this->closed) {
        	return false;
        }
        
        $hasUpdate = parent::entityBaseTick($tickDiff);
        
        if($this->ticksLived % 20 === 0) {
            $this->setNameTag($this->getNameTagCustom());
            $this->setNameTagVisible(true);
            $this->setNameTagAlwaysVisible(true);
            $this->setScale(1);
            $this->setImmobile(true);
        }

        $every = (float) ConfigManager::getValue("npc-rotation-rate", "entities.yml") * 20;

        // Rotation
        if($this->ticksLived % $every === 0) {
            if(!ConfigManager::getValue("npc-rotation", "entities.yml")) return;

            $expandedBoundingBox = $this->getBoundingBox()->expandedCopy(15, 15, 15);
        
            foreach($this->getWorld()->getNearbyEntities($expandedBoundingBox, $this) as $player) {
                if($player instanceof Player) {
                    // Pitch and Yaw calculations
                    $xdiff = $player->getPosition()->x - $this->getPosition()->x;
                    $zdiff = $player->getPosition()->z - $this->getPosition()->z;
                    $angle = atan2($zdiff, $xdiff);
                    $yaw = (($angle * 180) / M_PI) - 90;
                    $ydiff = $player->getPosition()->y - $this->getPosition()->y;
                    $v = new Vector2($this->getPosition()->x, $this->getPosition()->z);
                    $dist = $v->distance(new Vector2($player->getPosition()->x, $player->getPosition()->z));
                    $angle = atan2($dist, $ydiff);
                    $pitch = (($angle * 180) / M_PI) - 90;
            
                    // Package Creation
                    $pk = MovePlayerPacket::create(
                        $this->getId(),
                        $this->getPosition()->asVector3()->add(0, $this->getEyeHeight(), 0),
                        $pitch,
                        $yaw,
                        $headYaw,
                        MovePlayerPacket::NORMAL,
                        $this->onGround,
                        0,
                        0,
                        0,
                        0
                    );
                    
                    $player->getNetworkSession()->sendDataPacket($pk);
                }
            }
        }
        
        return $hasUpdate;
    }

    public function getNameTagCustom(): string 
    {
        $replace = [
            "{playing}"       => count(Arena::getInstance()->getPlayers()),
            "{spectating}"    => count(Arena::getInstance()->getSpectators()),
            "{total_players}" => count(Arena::getInstance()->getAllPlayers()),
            "{arena_status}"  => Arena::getInstance()->isArenaLoaded() ? "§aOnline" : "§cOffline",
            "{line}"          => "\n§r",
            "&"               => "§"
        ];

        return str_replace(array_keys($replace), array_values($replace), ConfigManager::getValue("join-npc-nametag", "entities.yml"));
    }

    public function attack(EntityDamageEvent $source): void 
    {
        switch($source->getCause()) {
            case EntityDamageEvent::CAUSE_VOID:
                parent::attack($source);
                break;
            case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                if(!$source instanceof EntityDamageByEntityEvent)
                    return;
                
                $player = $source->getDamager();

                if($player instanceof Player && !$this->isCooldown($player) && !EntityManager::isRemoveEntity($player)) {
                    $this->setCooldown($player);

                    (new JoinForm($player));
                }

                if(!$player instanceof Player || !EntityManager::isRemoveEntity($player))
                    return;

                $commandTime = EntityManager::$remove[$player->getUniqueId()->toString()];
                
                if(time() - $commandTime > (60 * 3)) {
                    EntityManager::unsetRemoveEntity($player);
                    return;
                }

                $this->flagForDespawn();
                EntityManager::unsetRemoveEntity($player);

                $player->sendMessage(ConfigManager::getPrefix() . "§aThe NPC removed successfully.");
                break;
        }
    }

    private function setCooldown(Player $player): void 
    {
        $this->cooldown[$player->getUniqueId()->toString()] = time();
    }

    private function isCooldown(Player $player): bool 
    {
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