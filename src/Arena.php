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

use combofly\tasks\ScoreboardTask;
use combofly\utils\ConfigManager;
use combofly\utils\Utils;
use combofly\entity\JoinEntity;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use pocketmine\player\GameMode;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\world\Position;
use pocketmine\utils\SingletonTrait;

class Arena {
    use SingletonTrait;

    const MESSAGE = "message";
    const TITLE = "title";
    const SUBTITLE = "subtitle";
    const TIP = "tip";
    const POPUP = "popup";

    private $economy;

    public $players = [];
    public $spectators = [];
    public $data = [];
    
    public function __construct() {
        self::setInstance($this);

        Loader::getInstance()->getServer()->getPluginManager()->registerEvents(new EventListener(), Loader::getInstance());
        
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ScoreboardTask(), (int) ConfigManager::getValue("scoreboard-update-interval", "scoreboard.yml") * 20);

        Entity::registerEntity(JoinEntity::class, true, ["ComboFlyJoinNPC", "combofly:join_npc"]);

        $this->loadArena();

        if(!is_null(Loader::getInstance()->getServer()->getPluginManager()->getPlugin("EconomyAPI"))) {
            $this->economy = EconomyAPI::getInstance();
        } else {
            Loader::getInstance()->getLogger()->alert("The EconomyAPI dependency was not found.");
        }
    }

    public function loadArena(): void {
        if(!ConfigManager::getValue("arena-world"))
            return;
    
        Loader::getInstance()->getServer()->getWorldManager()->loadWorld(ConfigManager::getValue("arena-world"));
    }

    /**
     * Set where players appear in the arena.
     *
     * @param  Player $player From this variable the position is obtained
     * @return void
     */
    public function setArena($player): void {
        if($player instanceof Player)
            return;
        $pos = $player->getPosition();

        ConfigManager::setValue("arena-world", $pos->getWorld()->getFolderName());
        ConfigManager::setValue("arena-pos", [
            "x" => $pos->getX(),
            "y" => $pos->getY(),
            "z" => $pos->getZ()
        ]);

        $this->loadArena();
    }

    public function loadLobby(): void {
        if(!ConfigManager::getValue("lobby-world"))
            return;
    
        Loader::getInstance()->getServer()->getWorldManager()->loadWorld(ConfigManager::getValue("lobby-world"));
    }
  
    /**
     * Set where players appear when exiting the arena.
     *
     * @param  Player $player From this variable the position is obtained
     * @return void
     */
    public function setLobby($player): void {
        if($player instanceof Player)
            return;
        $pos = $player->getPosition();

        ConfigManager::setValue("lobby-world", $pos->getWorld()->getFolderName());
        ConfigManager::setValue("lobby-pos", [
            "x" => $pos->getX(),
            "y" => $pos->getY(),
            "z" => $pos->getZ()
        ]);

        $this->loadLobby();
    }

    /**
     * Know if the arena is loaded.
     *
     * @return bool `true` if it is loaded and `false` if not
     */
    public function isArenaLoaded(): bool {
        if(!ConfigManager::getValue("arena-world"))
            return false;

        return Loader::getInstance()->getServer()->getWorldManager()->isWorldLoaded(ConfigManager::getValue("arena-world"));
    }

    /**
     * Know if the lobby is loaded.
     *
     * @return bool `true` if it is loaded and `false` if not
     */
    public function isLobbyLoaded(): bool {
        if(!ConfigManager::getValue("lobby-world"))
            return true;

        return Loader::getInstance()->getServer()->getWorldManager()->isWorldLoaded(ConfigManager::getValue("lobby-world"));
    }

    /**
     * Respawn a player if they are in spectator mode.
     *
     * @param  Player $player Player who is currently in spectator mode.
     */
    public function respawn(Player $player): void {
        if(!$this->isSpectator($player))
            return;
        
        unset($this->spectators[$player->getUniqueId()->toString()]);

        $this->addPlayer($player, true);
    }

    /**
     * Add a player to the arena.
     *
     * @param  Player $player  Player to be added
     * @param  bool   $respawn `true` if the player respawn (this is only used by @method void respawn())
     */ 
    public function addPlayer(Player $player, bool $respawn = false): void {
        if($this->isPlayer($player) || $this->isSpectator($player))
            return;
        
        $this->loadArena();

        if(!$this->isArenaLoaded()) {
            $player->sendMessage(ConfigManager::getPrefix() . "§cSorry, the arena is not enabled!");
            return;
        }

        $this->players[$player->getUniqueId()->toString()] = $player;
        
        Utils::resetPlayer($player);
        $this->giveKit($player);

        $player->setGamemode(GameMode::SURVIVAL());

        $world = Loader::getInstance()->getServer()->getWorldManager()->getWorldByName(ConfigManager::getValue("arena-world"));
        $vector = ConfigManager::getValue("arena-pos");
        $x = (float) $vector["x"];
        $y = (float) $vector["y"];
        $z = (float) $vector["z"];

        $player->teleport(new Position($x, $y, $z, $world));

        if(!$respawn)
            $this->broadcast("§c{$player->getName()} §r§7joined the arena!");
    }

    /**
     * Take a player out of the arena.
     *
     * @param  Player $player Player to be removed
     * @param  bool   $isDied If the player is dead
     */
    public function quitPlayer(Player $player, bool $isDied = true): void {
        if(!$this->isPlayer($player))
            return;

        Utils::resetPlayer($player);

        if(!ConfigManager::getValue("lobby-world")) {
            $player->teleport(Loader::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
        } else {
            $this->loadLobby();

            if(!$this->isLobbyLoaded()) {
                $player->teleport(Loader::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
            } else {
                $world = Loader::getInstance()->getServer()->getWorldManager()->getWorldByName(ConfigManager::getValue("lobby-world"));
                $vector = ConfigManager::getValue("lobby-pos");
                $x = (float) $vector["x"];
                $y = (float) $vector["y"];
                $z = (float) $vector["z"];
    
                $player->teleport(new Position($x, $y, $z, $world));
            }
        }

        if(!$isDied)
            $this->broadcast("§c{$player->getName()} §r§7left the arena!");

        unset($this->players[$player->getUniqueId()->toString()]);

        ScoreboardTask::getInstance()->getScoreboardAPI()->remove($player);
    }

    /**
     * Know if a player is in the arena.
     *
     * @return bool `true` if it is playing and `false` if not
     */ 
    public function isPlayer(Player $player): bool {
        return isset($this->players[$player->getUniqueId()->toString()]);
    }

    /**
     * Add a player to spectate the arena.
     *
     * @param  Player $player  Player to be added
     * @param  bool   $isDied `true` if the player was added as a spectator due to being killed (This is only used by events)
     */ 
    public function addSpectator(Player $player, bool $isDied = true): void {
        if($this->isSpectator($player) || $this->isPlayer($player))
            return;
        
        $this->loadArena();

        if(!$this->isArenaLoaded()) {
            $player->sendMessage(ConfigManager::getPrefix() . "§cSorry, the arena is not enabled!");

            if($isDied) {
                $this->quitPlayer($player, $isDied);
            }
            return;
        }

        if($isDied) {
            unset($this->players[$player->getUniqueId()->toString()]);
        }

        $this->spectators[$player->getUniqueId()->toString()] = $player;
        
        Utils::resetPlayer($player);

        $itemData = ConfigManager::getValue("spectator-item");
        $itemSlot = $itemData["slot"];
        $itemID = $itemData["id"];
        $itemMeta = $itemData["meta"];
        $itemName = str_replace(["&"], ["§"], $itemData["name"]);
        $itemLore = str_replace(["&"], ["§"], $itemData["lore"]);

        $item = Item::get($itemID, $itemMeta)->setCustomName($itemName)->setLore([$itemLore]);
        $item->getNamedTag()->setInt("spectator", 1);

        $player->getInventory()->setItem($itemSlot, $item);

        $player->setGamemode(GameMode::SPECTATOR());

        Utils::sendAdventureSettings($player);

        if(!$isDied) {
            $world = Loader::getInstance()->getServer()->getWorldManager()->getWorldByName(ConfigManager::getValue("arena-world"));
            $vector = ConfigManager::getValue("arena-pos");
            $x = (float) $vector["x"];
            $y = (float) $vector["y"];
            $z = (float) $vector["z"];

            $player->teleport(new Position($x, $y, $z, $world));

            $this->broadcast("§c{$player->getName()} §r§7joined the arena! (Spectator)");
        }
    }

    /**
     * Take a spectator out of the arena.
     *
     * @param  Player $player Player to be removed
     */
    public function quitSpectator(Player $player): void {
        if(!$this->isSpectator($player)) 
            return;

        Utils::resetPlayer($player);

        if(!ConfigManager::getValue("lobby-world")) {
            $player->teleport(Loader::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
        } else {
            $this->loadLobby();

            if(!$this->isLobbyLoaded()) {
                $player->teleport(Loader::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
            } else {
                $world = Loader::getInstance()->getServer()->getWorldManager()->getWorldByName(ConfigManager::getValue("lobby-world"));
                $vector = ConfigManager::getValue("lobby-pos");
                $x = (float) $vector["x"];
                $y = (float) $vector["y"];
                $z = (float) $vector["z"];
    
                $player->teleport(new Position($x, $y, $z, $world));
            }
        }

        $this->broadcast("§c{$player->getName()} §r§7left the arena! (Spectator)");

        unset($this->spectators[$player->getUniqueId()->toString()]);

        ScoreboardTask::getInstance()->getScoreboardAPI()->remove($player);
    }

    /**
     * Know if a player is spectating the arena.
     *
     * @return bool `true` if it is spectating and `false` if not
     */ 
    public function isSpectator(Player $player): bool {
        return isset($this->spectators[$player->getUniqueId()->toString()]);
    }

    /**
     * Get an array with the players that are currently playing.
     *
     * @return array<Player>
     */
    public function getPlayers(): array {
        $players = [];

        foreach($this->players as $uuid => $player) {
            $players[] = $player; 
        }

        return $players;
    }

    /**
     * Get an array with the players that are currently spectating.
     *
     * @return array<Player>
     */
    public function getSpectators(): array {
        $players = [];

        foreach($this->spectators as $uuid => $player) {
            $players[] = $player; 
        }

        return $players;
    }

    /**
     * Get an array with the players and spectators who are currently playing.
     *
     * @return array<Player>
     */
    public function getAllPlayers(): array {
        $players = [];

        foreach(array_merge($this->players, $this->spectators) as $uuid => $player) {
            $players[] = $player; 
        }

        return $players;
    }

    /**
     * Set the kit with which the players appear in the arena.
     *
     * @param  Player $player From this variable the inventory and the inventory of the armor is obtained
     */
    public function setKit(Player $player): void {
        $fileData = [];
        $file = ConfigManager::getConfig("kit.yml");

        $inventory = $player->getInventory()->getContents();
        $armorInventory = $player->getArmorInventory()->getContents();

        foreach($inventory as $slot => $item) {
            $fileData["inventory"][$slot] = $item->jsonSerialize();
        }

        foreach($armorInventory as $slot => $item) {
            $fileData["armorInventory"][$slot] = $item->jsonSerialize();
        }

        $fileData["slot"] = $player->getInventory()->getHeldItemIndex();

        $file->setAll($fileData);
        $file->save();
    } 

    /**
     * Place the kit configured for the arena on the player.
     *
     * @param  Player $player Player to place the kit
     */
    public function giveKit(Player $player): void {
        $inventory = [];
        $armorInventory = [];
        $slot = 0;

        $kit = ConfigManager::getConfig("kit.yml");
        $kitData = $kit->getAll();

        if($kitData === []) {
            $player->sendMessage(ConfigManager::getPrefix() . "§cPlease set the arena kit.");
            return;
        }

        if(isset($kitData["inventory"])) {
            foreach($kitData["inventory"] as $slot => $item) {
                $inventory[$slot] = Item::jsonDeserialize($item);
            }
        }

        if(isset($kitData["armorInventory"])) {
            foreach($kitData["armorInventory"] as $slot => $item) {
                $armorInventory[$slot] = Item::jsonDeserialize($item);
            }
        }

        if(isset($kitData["slot"]))
            $slot = $kitData["slot"];

        $player->getInventory()->setContents($inventory);
        $player->getArmorInventory()->setContents($armorInventory);
        $player->getInventory()->setHeldItemIndex($slot);
    }

    /**
     * Send a global message to all players and spectators in the arena.
     *
     * @param  string $text Message to send
     * @param         $type Type of message to send
     */
    public function broadcast(string $text, $type = self::MESSAGE): void {
        foreach($this->getAllPlayers() as $player) {
            switch($type) {
                case self::MESSAGE:
                    $player->sendMessage(ConfigManager::getPrefix() . $text);
                    break;
                case self::TITLE:
                    $player->sendTitle($text);
                    break;
                case self::SUBTITLE:
                    $player->sendSubTitle($text);
                    break;
                case self::TIP:
                    $player->sendTip($text);
                    break;
                case self::POPUP:
                    $player->sendPopup($text);
                    break;
            }
        }
    }

    public function getEconomy(): ?EconomyAPI {
        return $this->economy;
    }

    /**
     * Get the PlayerData class that contains the basic data of the player.
     *
     * @see PlayerData
     * @param  Player          $player Player to get data
     * @return PlayerData|null
     */
    public function getPlayerData(Player $player): ?PlayerData {
        if(!isset($this->data[$player->getUniqueId()->toString()])) {
            throw new \Exception("Player data was not found.");
        }

        return $this->data[$player->getUniqueId()->toString()];
    }

    /**
     * Add a kill to a player.
     *
     * @param  Player $killer Player to add the kill
     * @param  Player $died   Player who was killed and to which a death is added.
     */
    public function addKill(Player $killer, Player $died): void {
        $moneyReward = (int) ConfigManager::getValue("money-reward");
        $economy = $this->getEconomy();

        if(!is_null($economy) && $moneyReward > 0) {
            $economy->addMoney($killer, $moneyReward);
            $killer->sendPopup("§r§6+{$moneyReward} coins!");
        }

        $killerData = $this->getPlayerData($killer);
        
        $killerData->set("kills", $killerData->get("kills") + 1);
        $this->addDeath($died);
    }

    /**
     * Add a death to a player.
     *
     * @param  Player $died Player to which death is added.
     */
    public function addDeath(Player $died): void {
        $diedData = $this->getPlayerData($died);

        $diedData->set("deaths", $diedData->get("deaths") + 1);
    }

    /**
     * Obtain the kills of a player.
     *
     * @param  Player $player
     * @return int
     */
    public function getKills(Player $player): int {
        return (int) $this->getPlayerData($player)->get("kills");
    }

    /**
     * Obtain the deaths of a player.
     *
     * @param  Player $player
     * @return int
     */
    public function getDeaths(Player $player): int {
        return (int) $this->getPlayerData($player)->get("deaths");
    }

    public function shutdown(): void {
        foreach($this->data as $uuid => $data) {
            $data->save();
            unset($this->data[$uuid]);
        }

        foreach($this->getAllPlayers() as $player) {
            $this->quitPlayer($player);
            $this->quitSpectator($player);
        }
    }
}
