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

namespace combofly\api\scoreboard;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class ScoreboardAPI {
	
	private $scoreboards = [];
	
	public function new(Player $player, string $objectiveName, string $displayName): void { 
		if(isset($this->scoreboards[$player->getName()])){
			$this->remove($player);
		}

		$pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = "sidebar";
		$pk->objectiveName = $objectiveName;
		$pk->displayName = $displayName;
		$pk->criteriaName = "dummy";
		$pk->sortOrder = 0;

		$player->sendDataPacket($pk);
		$this->scoreboards[$player->getName()] = $objectiveName;
	}
	
	public function remove(Player $player): void {
		if(isset($this->scoreboards[$player->getName()])){
			$objectiveName = $this->getObjectiveName($player);
			
			$pk = new RemoveObjectivePacket();
			$pk->objectiveName = $objectiveName;

			$player->sendDataPacket($pk);
			unset($this->scoreboards[$player->getName()]);
		}
	}
	
	public function setLine(Player $player, int $score, string $message): void {
		if(!isset($this->scoreboards[$player->getName()])){
			throw new \InvalidKeyException("You not have set to scoreboards");
		}

		if($score > 15 || $score < 1){
			throw new \Exception("Error, you exceeded the limit of parameters 1-15");
		}

		$objectiveName = $this->getObjectiveName($player);
		
        $entry = new ScorePacketEntry();
		$entry->objectiveName = $objectiveName;
		$entry->type = $entry::TYPE_FAKE_PLAYER;
		$entry->customName = $message;
		$entry->score = $score;
		$entry->scoreboardId = $score;

		$pk = new SetScorePacket();
		$pk->type = $pk::TYPE_CHANGE;
		$pk->entries[] = $entry;
		
        $player->sendDataPacket($pk);
	}
	
	public function getObjectiveName(Player $player): ?string {
		return isset($this->scoreboards[$player->getName()]) ? $this->scoreboards[$player->getName()] : null;
	}
}