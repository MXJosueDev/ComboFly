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

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class ScoreboardAPI {

	const DISPLAY_SLOT = "sidebar";
	const CRITERIA_NAME = "dummy";
	const SORT_ORDER = 0;
	
	private $scoreboards = [];
	
	public function new(Player $player, string $objectiveName, string $displayName): void { 
		if(isset($this->scoreboards[$player->getName()])){
			$this->remove($player);
		}

		$pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = self::DISPLAY_SLOT;
		$pk->objectiveName = $objectiveName;
		$pk->displayName = $displayName;
		$pk->criteriaName = self::CRITERIA_NAME;
		$pk->sortOrder = self::SORT_ORDER;

		$player->getNetworkSession()->sendDataPacket($pk);
		$this->scoreboards[$player->getName()] = $objectiveName;
	}
	
	public function remove(Player $player): void {
		if(isset($this->scoreboards[$player->getName()])){
			$objectiveName = $this->getObjectiveName($player);
			
			$pk = new RemoveObjectivePacket();
			$pk->objectiveName = $objectiveName;

			$player->getNetworkSession()->sendDataPacket($pk);
			unset($this->scoreboards[$player->getName()]);
		}
	}
	
	public function setLine(Player $player, int $score, string $message): void {
		if(!isset($this->scoreboards[$player->getName()])){
			throw new \Exception("You not have set to scoreboards");
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
		
		$player->getNetworkSession()->sendDataPacket($pk);
	}
	
	public function getObjectiveName(Player $player): ?string {
		return isset($this->scoreboards[$player->getName()]) ? $this->scoreboards[$player->getName()] : null;
	}
}