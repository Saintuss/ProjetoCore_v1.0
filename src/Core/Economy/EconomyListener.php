<?php

namespace Core\Economy;

use Core\Loader;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;

class EconomyListener implements Listener {

	private $plugin;
	private $utils;

	public function __construct(Loader $plugin){
		Server::getInstance()->getPluginManager()->registerEvents($this, $plugin);
		$this->plugin = $plugin;
		$this->utils = $plugin->economyUtils;
	}

	private function getPlugin(){
		return $this->plugin;
	}

	private function getUtils(){
		return $this->utils;
	}

	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();

		$utils = $this->getUtils();

		if(!$utils->getPlayer($name)){
			$utils->createPlayer($name);
			$this->getPlugin()->getLogger()->notice("[!] A conta bancária do jogador {$name} foi criada");
		}
	}
}