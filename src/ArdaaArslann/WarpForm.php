<?php

namespace ArdaaArslann;

use pocketmine\{Player, Server};
use pocketmine\command\{CommandSender, Command};
use pocketmine\utils\Config;
use ArdaaArslann\WarpMain;
use pocketmine\form\MenuOption;
use pocketmine\form\MenuForm;
use pocketmine\math\Vector3;
use pocketmine\level\Level;

class WarpForm extends MenuForm {
  
  public function __construct(Player $g){
  $this->plugin = WarpMain::getInstance();
  $options = [];
  foreach($this->plugin->config->getAll() as $key => $value){
  $options[] = new MenuOption($key);
  }
  parent::__construct(
    "Işınlanma Menüsü",
    "§7Gitmek İstediğin Yeri Seç",
    $options, function(Player $g, int $data): void{
    $text = $this->getOption($data)->getText();
    if(empty($this->plugin->config->get($text))){
    $g->sendMessage("§f» §c{$text} §7Adında Kayıt Bulunamadı!");
    return;
    }
    
    if($this->plugin->config->get($text)["getX"] === null or $this->plugin->config->get($text)["getY"] === null or $this->plugin->config->get($text)["getZ"] === null or $this->plugin->config->get($text)["getLevel"] === null){
    $g->sendMessage("§f» §c{$text} §7Adındaki Warpta Eksik Bilgiler Var!");
    return;
    }
    
    $position = $this->plugin->getWarp($text);
    $this->plugin->getServer()->loadLevel($this->plugin->config->get($text)["getLevel"]);
    $g->teleport(Server::getInstance()->getLevelByName($this->plugin->config->get($text)["getLevel"])->getSpawnLocation());
    $g->teleport($position);
    $g->sendMessage("§f» §a{$text} §7Adlı Yere Işınlandın!");
    });
  }
}