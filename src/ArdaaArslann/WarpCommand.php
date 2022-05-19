<?php

namespace ArdaaArslann;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\command\{CommandSender, Command};
use pocketmine\utils\Config;
use ArdaaArslann\Main;
use ArdaaArslann\WarpForm;

class WarpCommand extends Command {
  
  public function __construct(WarpMain $plugin){
    parent::__construct("warp", "Dünyalar Arası Geçiş Bileti", "warp");
    $this->p = $plugin;
    $this->setAliases(["isinlan"]);
  }
  
  public function execute(CommandSender $g, string $label, $args){
    $command = array_shift($args);
    $warpName = implode(" ", $args);
    if(is_null($command)){
    if($g instanceof Player){
    $g->sendForm(new WarpForm($g));
    return;
    }else{
    $g->sendMessage(str_replace("{warp_name}", $warpName, $this->p->config->get("messages")["warp.usage"])); 
    return;
    }
    }
    
    if(in_array(strtolower($command), $this->p->config->get("commands")["warp.create.commands"])){
    
    if(!$g->hasPermission($this->p->config->get("permissions")["warp.create.permission"])){
    $g->sendMessage($this->p->config->get("messages")["no.have.permission"]);
    return;
    }
    
    if(is_null($warpName) or $warpName == ""){
    $g->sendMessage(str_replace("{warp_name}", $warpName, $this->p->config->get("messages")["warp.create.usage"]));
    return;
    }
    
    if($this->p->getWarp($warpName) === true){
    $g->sendMessage(str_replace("{warp_name}", $warpName, $this->p->config->get("messages")["warp.create.failed"]));
    return;
    }
    
    $x = $g->getPosition()->getFloorX();
    $y = $g->getPosition()->getFloorY();
    $z = $g->getPosition()->getFloorZ();
    $world = $g->getWorld()->getFolderName();
    if($this->p->setWarp($warpName, $x, $y, $z, $world) === true){
    $g->sendMessage(str_replace("{warp_name}", $warpName, $this->p->config->get("messages")["warp.create.succes"]));
    return;
    } else $g->sendMessage(str_replace("{warp_name}", $warpName, $this->p->config->get("messages")["warp.create.error"]));
    return;
    }
    
    if(in_array(strtolower($command), $this->p->config->get("commands")["warp.delete.commands"])){
    
    if(!$g->hasPermission($this->p->config->get("permissions")["warp.delete.permission"])){
    $g->sendMessage($this->p->config->get("messages")["no.have.permission"]);
    return;
    }
    
    if(is_null($warpName) or $warpName == ""){
    $g->sendMessage(str_replace("{warp_name}", $warpName, $this->p->config->get("messages")["warp.delete.usage"]));
    return;
    }
    
    if($this->p->getWarp($warpName) === false){
    $g->sendMessage(str_replace("{warp_name}", $warpName, $this->p->config->get("messages")["warp.delete.failed"]));
    return;
    }
    
    if($this->p->delWarp($warpName) === true){
    $g->sendMessage(str_replace("{warp_name}", $warpName, $this->p->config->get("messages")["warp.delete.succes"]));
    return;
    } else $g->sendMessage(str_replace("{warp_name}", $warpName, $this->p->config->get("messages")["warp.delete.error"]));
    return;
    }
    
    if(in_array(strtolower($command), $this->p->config->get("commands")["warp.list.commands"])){
    
    if(!$g->hasPermission($this->p->config->get("permissions")["warp.list.permission"])){
    $g->sendMessage($this->p->config->get("messages")["no.have.permission"]);
    return;
    }
    
    if(($list = $this->p->listWarp()) !== false){
    $g->sendMessage(str_replace("{warp_name}", $list, $this->p->config->get("messages")["warp.list.succes"]));
    return;
    } else $g->sendMessage(str_replace("{warp_name}", $list, $this->p->config->get("messages")["warp.list.error"]));
    return;
    }
    
    $g->sendMessage($this->p->config->get("messages")["warp.usage"]);
    return;
  }
}
