<?php

namespace ArdaaArslann;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\command\{CommandSender, Command};
use pocketmine\utils\Config;
use ArdaaArslann\WarpMain;
use dktapps\pmforms\{MenuForm, MenuOption, FormIcon};
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Slider;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Toggle;
use pocketmine\math\Vector3;
use pocketmine\world\Level;

class WarpForm extends MenuForm {
  
  public function __construct(Player $g){
  $this->p = WarpMain::getInstance();
  $options = [];
  $num = 0;
  foreach($this->p->getWarps() as $warp => $info){
  $infos = explode(":", $info);
  if(file_exists($this->p->getServer()->getDataPath()."worlds/".$infos[3])){
  $this->p->getServer()->getWorldManager()->loadWorld($infos[3]);
  $world = count($this->p->getServer()->getWorldManager()->getWorldByName($infos[3])->getPlayers());
  $button = str_replace("{warp_name}", $warp, $this->p->config->get("ui")["button"]);
  $button = str_replace("{number}", $world, $button);
  $options[] = new MenuOption($button);
  $this->list[$num] = $warp.":".$info;
  $num++;
  }else{
  $this->p->delWarp($warp);
  }
  if($num === 0){$text = $this->p->config->get("ui")["no.warps"];}else{$text = str_replace("{number}", $num, $this->p->config->get("ui")["warps"]);}
  parent::__construct(
    $this->p->config->get("ui")["title"],
    $text,
    $options, function(Player $g, int $data): void{
    $button = $this->list[$data];
    $button = explode(":", $button);
    if($this->p->getWarp($button[0]) === false){
    $g->sendMessage(str_replace("{warp_name}", $button[0], $this->p->config->get("messages")["warp.teleport.error"]));
    return;
    }
    
    if($this->p->goWarp($g, $button[1], $button[2], $button[3], $button[4]) === true){
    $g->sendMessage(str_replace("{warp_name}", $button[0], $this->p->config->get("messages")["warp.teleport.succes"]));
    }else $g->sendMessage(str_replace("{warp_name}", $button[0], $this->p->config->get("messages")["warp.teleport.failed"]));
    return;
    });
  }
}
}
