<?php

declare(strict_types=1);

namespace Terpz710\RandomSpawn;

use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\world\World;

class Main extends PluginBase implements Listener {

    private $joinedPlayers = [];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->reloadConfig();
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        if (!$this->hasJoinedBefore($player)) {
            $spawnLocation = $this->getRandomSpawnLocation($player);
            $player->teleport($spawnLocation);
            $this->markAsJoined($player);
        }
    }

    private function hasJoinedBefore(Player $player): bool {
        $playerDataFile = $this->getDataFolder() . "joined_players.json";
        if (!file_exists($playerDataFile)) {
            return false;
        }

        $playerUniqueId = $player->getUniqueId()->toString();
        $joinedPlayersData = json_decode(file_get_contents($playerDataFile), true);

        return isset($joinedPlayersData[$playerUniqueId]);
    }

    private function markAsJoined(Player $player): void {
        $playerDataFile = $this->getDataFolder() . "joined_players.json";
        $playerUniqueId = $player->getUniqueId()->toString();
        $joinedPlayersData = [];
        if (file_exists($playerDataFile)) {
            $joinedPlayersData = json_decode(file_get_contents($playerDataFile), true);
        }

        $joinedPlayersData[$playerUniqueId] = true;

        file_put_contents($playerDataFile, json_encode($joinedPlayersData, JSON_PRETTY_PRINT));
    }

    public function getRandomSpawnLocation(Player $player): Position {
        $config = $this->getConfig();
        $maxX = $config->get("spawn_range")["max_x"];
        $maxZ = $config->get("spawn_range")["max_z"];
        
        $x = rand(0, $maxX);
        $z = rand(0, $maxZ);
        
        $worldManager = $this->getServer()->getWorldManager();
        $world = $worldManager->getDefaultWorld();
        
        $y = $world->getHighestBlockAt($x, $z);
        
        return new Position($x, $y, $z, $world);
    }
}
