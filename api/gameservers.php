<?php
require_once(dirname(__DIR__)."/api/api.php");
require_once(dirname(__DIR__)."/api/error.php");
require_once(dirname(__DIR__)."/api/games.php");

class gameservers extends Handler {
  function __construct($conn){
    $this->conn = $conn;
    $this->list = ['active'=>[], 'archived'=>[]];
  }
  
  function show_gameservers($active){
    $results = [];
    foreach($this->list[$active=='active'?'active':'archived'] as $gameserver){
      $results[] = (array)$gameserver;
      unset($results[count($results)-1]['conn']);
    }
    return $results;
  }

  function get_gameservers($guildid){
    $result = $this->conn->query("SELECT gameserver.*
                                  FROM gameserver
                                  WHERE GuildId = $guildid
                                  GROUP BY Id
                                  ORDER BY GuildId ASC, Active DESC");
    
    if(!$result){
      specific_error(SERVER_ERROR, "Failed to get gameservers; ".$guildid);//$this->conn->error);
    }
    $this->list = ['active'=>[], 'archived'=>[]];
    while($row = $result->fetch_assoc()){
      $this->list[($row['Active']?'active':'archived')][]=new gameserver($this->conn, $row['Id'], $row['Name'], $row['Active'], $row['Running'], $row['RemainingMinutes'], $row['GameId'], $row['TierId'], $row['GMSId']);
    }
  }
}

class gameserver {
  function __construct($conn, $id, $name, $active, $running, $minutes, $gameid, $tierid, $gsmsid){
    $this->conn = $conn;
    $this->Id = $id;
    $this->Name = $name;
    $this->State = ($running?'running':($active?'active':'archived'));
    $this->Minutes = $minutes;
    
    $games = new games($conn);
    $this->Game = $games->get_game($gameid);
    $this->Tier = $games->get_game_tier($gameid, $tierid);
    $this->GSMS = $games->get_game_gsms($gameid, $gsmsid);
  }
}
?>