<?php
/**
 * @name Dash
 * @main dash\Dash
 * @author sipsam1
 * @version 1
 * @api 3.0.0-ALPHA10
 */

namespace dash;

use pocketmine\{plugin\PluginBase, event\Listener, command\CommandSender, command\Command, command\PluginCommand};
use function strtolower, intval, str_replace, explode;

class Dash extends PluginBase implements Listener{
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->regCmd("dash", "OP", \pocketmine\utils\TextFormat::RED."You don't have permission to use this command", "Let's Dash");
		$this->times = [];
	}
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
		$cmd = strtolower($command->getName());
		if(isset($args[0])) $args[0] = preg_replace("/\s+/", "", $args[0]);
		if(isset($args[1])) $args[1] = preg_replace("/\s+/", "", $args[1]);
		if($cmd == "dash"){
			if(!isset($args[0]) or !isset($args[1])){
				$sender->sendMessage("/dash (all/[player,...]) (intensity)");
				return \false;
			}
			if(isset($args[2])) return \false;
			if(!is_numeric($args[1])){
				$sender->sendMessage(\pocketmine\utils\TextFormat::RED."You can only use the Number on the intensity");
				return \false;
			}
			$intensity = intval($args[1]);
			if($args[0] == "all"){
				foreach($this->getServer()->getOnlinePlayers() as $player){
					if(!$player->isOp()){
						$player->setMotion($player->getDirectionVector()->multiply($intensity));
						$player->sendMessage(\pocketmine\utils\TextFormat::LIGHT_PURPLE."You are dashed by OP");
					}
				}
				return \true;
			}
			$players = str_replace("[", "", $args[0]);
			$players = str_replace("]", "", $players);
			$players = explode(",", $players);
			foreach($players as $k => $v){
				$player = $this->getServer()->getPlayerExact($players[$k]);
				if(!$player->isOp()){
					$player->setMotion($player->getDirectionVector()->multiply($intensity));
					$player->sendMessage(\pocketmine\utils\TextFormat::LIGHT_PURPLE."You are dashed by OP");
				}
			}
			return \true;
		}
		return \false;
	}
	public function regCmd($name, $permission = "OP", $permissionMessage = "", $description = ""){
		$cmd = new PluginCommand($name, $this);
		$cmd->setPermission($permission);
		$cmd->setPermissionMessage($permissionMessage);
		$cmd->setDescription($description);
		$this->getServer()->getCommandMap()->register($name, $cmd);
	}
}