<?php

namespace ArdaaArslann;

use pocketmine\plugin\{Plugin, PluginBase};
use pocketmine\command\{Command, CommandSender};
use pocketmine\{Player, Server};
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\event\Listener;

class WarpMain extends PluginBase implements Listener {
  
  public static $instance;
  
  public function onLoad()
   {
  static::$instance = $this;
  }
  
  public static function getInstance(): WarpMain
   {
  return self::$instance;
  }
  
  public function onEnable(){
    $this->getLogger()->info("§aPlugin is Enabled!");
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
    foreach($this->config->getAll() as $key => $value){
    if($value["getX"] === null or $value["getY"] === null or $value["getZ"] === null or $value["getLevel"] === null){
    $this->config->remove($key);
    $this->config->save();
    }
    }
  }
  
  public function onDisable(){
    $this->getLogger()->info("§cPlugin is Disabled!");
  }
  
  public function onCommand(CommandSender $g, Command $cmd, string $commandLabel, array $args): bool{
   switch($cmd->getName()){
    case "warp":
    if($g->hasPermission("warp.command")){
    if(empty($args[0])){
    if(count($this->config->getAll(true)) > 0){
    $g->sendForm(new WarpForm($g));
    }else $g->sendMessage("§f» §7Hiç Warp Yeri Yok!");
    } else {
    $warpName = implode(" ", $args);
    if(!empty($this->config->get($warpName))){
    
    if($this->getServer()->getLevelByName($this->config->get($warpName)["getLevel"])){
    
    $position = $this->getWarp($warpName);
    $g->teleport($position);
    $g->sendMessage("§f» §a{$warpName} §7Adlı Yere Işınlandın!");
    
    }else $g->sendMessage("§f» §c{$warpName} §7Adlı Dünya Bulunamadı!");
    
    }else $g->sendMessage("§f» §c{$warpName} §7Adında Bir Kayıt Bulunamadı!");
    }
    }else $g->sendMessage("§f» §cBu Komutu Kullanmaya Yetkin Yok!");
    break;
    
    case "warplist":
    if($g->hasPermission("warplist.command")){
    if(count($this->config->getAll(true)) > 0){
    $warpList = "";
    foreach($this->config->getAll() as $key => $value){
    $warpList .= "§f[§e{$key}§f], ";
    }
    $g->sendMessage("§f» §aWarplar: {$warpList}");
    }else{
    $g->sendMessage("§f» §aWarplar: §7Hiç Yer Yok!");
    }
    }else $g->sendMessage("§f» §cBu Komutu Kullanmaya Yetkin Yok!");
    break;
    
    case "setwarp":
    if($g->hasPermission("setwarp.command")){
    $warpName = implode(" ", $args);
    if(empty($args[1])){
    if(empty($this->config->get($warpName))){
    
    $x = $g->getX();
    $y = $g->getY();
    $z = $g->getZ();
    $levelName = $g->getLevel()->getFolderName();
    $this->setWarp($warpName, $x, $y, $z, $levelName);
    $g->sendMessage("§f» §a{$warpName} §7Adında Warp Oluşturuldu!");
    }else $g->sendMessage("§f» §c{$warpName} §7İsminde Bir Warp Yeri Var!");
    }else $g->sendMessage("§f» §cKullanım: §f/setwarp [warp:string]");
    }else $g->sendMessage("§f» §cBu Komutu Kullanmaya Yetkin Yok!");
    break;
    
    case "delwarp":
    if($g->hasPermission("delwarp.command")){
    $warpName = implode(" ", $args);
    if(empty($args[0])){
    if(!empty($this->config->get($warpName))){
    
    $this->config->remove($warpName);
    $this->config->save();
    $g->sendMessage("§f» §a{$warpName} §7Adındaki Warp Silindi!");
    }else $g->sendMessage("§f» §c{$warpName} §7Adında Bir Warp Yok!");
    }else $g->sendMessage("§f» §cKullanım: §f/delwarp [warp:string]");
    }else $g->sendMessage("§f» §cBu Komutu Kullanmaya Yetkin Yok!");
    break;
    }
    return true;
  }
  
  public function setWarp(string $warpName, int $x, int $y, int $z, string $levelName){
    $this->config->setNested($warpName.".getX", $x);
    $this->config->setNested($warpName.".getY", $y);
    $this->config->setNested($warpName.".getZ", $z);
    $this->config->setNested($warpName.".getLevel", $levelName);
    $this->config->save();
  }
  
  public function getWarp($warpName){
    $x = $this->config->getNested($warpName.".getX");
    $y = $this->config->getNested($warpName.".getY");
    $z = $this->config->getNested($warpName.".getZ");
    $level = $this->config->getNested($warpName.".getLevel");
    $position = new Vector3((int)$x, (int)$y, (int)$z, $level);
    return $position;
  }
  
}