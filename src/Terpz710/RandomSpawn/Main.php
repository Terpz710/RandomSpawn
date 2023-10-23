<?php

declare(strict_types=1);

namespace Terpz710\RandomSpawn;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->reloadConfig();
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $spawnLocation = $this->getRandomSpawnLocation($player);
        $player->teleport($spawnLocation);
    }

    public function onPlayerRespawn(PlayerRespawnEvent $event): void {
        $player = $event->getPlayer();
        $spawnLocation = $this->getRandomSpawnLocation($player);
        $event->setRespawnPosition($spawnLocation);
    }

    public function onPlayerDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $spawnLocation = $this->getRandomSpawnLocation($player);
        $player->teleport($spawnLocation);
    }

    public function getRandomSpawnLocation($player): Vector3 {
        $config = $this->getConfig();
        $maxX = $config->get("spawn_range")["max_x"];
        $maxZ = $config->get("spawn_range")["max_z"];
        
        $x = rand(0, $maxX);
        $z = rand(0, $maxZ);
        $world = $player->getDefaultWorld();
        
        $y = $world->getHighestBlockAt($x, $z);
        
        return new Vector3($x, $y, $z);
    }
}
