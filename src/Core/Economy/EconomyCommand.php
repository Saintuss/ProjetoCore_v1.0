<?php

namespace Core\Economy;

use Core\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;

class EconomyCommand extends Command {

	private $utils;

	public function __construct(Loader $plugin){
		parent::__construct("money");
		$this->utils = $plugin->economyUtils;
	}

	private function getUtils(){
		return $this->utils;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		$utils = $this->getUtils();
		$myMoney = $utils->myMoney($sender->getName());
		if(!$sender instanceof Player){
			return true;
		}
		if(!isset($args[0])){
			$sender->sendMessage("§fSeu dinheiro: §e$".$myMoney);
			$sender->sendMessage("§7[§e!§7] §fComandos disponíveis\n§7- §f/money §ePAGAR\n§7- §f/money §eTOP");
			return true;
		}
		switch ($args[0]) {
			case 'pagar':
				if(!isset($args[1])){
					$sender->sendMessage("§cUsa /money pagar [jogador] [dinheiro]");
					return true;
				}
				if(!isset($args[2])){
					$sender->sendMessage("§cUsa /money pagar [jogador] [dinheiro]");
					return true;
				}
				if(!is_numeric($args[2])){
					$sender->sendMessage("§cPor favor coloque uma quantidade correta");
					return true;
				}
				$player = Server::getInstance()->getPlayer($args[1]);
				if(!$player instanceof Player){
					$sender->sendMessage("§cEste jogador não está online");
					return true;
				}
				$reduce = $myMoney - $args[2];
				if($reduce < 0){
					$sender->sendMessage("§cVocê não tem dinheiro suficiente!");
					return true;
				}
				$utils->reduceMoney($sender->getName(), $args[2]);
				$utils->addMoney($player->getName(), $args[2]);
				$sender->sendMessage("§7[§e!§7] §fVocê pagou $ ".$args[2]." ao jogador ".$player->getName());
				$player->sendMessage("§7[§e!§7] §fVocê recebeu $ ".$args[2]." do jogador ".$sender->getName());
				break;

			case 'ver':
				break;

			case 'top':
				$query = $utils->getQuery();
				if(!isset($args[1]) or $args[1] == 0){
					$sql = $query->query("SELECT * FROM economy ORDER BY money DESC LIMIT 0, 10 ;");
					$i = 0;
					$sender->sendMessage("§7[§e!§7] §fTOP RICOS §7[§e!§7]");
					while ($row = $sql->fetchArray(SQLITE3_ASSOC)) {
						$i++;
						$player = $row['player_name'];
						$money = $row['money'];
						$sender->sendMessage("§8".$i."° §f".$player." §8: §e$".$money);
					}
					$total = $query->query("SELECT COUNT(*) FROM economy;")->fetchArray(SQLITE3_ASSOC);
					$total = $total['COUNT(*)'];
					$total = $total / 10;
					$total = explode(".", $total);
					$total = $total[0];
					$total = $total + 1;
					$sender->sendMessage("§7Lista §e1 §7de §e".$total);
					return true;
				}
				$pag = $args[1];
				if(isset($pag)){
					$total = $query->query("SELECT COUNT(*) FROM economy;")->fetchArray(SQLITE3_ASSOC);
					$total = $total['COUNT(*)'];
					$total = $total / 10;
					$total = explode(".", $total);
					$total = $total[0];
					$total = $total + 1;
					$pag = $pag * 10;
					$pag = $pag - 10;
					$sql = $query->query("SELECT * FROM economy ORDER BY money DESC LIMIT {$pag}, 10 ;");
					$i = $pag;
					if($args[0] > $total){
						$sender->sendMessage("§cEsta página não existe!");
						return true;
					}
					while ($row = $sql->fetchArray(SQLITE3_ASSOC)) {
						$i++;
						$player = $row['player_name'];
						$money = $row['money'];
						$sender->sendMessage("§8".$i."° §f".$player." §8: §e$".$money);
					}
					$sender->sendMessage("§7Lista §e".$args[1]." §7de §e".$total);
					return true;
				}
				break;

			case 'set':
				if(!$sender->isOp()){
					return true;
				}
				if(!isset($args[1])){
					$sender->sendMessage("§cUsa /money set [jogador] [quantidade]");
					return true;
				}
				if(!isset($args[2])){
					$sender->sendMessage("§cUsa /money set [jogador] [quantidade]");
					return true;
				}
				if(!is_numeric($args[2])){
					$sender->sendMessage("§cPor favor coloque uma quantidade correta");
					return true;
				}
				$player = Server::getInstance()->getPlayer($args[1]);
				if(!$player instanceof Player){
					$sender->sendMessage("§cEste jogador não está online");
					return true;
				}
				$utils->setMoney($player->getName(), $args[2]);
				$sender->sendMessage("§7[§e!§7] §fVocê alterou o dinheiro de ".$player->getName()." para $ ".$args[2]);
				$player->sendMessage("§7[§e!§7] §fSeu dinheiro foi alterado para $ ".$args[2]);
				break;

			case 'add':
				if(!$sender->isOp()){
					return true;
				}
				if(!isset($args[1])){
					$sender->sendMessage("§cUsa /money add [jogador] [quantidade]");
					return true;
				}
				if(!isset($args[2])){
					$sender->sendMessage("§cUsa /money add [jogador] [quantidade]");
					return true;
				}
				if(!is_numeric($args[2])){
					$sender->sendMessage("§cPor favor coloque uma quantidade correta");
					return true;
				}
				$player = Server::getInstance()->getPlayer($args[1]);
				if(!$player instanceof Player){
					$sender->sendMessage("§cEste jogador não está online");
					return true;
				}
				$utils->addMoney($player->getName(), $args[2]);
				$player->sendMessage("§7[§e!§7] §f$ ".$args[2]." foram adicionados à sua conta bancária");
				$sender->sendMessage("§7[§e!§7] §fVocê adicionou $ ".$args[2]." à conta bancária do jogador ".$player->getName());
				break;

			case 'reduce':
				if(!$sender->isOp()){
					return true;
				}
				if(!isset($args[1])){
					$sender->sendMessage("§cUsa /money reduce [jogador] [quantidade]");
					return true;
				}
				if(!isset($args[2])){
					$sender->sendMessage("§cUsa /money reduce [jogador] [quantidade]");
					return true;
				}
				if(!is_numeric($args[2])){
					$sender->sendMessage("§cPor favor coloque uma quantidade correta");
					return true;
				}
				$player = Server::getInstance()->getPlayer($args[1]);
				if(!$player instanceof Player){
					$sender->sendMessage("§cEste jogador não está online");
					return true;
				}
				$playerMoney = $utils->myMoney($player->getName());
				$reduce = $playerMoney - $args[2];
				if($reduce < 0){
					$reduce = 0;
				}
				$utils->setMoney($player->getName(), $reduce);
				$player->sendMessage("§7[§e!§7] §f$ ".$args[2]." foram retirados de sua conta bancária");
				$sender->sendMessage("§7[§e!§7] §fVocê tirou $ ".$args[2]." da conta do jogador ".$player->getName());
				break;
			
			default:
				break;
		}
		return false;
	}
}