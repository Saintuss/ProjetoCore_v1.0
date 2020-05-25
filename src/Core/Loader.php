<?php

namespace Core;

use Core\Economy\EconomyCommand;
use Core\Economy\EconomyUtils;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase {

    // SQlite3
    public $economyData;

    // Utils
    /**
     * @var EconomyUtils
     */
    public $economyUtils;

    public function onEnable(){
        // Creation Pastas
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder()."Database");

        // Creation base de dados
        $this->economyData = new \SQLite3($this->getDataFolder()."Database/economy.sqlite3");
        $this->economyData->exec("CREATE TABLE IF NOT EXISTS economy(
        player_name TEXT NOT NULL PRIMARY KEY,
        money INT NOT NULL
        )");

        // Register Utils
        $this->economyUtils = new EconomyUtils($this);

        // Register Events

        // Register Commands
        $commands = [
            new EconomyCommand($this)
        ];
        $this->getServer()->getCommandMap()->registerAll('Core', $commands);
    }
}