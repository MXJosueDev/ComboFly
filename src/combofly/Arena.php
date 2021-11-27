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
use combofly\tasks\async\SetKitTask;
use combofly\utils\ConfigManager;
use combofly\utils\Utils;
use combofly\entity\JoinEntity;
use onebone\economyapi\EconomyAPI;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\Position;
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
        
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ScoreboardTask(), (int) ConfigManager::getValue("scoreboard-update-interval", 1, "scoreboard.yml") * 20);

        Entity::registerEntity(JoinEntity::class, true, ["ComboFlyJoinNPC", "combofly:join_npc"]);

        $this->loadArena();

        if(!is_null(Loader::getInstance()->getServer()->getPluginManager()->getPlugin("EconomyAPI"))) {
            $this->economy = EconomyAPI::getInstance();
        } else {
            Loader::getInstance()->getLogger()->alert("The EconomyAPI dependency was not found.");
        }
    }

    public function loadArena(): void {
        if(!ConfigManager::getValue("arena-level", false))
            return;
    
        Loader::getInstance()->getServer()->loadLevel(ConfigManager::getValue("arena-level"));
    }

    public function setArena(Position $pos): void {
        ConfigManager::setValue("arena-level", $pos->getLevel()->getFolderName());
        ConfigManager::setValue("arena-pos", [
            "x" => $pos->getX(),
            "y" => $pos->getY(),
            "z" => $pos->getZ()
        ]);

        $this->loadArena();
    }

    public function loadLobby(): void {
        if(!ConfigManager::getValue("lobby-level", false))
            return;
    
        Loader::getInstance()->getServer()->loadLevel(ConfigManager::getValue("lobby-level", false));
    }
    
    public function setLobby(Position $pos): void {
        ConfigManager::setValue("lobby-level", $pos->getLevel()->getFolderName());
        ConfigManager::setValue("lobby-pos", [
            "x" => $pos->getX(),
            "y" => $pos->getY(),
            "z" => $pos->getZ()
        ]);

        $this->loadLobby();
    }

    public function isArenaLoaded(): bool {
        if(!ConfigManager::getValue("arena-level", false))
            return false;

        return Loader::getInstance()->getServer()->isLevelLoaded(ConfigManager::getValue("arena-level", false));
    }

    public function isLobbyLoaded(): bool {
        if(!ConfigManager::getValue("lobby-level", false))
            return true;

        return Loader::getInstance()->getServer()->isLevelLoaded(ConfigManager::getValue("lobby-level", false));
    }

    public function respawn(Player $player): void {
        if(!$this->isSpectator($player))
            return;
        
        unset($this->spectators[$player->getUniqueId()->toString()]);

        $this->addPlayer($player, true);
    }

    public function addPlayer(Player $player, bool $respawn = false): void {
        if($this->isPlayer($player))
            return;
        
        $this->loadArena();

        if(!$this->isArenaLoaded()) {
            $player->sendMessage(ConfigManager::getPrefix() . "§cSorry, the arena is not enabled!");
            return;
        }

        $this->players[$player->getUniqueId()->toString()] = $player;
        
        Utils::resetPlayer($player);
        $this->giveKit($player);

        $player->setGamemode(Player::SURVIVAL);

        $level = Loader::getInstance()->getServer()->getLevelByName(ConfigManager::getValue("arena-level", false));
        $vector = ConfigManager::getValue("arena-pos", ["x" => 0, "y" => 0, "z" => 0]);
        $x = (float) $vector["x"];
        $y = (float) $vector["y"];
        $z = (float) $vector["z"];

        $player->teleport(new Position($x, $y, $z, $level));

        if(!$respawn)
            $this->broadcast("§c{$player->getName()} §r§7joined the arena!");
    }

    public function quitPlayer(Player $player, bool $isDied = true): void {
        if(!$this->isPlayer($player))
            return;

        Utils::resetPlayer($player);

        if(!ConfigManager::getValue("lobby-level", false)) {
            $player->teleport(Loader::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
        } else {
            $this->loadLobby();

            if(!$this->isLobbyLoaded()) {
                $player->teleport(Loader::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
            } else {
                $level = Loader::getInstance()->getServer()->getLevelByName(ConfigManager::getValue("lobby-level", false));
                $vector = ConfigManager::getValue("lobby-pos", ["x" => 0, "y" => 0, "z" => 0]);
                $x = (float) $vector["x"];
                $y = (float) $vector["y"];
                $z = (float) $vector["z"];
    
                $player->teleport(new Position($x, $y, $z, $level));
            }
        }

        unset($this->players[$player->getUniqueId()->toString()]);

        ScoreboardTask::getInstance()->getScoreboardAPI()->remove($player);

        if(!$isDied)
            $this->broadcast("§c{$player->getName()} §r§7left the arena!");
    }

    public function isPlayer(Player $player): bool {
        return isset($this->players[$player->getUniqueId()->toString()]);
    }

    public function addSpectator(Player $player, bool $isDied = true): void {
        if($this->isSpectator($player)) return;
        
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

        $itemData = ConfigManager::getValue("spectator-item", ["slot" => 4, "id" => 345, "meta" => 0, "name" => "&r&l&cNavigator", "lore" => "Right click to open the menu."]);
        $itemSlot = $itemData["slot"];
        $itemID = $itemData["id"];
        $itemMeta = $itemData["meta"];
        $itemName = str_replace(["&"], ["§"], $itemData["name"]);
        $itemLore = str_replace(["&"], ["§"], $itemData["lore"]);

        $item = Item::get($itemID, $itemMeta)->setCustomName($itemName)->setLore([$itemLore]);
        $item->getNamedTag()->setInt("spectator", 1);

        $player->getInventory()->setItem($itemSlot, $item);

        $player->setGamemode(Player::SPECTATOR);

        Utils::sendAdventureSettings($player);

        if(!$isDied) {
            $level = Loader::getInstance()->getServer()->getLevelByName(ConfigManager::getValue("arena-level", false));
            $vector = ConfigManager::getValue("arena-pos", ["x" => 0, "y" => 0, "z" => 0]);
            $x = (float) $vector["x"];
            $y = (float) $vector["y"];
            $z = (float) $vector["z"];

            $player->teleport(new Position($x, $y, $z, $level));

            $this->broadcast("§c{$player->getName()} §r§7joined the arena! (Spectator)");
        }
    }

    public function quitSpectator(Player $player): void {
        if(!$this->isSpectator($player))
            return;

        Utils::resetPlayer($player);

        if(!ConfigManager::getValue("lobby-level", false)) {
            $player->teleport(Loader::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
        } else {
            $this->loadLobby();

            if(!$this->isLobbyLoaded()) {
                $player->teleport(Loader::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
            } else {
                $level = Loader::getInstance()->getServer()->getLevelByName(ConfigManager::getValue("lobby-level", false));
                $vector = ConfigManager::getValue("lobby-pos", ["x" => 0, "y" => 0, "z" => 0]);
                $x = (float) $vector["x"];
                $y = (float) $vector["y"];
                $z = (float) $vector["z"];
    
                $player->teleport(new Position($x, $y, $z, $level));
            }
        }

        unset($this->spectators[$player->getUniqueId()->toString()]);

        ScoreboardTask::getInstance()->getScoreboardAPI()->remove($player);

        $this->broadcast("§c{$player->getName()} §r§7left the arena! (Spectator)");
    }

    public function isSpectator(Player $player): bool {
        return isset($this->spectators[$player->getUniqueId()->toString()]);
    }

    public function getPlayers(): array {
        $players = [];

        foreach($this->players as $uuid => $player) {
            $players[] = $player; 
        }

        return $players;
    }

    public function getSpectators(): array {
        $players = [];

        foreach($this->spectators as $uuid => $player) {
            $players[] = $player; 
        }

        return $players;
    }

    public function getAllPlayers(): array {
        $players = [];

        foreach(array_merge($this->players, $this->spectators) as $uuid => $player) {
            $players[] = $player; 
        }

        return $players;
    }

    public function setKit(Player $player): void {
        // Loader::getInstance()->getServer()->getAsyncPool()->submitTask(new SetKitTask($player->getInventory(), $player->getArmorInventory(), strtolower($player->getName())));
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
        $player->getArmorInventory()->setContents($inventory);
        $player->getInventory()->setHeldItemIndex($slot);
    }

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

    public function getPlayerData(Player $player): ?PlayerData {
        if(!isset($this->data[$player->getUniqueId()->toString()])) {
            throw new \Exception("Player data was not found.");
        }

        return $this->data[$player->getUniqueId()->toString()];
    }

    public function addKill(Player $killer, Player $died): void {
        $moneyReward = (int) ConfigManager::getValue("money-reward", 20);
        $economy = $this->getEconomy();

        if(!is_null($economy) && $moneyReward > 0) {
            $economy->addMoney($killer, $moneyReward);
            $killer->sendPopup("§r§6+{$moneyReward} coins!");
        }

        $killerData = $this->getPlayerData($killer);
        
        $killerData->set("kills", $killerData->get("kills") + 1);
        $this->addDeath($died);
    }

    public function addDeath(Player $died): void {
        $diedData = $this->getPlayerData($died);

        $diedData->set("deaths", $diedData->get("deaths") + 1);
    }

    public function getKills(Player $player): int {
        return (int) $this->getPlayerData($player)->get("kills");
    }

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
