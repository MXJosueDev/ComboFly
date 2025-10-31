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

- Set up the arena
```php
<?php

use combofly\Arena; /* Class in which the API methods are.*/
use pocketmine\world\Position;
use pocketmine\Server;

$arena = Arena::getInstance(); /* Getting the instance of the object. */

$world = Server::getInstance()->getWorldManager()->getWorldByName("ComboFlyArena"); /* Object with instance of `pocketmine\world\World`. */
$pos = new Position(0, 100, 0 $world); /* Object instantiated to `pocketmine\world\Position`. */

$arena->setArena($pos); /* Set the position in which players will appear in the arena. */
```

- Set up the lobby
```php
<?php

use combofly\Arena; /* Class in which the API methods are.*/
use pocketmine\world\Position;
use pocketmine\Server;

$arena = Arena::getInstance(); /* Getting the instance of the object. */

$world = Server::getInstance()->getWorldManager()->getDefaultWorld(); /* Object with instance of `pocketmine\world\World`. */
$pos = new Position(0, 100, 0 $world); /* Object instantiated to `pocketmine\world\Position`. */

$arena->setLobby($pos); /* Sets the position players will appear in when they exit the arena. */
```

- Known if arena or lobby is loaded
```php
<?php

use combofly\Arena; /* Class in which the API methods are.*/

$arena = Arena::getInstance(); /* Getting the instance of the object. */

/* Returns `true` if it is loaded and` false` if not. */
$isArenaLoaded = $arena->isArenaLoaded(); 
$isLobbyLoaded = $arena->isLobbyLoaded(); 
```

- Add players or spectators to the arena
```php
<?php

use combofly\Arena; /* Class in which the API methods are.*/
use pocketmine\Server;

$arena = Arena::getInstance(); /* Getting the instance of the object. */

$player = Server::getInstance()->getPlayerExact("MXJosuepro033"); /* Player to add. */

/* This adds the player to the arena. */
$arena->addPlayer($player); 
$arena->addSpectator($player); 
```

- Remove players or spectators to the arena
```php
<?php

use combofly\Arena; /* Class in which the API methods are.*/
use pocketmine\Server;

$arena = Arena::getInstance(); /* Getting the instance of the object. */

$player = Server::getInstance()->getPlayerExact("MXJosuepro033"); /* Player to remove. */

/* This remove the player to the arena. */
$arena->quitPlayer($player); 
$arena->quitSpectator($player); 
```

- Know if a player is a player or a spectator in the arena
```php
<?php

use combofly\Arena; /* Class in which the API methods are.*/
use pocketmine\Server;

$arena = Arena::getInstance(); /* Getting the instance of the object. */

$player = Server::getInstance()->getPlayerExact("MXJosuepro033"); /* Player. */

/* This returns `true` if it is and` false` if not. */
$arena->isPlayer($player); 
$arena->isSpectator($player); 
```

- Get the list of players, spectators, or all players
```php
<?php

use combofly\Arena; /* Class in which the API methods are.*/

$arena = Arena::getInstance(); /* Getting the instance of the object. */

$players = $arena->getPlayers(); /* Returns an array with the list of players. */
$spectators = $arena->getSpectators(); /* Returns an array with the list of spectators. */
$all = $arena->getAllPlayers(); /* Returns an array with the list of players and spectators. */
```

- Set up the players kit when entering the arena
```php
<?php

use combofly\Arena; /* Class in which the API methods are.*/

$arena = Arena::getInstance(); /* Getting the instance of the object. */

$player = Server::getInstance()->getPlayerExact("MXJosuepro033"); /* From this variable the Inventory and the Armor Inventory are obtained. */

$arena->setKit($player); /* This sets up the arena kit. */
```

- Give the arena kit to a player
```php
<?php

use combofly\Arena; /* Class in which the API methods are.*/

$arena = Arena::getInstance(); /* Getting the instance of the object. */

$player = Server::getInstance()->getPlayerExact("MXJosuepro033"); /* Player to give the kit. */

$arena->giveKit($player); /* This resets the player's inventory and gives him the items. */
```

- Broadcast Message
```php
<?php

use combofly\Arena; /* Class in which the API methods are.*/

$arena = Arena::getInstance(); /* Getting the instance of the object. */

/* The second parameter is the type of message to send, you can find the types
   in `combofly\Arena` or this is the list:
    - MESSAGE  
    - TITLE  
    - SUBTITLE
    - TIP   
    - POPUP */
$arena->broadcast("Your message here.", Arena::MESSAGE); /* Global message to players and spectators in the arena. */
```

- Get Kills, Deaths and PlayerData of Player
```php
<?php

use combofly\Arena; /* Class in which the API methods are.*/

$arena = Arena::getInstance(); /* Getting the instance of the object. */

$player = "MXJosuepro033"; /* Getting a player to get their kills. */

/* The $player parameter can have a `pocketmine\player\Player` instance or be a string 
   with the player's name (If you query the data for the player's name
   and it is offline, it must have played before or it will return `0` or `null` 
   depending on the method used). */
$playerData = $arena->getPlayerData($player); /* It will return an object with instance of `combofly\PlayerData`. */
$playerKills = $arena->getKills($player); /* This will return the number of kills of the player. */
$playerDeaths = $arena->getDeaths($player); /* This will return the number of deaths of the player. */
```

## License

[MIT](https://choosealicense.com/licenses/mit/)
