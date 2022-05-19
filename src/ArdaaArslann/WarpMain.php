<?php

namespace ArdaaArslann;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\{World, Position};
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use ArdaaArslann\{WarpCommand};

class WarpMain extends PluginBase {
  
  public static $instance;
  public function onLoad() : void
   {
  static::$instance = $this;
  }
  public static function getInstance(): WarpMain
   {
  return self::$instance;
  }
  
  public function onDisable() : void
   {
  $this->getLogger()->info($this->config->get("messages")["warp.disable"]);
  }
  
  public function onEnable(): void
   {
    $this->saveResource("config.yml");
    $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
    if($this->config->get("database") != "sqlite3" or $this->config->get("database") != "yaml"){
    $this->config->set("sqlite3");
    $this->config->save();
    $this->getLogger()->info("Database loaded.");
    }
    if($this->config->get("show.players") or !$this->config->get("shop.players")){
    $this->config->set("show.players", true);
    $this->config->save();
    $this->getLogger()->info("Settings loaded.");
    }
    
    $this->sqlite = new \SQLite3($this->getDataFolder()."warps.sqlite3");
    $this->sqlite->exec("CREATE TABLE IF NOT EXISTS warps(warpName TEXT NOT NULL, x INT NOT NULL, y INT NOT NULL, z INT NOT NULL, world TEXT NOT NULL)");
    $this->warp = new Config($this->getDataFolder()."warps.yml");
    $this->getServer()->getCommandMap()->register("warp", new WarpCommand($this));
    $this->getLogger()->info($this->config->get("messages")["warp.enable"]);
  }
  
  public function getWarp($warpName): bool
   {
    if($this->config->get("database") == "yaml"){
    if($this->warp->get($warpName)){return true;}else{return false;}
    }elseif($this->config->get("database") == "sqlite3"){
    $sql = $this->sqlite->query("SELECT * FROM warps WHERE warpName='$warpName'");
    if($sql->fetchArray(SQLITE3_ASSOC) === false){return false;}else{return true;}
    }
    return false;
  }
  
  public function setWarp($warpName, $x, $y, $z, $world):bool
   {
    if($this->config->get("database") == "yaml"){
    if($this->getWarp($warpName) === false){
    $this->warp->set($warpName, $x.":".$y.":".$z.":".$world);
    $this->warp->save();
    return true;
    }else return false;
    }elseif($this->config->get("database") == "sqlite3"){
    if($this->getWarp($warpName) === false){
    $this->sqlite->exec("INSERT INTO warps(warpName, x, y, z, world) VALUES ('$warpName', '$x', '$y', '$z', '$world')");
    return true;
    }else return false;
    }
   return false;
   }
   
  public function delWarp($warpName): bool
   {
    if($this->config->get("database") == "yaml"){
    if($this->getWarp($warpName) === true){
    $this->warp->remove($warpName);
    $this->warp->save();
    }else return false;
    }elseif($this->config->get("database") == "sqlite3"){
    if($this->getWarp($warpName) === true){
    $this->sqlite->exec("DELETE FROM warps WHERE warpName='$warpName'");
    return true;
    }else return false;
    }
    return false;
  }
  
  public function listWarp()
   {
   $list = "";
   $i = 0;
   if($this->config->get("database") == "yaml"){
   foreach($this->warp->getAll(true) as $key){
   $list .= str_replace("{warp_name}", $key, $this->config->get("messages")["warp.list.output"]);
   $i++;
   }
   if($i > 0){return $list;}else{return false;}
   }elseif($this->config->get("database") == "sqlite3"){
   $sql = $this->sqlite->query("SELECT * FROM warps");
   while($result = $sql->fetchArray(SQLITE3_ASSOC)){
   $list .= str_replace("{warp_name}", $result["warpName"], $this->config->get("messages")["warp.list.output"]);
   $i++;
   }
   if($i > 0){return $list;}else{return false;}
   }
   return false;
  }
  
  public function getWarps(): ?array
   {
   $list = [];
   if($this->config->get("database") == "yaml"){
   foreach($this->warp->getAll() as $key => $value){
   $list[$key] = $value;
   }
   if(is_null($list)){return $list = [];}else{return $list;}
   }elseif($this->config->get("database") == "sqlite3"){
   $sql = $this->sqlite->query("SELECT * FROM warps");
   while($result = $sql->fetchArray(SQLITE3_ASSOC)){
   if($result !== null){
   $list[$result["warpName"]] = $result["x"].":".$result["y"].":".$result["z"].":".$result["world"];
   }
   }
   if(is_null($list)){return $list = [];}else{return $list;}
   }
   return false;
  }
  
  public function goWarp($g, $x, $y, $z, $world): bool
   {
   if(!file_exists($this->getServer()->getDataPath()."worlds/".$world)){
    return false;
    }else{
    $position = new Vector3((int)$x, (int)$y, (int)$z);
    $this->getServer()->getWorldManager()->loadWorld($world);
    $g->teleport($this->getServer()->getWorldManager()->getWorldByName($world)->getSpawnLocation());
    $g->teleport($position);
    return true;
    }
   }
}
