<?php
require_once(dirname(__DIR__)."/api/error.php");
require_once(dirname(__DIR__)."/api/games.php");

class gameservers {
  function __construct($conn){
    $this->conn = $conn;
    $this->list = ['active'=>[], 'archived'=>[]];
  }

  function resolve($ctx){
    return generic_error(UNKNOWN_REQUEST);
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
      specific_error(SERVER_ERROR, "Failed to get gameservers; ".$this->conn->error);
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
    $this->id = $id;
    $this->name = $name;
    $this->state = ($active?'active':($running?'running':'archived'));
    $this->minutes = $minutes;
    
    $games = new games($conn);
    $this->game = $games->get_game($gameid);
    $this->tier = $games->get_game_tier($gameid, $tierid);
    $this->gsms = $games->get_game_gsms($gameid, $gsmsid);
  }
}
?>