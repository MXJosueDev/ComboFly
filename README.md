# ComboFly

ComboFly is an open source plugin and is made for [PocketMine-MP](https://github.com/pmmp/PocketMine-MP) 3.25.x.

It is currently under development. ✔

## Installation

Install the file named `ComboFly.phar` in the`/home/plugins/` folder, you can download this file from [Poggit](https://poggit.pmmp.io/plugins)

## Commands

### Command info

**Name:** /combofly

**Alias:**

- /cf

### Command List

| Command | Description | Permission |
|-|:-:|:-:|
| **/combofly help** | Get help on the sub-commands. | No permission |
| **/combofly join** | Join the arena by means of a command. | combofly.command.join.with.command |
| **/combofly setarena** | Set where players appear in the arena. | combofly.command.setarena |
| **/combofly setlobby** | Set where players appear when exiting the arena. | combofly.command.setlobby |
| **/combofly setkit** | Configure the kit with which the players appear in the arena (The kit will be configured with your inventory). | combofly.command.setkit |
| **/combofly setjoin** | Put the JoinNPC in your current location. | combofly.command.setjoin |
| **/combofly removejoin** | Remove the JoinNPC (Hit it). | combofly.command.removejoin |

## Permissions

| Permission | Description |
|-|:-:|
| **combofly.command.join.with.command** | Allows join the arena by means of a command. |
| **combofly.command.setarena** | Allows set where players appear in the arena. |
| **combofly.command.setlobby** | Allows set where players appear when exiting the arena. |
| **combofly.command.setkit** | Allows configure the kit with which the players appear in the arena. |
| **combofly.command.setjoin** | Allows put the JoinNPC in your current location. |
| **combofly.command.removejoin** | Allows remove the JoinNPC. |

## Features

| List |
|-|
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

## Contributing

Please do not make Pull Request updating the supported PocketMine-MP API, it will be closed as soon as you see it, just open an issue.

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)
