# ComboFly

ComboFly is an open source plugin and is made for [PocketMine-MP](https://github.com/pmmp/PocketMine-MP) 4.x.x.

It is currently upgrading from PM3 to PM4.

## Installation

Install the file named `ComboFly.phar` in the`/home/plugins/` folder, you can download this file from [Poggit](https://poggit.pmmp.io/plugins).

## Developers

Please see <a href="https://github.com/MXJosueDev/ComboFly/blob/PM4/CONTRIBUTING.md">CONTRIBUTING</a>.

### API

- Get Kills, Deaths and PlayerData of Player
```php
<?php

use combofly\Arena; /* Class in which the API methods are.*/
use pocketmine\Server;

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
| **/combofly debug** | Debug info for bug report. | No permission. |

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

## License

[MIT](https://choosealicense.com/licenses/mit/)
