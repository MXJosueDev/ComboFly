# pmforms
Form API library for PocketMine-MP plugins using closures for handling data

## Overview
This library provides a simple and elegant way to create custom forms (UI) for Minecraft: Bedrock Edition using PocketMine-MP. It supports three types of forms:

- **MenuForm** - A form with a list of buttons
- **CustomForm** - A form with custom input elements (text inputs, dropdowns, toggles, sliders, etc.)
- **ModalForm** - A simple yes/no dialog

## Table of Contents
- [MenuForm Examples](#menuform-examples)
- [CustomForm Examples](#customform-examples)
- [ModalForm Examples](#modalform-examples)
- [Custom Form Elements](#custom-form-elements)
- [FormIcon Usage](#formicon-usage)
- [Error Handling](#error-handling)
- [Including in Other Plugins](#including-in-other-plugins)

## MenuForm Examples

### Basic Menu
A menu form presents a list of buttons to the player. The player can select one button or close the form.

```php
<?php

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

$form = new MenuForm(
    "Choose an Option",              // Form title
    "Please select what you'd like to do:", // Form text/description
    [
        new MenuOption("Option 1"),
        new MenuOption("Option 2"),
        new MenuOption("Option 3")
    ],
    function(Player $player, int $selectedOption) : void {
        // Handle button selection
        switch($selectedOption) {
            case 0:
                $player->sendMessage("You selected Option 1");
                break;
            case 1:
                $player->sendMessage("You selected Option 2");
                break;
            case 2:
                $player->sendMessage("You selected Option 3");
                break;
        }
    },
    function(Player $player) : void {
        // Optional: Handle form closure (when player presses X)
        $player->sendMessage("You closed the form");
    }
);

$player->sendForm($form);
```

### Menu with Icons
You can add icons to menu buttons using `FormIcon`:

```php
<?php

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use dktapps\pmforms\FormIcon;
use pocketmine\player\Player;

$form = new MenuForm(
    "Shop Menu",
    "Select an item to purchase:",
    [
        new MenuOption("Diamond Sword", new FormIcon("https://example.com/sword.png")),
        new MenuOption("Golden Apple", new FormIcon("https://example.com/apple.png")),
        new MenuOption("Enchanted Book", new FormIcon("https://example.com/book.png"))
    ],
    function(Player $player, int $selectedOption) : void {
        $items = ["Diamond Sword", "Golden Apple", "Enchanted Book"];
        $player->sendMessage("You purchased: " . $items[$selectedOption]);
    }
);

$player->sendForm($form);
```

## CustomForm Examples

### Basic Custom Form
A custom form allows you to create forms with various input elements.

```php
<?php

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Toggle;
use pocketmine\player\Player;

$form = new CustomForm(
    "Player Settings",
    [
        new Input("username", "Enter your username:", "Username", "Steve"),
        new Dropdown("gamemode", "Select gamemode:", ["Survival", "Creative", "Adventure"]),
        new Toggle("pvp", "Enable PVP?", false)
    ],
    function(Player $player, CustomFormResponse $response) : void {
        $username = $response->getString("username");
        $gamemodeIndex = $response->getInt("gamemode");
        $pvpEnabled = $response->getBool("pvp");
        
        $gamemodes = ["Survival", "Creative", "Adventure"];
        $player->sendMessage("Username: $username");
        $player->sendMessage("Gamemode: " . $gamemodes[$gamemodeIndex]);
        $player->sendMessage("PVP: " . ($pvpEnabled ? "Enabled" : "Disabled"));
    },
    function(Player $player) : void {
        // Optional: Handle form closure
        $player->sendMessage("Settings form closed");
    }
);

$player->sendForm($form);
```

### Advanced Custom Form with All Elements
This example demonstrates all available form elements:

```php
<?php

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\StepSlider;
use dktapps\pmforms\element\Slider;
use dktapps\pmforms\element\Toggle;
use pocketmine\player\Player;

$form = new CustomForm(
    "Advanced Settings",
    [
        new Label("section1", "§l§eGeneral Settings"),
        new Input("name", "Enter your display name:", "Display Name", $player->getName()),
        new Toggle("notifications", "Enable notifications?", true),
        
        new Label("section2", "§l§eGame Settings"),
        new Dropdown("difficulty", "Select difficulty:", ["Easy", "Normal", "Hard", "Expert"], 1),
        new StepSlider("render_distance", "Render distance:", ["Tiny", "Short", "Normal", "Far", "Very Far"], 2),
        new Slider("volume", "Music volume:", 0.0, 100.0, 1.0, 50.0)
    ],
    function(Player $player, CustomFormResponse $response) : void {
        $name = $response->getString("name");
        $notifications = $response->getBool("notifications");
        $difficultyIndex = $response->getInt("difficulty");
        $renderDistanceIndex = $response->getInt("render_distance");
        $volume = $response->getFloat("volume");
        
        $player->sendMessage("Settings saved!");
        $player->sendMessage("Name: $name");
        $player->sendMessage("Notifications: " . ($notifications ? "ON" : "OFF"));
        $player->sendMessage("Volume: $volume%");
    }
);

$player->sendForm($form);
```

## ModalForm Examples

### Simple Yes/No Dialog
A modal form presents a simple yes/no question with two buttons.

```php
<?php

use dktapps\pmforms\ModalForm;
use pocketmine\player\Player;

$form = new ModalForm(
    "Confirm Action",
    "Are you sure you want to delete your account?",
    function(Player $player, bool $choice) : void {
        if($choice) {
            // User clicked "Yes"
            $player->sendMessage("Account deleted!");
        } else {
            // User clicked "No"
            $player->sendMessage("Action cancelled");
        }
    },
    "Yes, Delete",  // Custom "Yes" button text
    "No, Cancel"    // Custom "No" button text
);

$player->sendForm($form);
```

### Modal with Default Button Text
If you don't specify button text, it will use the client's translated "Yes" and "No":

```php
<?php

use dktapps\pmforms\ModalForm;
use pocketmine\player\Player;

$form = new ModalForm(
    "Teleport",
    "Do you want to teleport to spawn?",
    function(Player $player, bool $choice) : void {
        if($choice) {
            $player->teleport($player->getWorld()->getSpawnLocation());
            $player->sendMessage("Teleported to spawn!");
        }
    }
    // Using default button text: "gui.yes" and "gui.no"
);

$player->sendForm($form);
```

## Custom Form Elements

### Input
Text input field with placeholder and default value:

```php
new Input(
    "element_name",        // Unique element name
    "Label text",          // Label shown above the input
    "Placeholder text",    // Text shown when input is empty
    "Default value"        // Default value (optional)
)
```

### Label
Display-only text (no input):

```php
new Label(
    "element_name",        // Unique element name
    "§l§aThis is a label" // Text to display (supports color codes)
)
```

### Dropdown
Dropdown menu with multiple options:

```php
new Dropdown(
    "element_name",        // Unique element name
    "Select an option:",   // Label text
    ["Option 1", "Option 2", "Option 3"], // Array of options
    0                      // Default selected index (optional, defaults to 0)
)
```

### StepSlider
Horizontal slider with predefined steps:

```php
new StepSlider(
    "element_name",        // Unique element name
    "Choose a level:",     // Label text
    ["Low", "Medium", "High", "Very High"], // Array of steps
    1                      // Default selected index (optional, defaults to 0)
)
```

### Slider
Numeric slider with min, max, and step values:

```php
new Slider(
    "element_name",        // Unique element name
    "Select volume:",      // Label text
    0.0,                   // Minimum value
    100.0,                 // Maximum value
    1.0,                   // Step increment
    50.0                   // Default value (optional, defaults to min)
)
```

### Toggle
On/off switch:

```php
new Toggle(
    "element_name",        // Unique element name
    "Enable feature?",     // Label text
    false                  // Default value (optional, defaults to false)
)
```

## FormIcon Usage

Icons can be added to `MenuOption` buttons. Icons can be loaded from URLs or paths:

### URL Icon
```php
use dktapps\pmforms\FormIcon;

// Load icon from URL (most commonly used)
$icon = new FormIcon(
    "https://example.com/icon.png",
    FormIcon::IMAGE_TYPE_URL  // Default, can be omitted
);

$option = new MenuOption("Option Text", $icon);
```

### Path Icon
```php
use dktapps\pmforms\FormIcon;

// Load icon from resource pack path (less commonly used)
$icon = new FormIcon(
    "textures/items/diamond_sword",
    FormIcon::IMAGE_TYPE_PATH
);

$option = new MenuOption("Diamond Sword", $icon);
```

**Note:** URL icons are more reliable. Path icons require the texture to be in the client's resource pack.

## Error Handling

### Form Validation
The library automatically validates form responses. If validation fails, a `FormValidationException` is thrown:

```php
use pocketmine\form\FormValidationException;

try {
    // Form handling is done automatically by PocketMine-MP
    // Validation errors are caught internally
} catch(FormValidationException $e) {
    // This is handled by the server
    $player->sendMessage("Invalid form data: " . $e->getMessage());
}
```

### Custom Form Response Validation
When retrieving values from `CustomFormResponse`, ensure the element name exists:

```php
try {
    $value = $response->getString("element_name");
} catch(\InvalidArgumentException $e) {
    // Element name not found
    $player->sendMessage("Error: Element not found");
}
```

### Closure Signatures
The library validates closure signatures at runtime. Make sure your closures match the required signatures:

- **MenuForm onSubmit:** `function(Player $player, int $selectedOption) : void`
- **MenuForm onClose:** `function(Player $player) : void`
- **CustomForm onSubmit:** `function(Player $player, CustomFormResponse $response) : void`
- **CustomForm onClose:** `function(Player $player) : void`
- **ModalForm onSubmit:** `function(Player $player, bool $choice) : void`

## Including in Other Plugins

This library supports being included as a [virion](https://github.com/poggit/support/blob/master/virion.md).

If you use [Poggit](https://poggit.pmmp.io) to build your plugin, you can add it to your `.poggit.yml` like so:

```yml
projects:
  YourPlugin:
    libs:
      - src: dktapps-pm-pl/pmforms/pmforms
        version: ^2.0.0
```

## Additional Resources

For more examples, check out the [demo plugin](https://github.com/dktapps-pm-pl/pmforms-demo) which shows how to use the API in a plugin.
