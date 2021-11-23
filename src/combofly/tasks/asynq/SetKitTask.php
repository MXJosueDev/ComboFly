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

namespace combofly\tasks\asynq;

use combofly\Loader;
use combofly\utils\ConfigManager;
use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\ArmorInventory;

class SetKitTask extends AsyncTask {

    protected $inventory;
    protected $armorInventory;
    protected $player;

    public function __construct(PlayerInventory $inventory, ArmorInventory $armorInventory, string $player) {
        $this->inventory = $inventory;
        $this->armorInventory = $armorInventory;
        $this->player = $player;
    }

    public function onRun() {
        $fileData = [];
        $file = ConfigManager::getConfig("kit.yml");

        $inventory = $this->getInventory()->getContents();
        $armorInventory = $this->getArmorInventory()->getContents();

        foreach($inventory as $slot => $item) {
            $fileData["inventory"][$slot] = $item->jsonSerialize();
        }

        foreach($armorInventory as $slot => $item) {
            $fileData["armorInventory"][$slot] = $item->jsonSerialize();
        }

        $fileData["slot"] = $this->getInventory()->getHeldItemIndex();

        $file->setAll($fileData);
        $file->save();
    }

    public function onCompletion(Server $server) {
        $player = $server->getPlayerExact($this->player);

        if(!is_null($player)) {
            $player->sendMessage(ConfigManager::getPrefix() . "§aThe kit was set up correctly!");
        }
	}

    protected function getInventory(): PlayerInventory {
        return $this->inventory;
    }

    protected function getArmorInventory(): ArmorInventory {
        return $this->armorInventory;
    }
}