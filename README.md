# ComboFly

ComboFly is an open source plugin and is made for [PocketMine-MP](https://github.com/pmmp/PocketMine-MP) 5.x.x.

## Installation

Install the file named `ComboFly.phar` in the`/home/plugins/` folder, you can download this file from [Poggit](https://poggit.pmmp.io/ComboFly).

## Command

### Command info

**Name:** /combofly

**Alias:**

- /cf

### Sub-Commands List

| Command | Description | Permission |
|-|:-:|:-:|
| **/combofly help** | Get help on the sub-commands. | No permission. |
| **/combofly join** | Join the arena by means of a command. | combofly.command.join.with.command |
| **/combofly setarena** | Set where players appear in the arena. | combofly.command.setarena |
| **/combofly setlobby** | Set where players appear when exiting the arena. | combofly.command.setlobby |
| **/combofly setkit** | Configure the kit with which the players appear in the arena (The kit will be configured with your inventory). | combofly.command.setkit |
| **/combofly setjoin** | Put the JoinNPC in your current location. | combofly.command.setjoin |
| **/combofly removejoin** | Remove the JoinNPC (Hit it). | combofly.command.removejoin |
| **/combofly debug** | Get the information you need to report a bug on github (It only runs from the console). | No permission. |

## Permissions

| Permission | Description |
|-|:-:|
| **combofly.\*** | Allow to players use all ComboFly features. |
| **combofly.command.join.with.command** | Allows join the arena by means of a command. |
| **combofly.command.setarena** | Allows set where players appear in the arena. |
| **combofly.command.setlobby** | Allows set where players appear when exiting the arena. |
| **combofly.command.setkit** | Allows configure the kit with which the players appear in the arena. |
| **combofly.command.setjoin** | Allows put the JoinNPC in your current location. |
| **combofly.command.removejoin** | Allows remove the JoinNPC. |

## Scoreboard

### Tags

| Tag | Description |
|-|:-:|
| **{player_kills}** | Shows the number of player kills. |
| **{player_deaths}** | Shows the number of player deaths. |
| **{player_ping}** | Shows the current ping of the player. |
| **{player_display_name}** | Shows the name that the player has to display. |
| **{player_real_name}** | Shows the real username of the player. |
| **{playing}** | Shows the number of players currently in the arena. |
| **{spectating}** | Shows the number of spectators currently in the arena. |
| **{total_players}** | Shows the total number of spectators and players currently in the arena. |

## Entities

### Tags

#### JoinNPC

| Tag | Description |
|-|:-:|
| **{playing}** | Shows the number of players currently in the arena. |
| **{spectating}** | Shows the number of spectators currently in the arena. |
| **{total_players}** | Shows the total number of spectators and players currently in the arena. |
| **{arena_status}** | Shows the status of the arena, this will return either 'Online' or 'Offline'. |

## Features

| List |
|-|
| Fully customizable |
| Spectator mode |
| Economy Integration |
| Scoreboard Support |
| Saves Players by UUID |
| Json Provider Support |
| UI |
| Join NPC |
| Custom Kit |

## TODO's

| List |
|-|
| SQLite3 Support |
| MySQL Support |
| Tops Floating Text |
| UI configuration menus |

## Libraries

List of libraries used in this plugin.

Note: All libraries are already integrated so you don't have to do extra installations.

### Library
- [pmforms - dktapps](https://github.com/dktapps-pm-pl/pmforms)
- [ScoreboardAPI - SabyMC](https://github.com/SabyMC/Implements/blob/main/src/scoreboard/ScoreboardAPI.php)

## Developers

Please see [CONTRIBUTING](https://github.com/MXJosueDev/ComboFly/blob/PM4/CONTRIBUTING.md).

### API

The ComboFly Arena API provides methods to manage arena functionality programmatically. All methods are accessed through the `Arena` singleton instance.

#### Getting Started

```php
<?php

use combofly\Arena;

$arena = Arena::getInstance();
```

#### Arena Configuration

**Set Arena Spawn Position**

Configure where players spawn when entering the arena.

```php
<?php

use combofly\Arena;
use pocketmine\world\Position;
use pocketmine\Server;

$arena = Arena::getInstance();

$world = Server::getInstance()->getWorldManager()->getWorldByName("ComboFlyArena");
$pos = new Position(0, 100, 0, $world);

$arena->setArena($pos);
```

**Set Lobby Spawn Position**

Configure where players return when exiting the arena.

```php
<?php

use combofly\Arena;
use pocketmine\world\Position;
use pocketmine\Server;

$arena = Arena::getInstance();

$world = Server::getInstance()->getWorldManager()->getDefaultWorld();
$pos = new Position(0, 100, 0, $world);

$arena->setLobby($pos);
```

**Check Arena Status**

Check if arena or lobby positions are configured.

```php
<?php

use combofly\Arena;

$arena = Arena::getInstance();

$isArenaLoaded = $arena->isArenaLoaded(); // Returns true if arena is configured
$isLobbyLoaded = $arena->isLobbyLoaded(); // Returns true if lobby is configured
```

#### Player Management

**Add Players to Arena**

Add a player as an active participant or spectator.

```php
<?php

use combofly\Arena;
use pocketmine\Server;

$arena = Arena::getInstance();
$player = Server::getInstance()->getPlayerExact("PlayerName");

$arena->addPlayer($player);     // Add as active player
$arena->addSpectator($player);  // Add as spectator
```

**Remove Players from Arena**

Remove a player or spectator from the arena.

```php
<?php

use combofly\Arena;
use pocketmine\Server;

$arena = Arena::getInstance();
$player = Server::getInstance()->getPlayerExact("PlayerName");

$arena->quitPlayer($player);     // Remove active player
$arena->quitSpectator($player);  // Remove spectator
```

**Check Player Status**

Check if a player is in the arena as a participant or spectator.

```php
<?php

use combofly\Arena;
use pocketmine\Server;

$arena = Arena::getInstance();
$player = Server::getInstance()->getPlayerExact("PlayerName");

if ($arena->isPlayer($player)) {
    // Player is an active participant
}

if ($arena->isSpectator($player)) {
    // Player is a spectator
}
```

**Get Player Lists**

Retrieve lists of players in the arena.

```php
<?php

use combofly\Arena;

$arena = Arena::getInstance();

$players = $arena->getPlayers();       // Array of active players
$spectators = $arena->getSpectators(); // Array of spectators
$all = $arena->getAllPlayers();        // Array of all players (active + spectators)
```

#### Kit Management

**Configure Arena Kit**

Set the kit items from a player's current inventory and armor.

```php
<?php

use combofly\Arena;
use pocketmine\Server;

$arena = Arena::getInstance();
$player = Server::getInstance()->getPlayerExact("PlayerName");

// The player's current inventory and armor will become the arena kit
$arena->setKit($player);
```

**Give Kit to Player**

Give the configured arena kit to a player (clears their inventory first).

```php
<?php

use combofly\Arena;
use pocketmine\Server;

$arena = Arena::getInstance();
$player = Server::getInstance()->getPlayerExact("PlayerName");

// Clears player inventory and gives them the arena kit
$arena->giveKit($player);
```

#### Broadcasting Messages

Send messages to all players and spectators in the arena.

```php
<?php

use combofly\Arena;

$arena = Arena::getInstance();

// Available message types: MESSAGE, TITLE, SUBTITLE, TIP, POPUP
$arena->broadcast("Welcome to the arena!", Arena::MESSAGE);
$arena->broadcast("Fight!", Arena::TITLE);
$arena->broadcast("Good luck!", Arena::SUBTITLE);
$arena->broadcast("Watch your back!", Arena::TIP);
$arena->broadcast("New player joined!", Arena::POPUP);
```

#### Player Statistics

Get player statistics (kills, deaths, and complete data).

```php
<?php

use combofly\Arena;
use pocketmine\player\Player;

$arena = Arena::getInstance();

// Can use Player object or player name string
$player = "PlayerName"; // or Server::getInstance()->getPlayerExact("PlayerName");

$playerData = $arena->getPlayerData($player);  // Returns PlayerData object
$kills = $arena->getKills($player);            // Returns kill count (int)
$deaths = $arena->getDeaths($player);          // Returns death count (int)

// Note: For offline players queried by name, they must have played before
// Otherwise returns 0 for kills/deaths or null for PlayerData
```

## License

[MIT](https://choosealicense.com/licenses/mit/)
