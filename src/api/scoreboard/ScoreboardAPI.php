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
 * 
 * Source from: https://github.com/SabyMC/Implements/blob/main/src/scoreboard/ScoreboardAPI.php
 */

namespace combofly\api\scoreboard;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as TF;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

final class ScoreboardAPI {
	use SingletonTrait;

	const DISPLAY_SLOT = SetDisplayObjectivePacket::DISPLAY_SLOT_SIDEBAR;
	const CRITERIA_NAME = 'dummy';
	const SORT_ORDER = SetDisplayObjectivePacket::SORT_ORDER_ASCENDING;

	private $scoreboards = [];

	public function sendNew(Player $player, string $title): void
	{
		if($this->hasScoreboard($player)) {
			$this->remove($player);
		}

		$pk = SetDisplayObjectivePacket::create(
			self::DISPLAY_SLOT,
			$player->getName(),
			TF::colorize($title),
			self::CRITERIA_NAME,
			self::SORT_ORDER
		);

		$player->getNetworkSession()->sendDataPacket($pk);
		$this->scoreboards[$player->getName()] = $player;
	}

	public function remove(Player $player): void 
	{
		if($this->hasScoreboard($player)) {
			$pk = RemoveObjectivePacket::create($player->getName());

			$player->getNetworkSession()->sendDataPacket($pk);
			unset($this->scoreboards[$player->getName()]);
		}
	}


	public function setLines(Player $player, array $lines): void
	{
		foreach($lines as $score => $line) {
			if($score >= 15) break;
			$this->setLine($player, $score + 1, $line);
		}
	}

	public function setLine(Player $player, int $score, string $message): void
	{
		if(!$this->hasScoreboard($player)) return;
		if($score > 15 || $score < 1) return;

		$entry = new ScorePacketEntry();
		$entry->objectiveName = $player->getName();
		$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
		$entry->customName = $message;
		$entry->score = $score;
		$entry->scoreboardId = $score;

		$pk = new SetScorePacket(
			SetScorePacket::TYPE_CHANGE,
			[$entry]
		);

		$player->getNetworkSession()->sendDataPacket($pk);
	}

	private function hasScoreboard(Player $player): bool
	{
		return isset($this->scoreboards[$player->getName()]);
	}
}