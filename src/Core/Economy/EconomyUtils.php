<?php

namespace Core\Economy;

use Core\Loader;

class EconomyUtils {

	private $plugin;

	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
	}

	private function getPlugin(){
		return $this->plugin;
	}

    /**
     * @return \SQLite3
     */
	public function getQuery(){
		return $this->getPlugin()->economyData;
	}

	private function getQueryPlayer($name){
		$query = $this->getQuery()->query("SELECT * FROM economy WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		return $query;
	}

	public function getPlayer($name){
		$query = $this->getQueryPlayer($name);
		if(!is_null($query['player_name'])){
			return $query;
		}
		return false;
	}

	public function createPlayer($name){
		$query = $this->getQuery();
		$query->exec("INSERT INTO economy (player_name, money) VALUES ('$name', 1000);");
	}

	public function myMoney($name){
		$query = $this->getPlayer($name);
		return $query['money'];
	}

	public function addMoney($name, $count){
		$query = $this->getQuery();
		$money = $this->myMoney($name) + $count;
		$query->exec("UPDATE economy SET money = '$money' WHERE player_name = '$name';");
	}

	public function setMoney($name, $money){
		$query = $this->getQuery();
		$query->exec("UPDATE economy SET money = '$money' WHERE player_name = '$name';");
	}

	public function reduceMoney($name, $count){
		$query = $this->getQuery();
		$money = $this->myMoney($name) - $count;
		$query->exec("UPDATE economy SET money = '$money' WHERE player_name = '$name';");
	}
}