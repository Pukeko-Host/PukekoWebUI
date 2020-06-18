<?php
require_once(dirname(__DIR__)."/api/api.php");
require_once(dirname(__DIR__)."/api/error.php");
require_once(dirname(__DIR__)."/api/gameservers.php");

class guilds extends Handler {
	function __construct($conn)
	{
		$this->conn = $conn;
		$this->list = array();
	}
	
	function resolve($ctx){
		switch($ctx->params[1]){
			case "guilds":
				return json_encode($this->show_guilds(), JSON_PRETTY_PRINT);
			break;
			case "guild":
				switch(count($ctx->params)){
					case 3:
						return json_encode($this->show_guild($ctx->params[2]), JSON_PRETTY_PRINT);
					break;
					case 5:
						switch($ctx->params[3]){
							case 'gameservers':
								if($ctx->params[4] == 'active' || $ctx->params[4] == 'archived'){
									$target = $this->list[$ctx->params[2]];
									$target->gameservers->conn = $this->conn;
									$target->gameservers->get_gameservers($target->id);
									return json_encode($target->gameservers->show_gameservers($ctx->params[4]), JSON_PRETTY_PRINT);
								}
								else return generic_error(UNKNOWN_REQUEST);
							break;
							default:
								return generic_error(UNKNOWN_REQUEST);
							break;
						}
					break;
					default:
						return generic_error(UNKNOWN_REQUEST);
				}
			break;
			default:
				return generic_error(UNKNOWN_REQUEST);
		}
	}

	function show_guilds(){
		$result = [];
		foreach(array_values($this->list) as &$guild){
			$result[] = $this->show_guild(null, $guild);
		}
		return $result;
	}
	
	function show_guild($id=null, $guild=null){
		if(!is_null($id)) $result = (array)$this->list[$id];
		else $result = (array)$guild;
		unset($result['conn']);
		unset($result['gameservers']);
		return $result;
	}
	
	function get_userguilds($userid, $apiguilds){
		$defaultpos = 1;
		foreach($apiguilds as &$guild){
			$this->conn->query("CALL AddGuild($guild->id, $userid)");
			$this->list[$guild->id] = new guild($this->conn, $guild->name, $guild->id, $guild->icon, $defaultpos);
			$defaultpos += 1;
		}
		$result = $this->conn->query("SELECT guildId,Pos,guildFolder FROM userguild WHERE userId = $userid");
		if(!$result){
			$this->list[] = new guild($this->conn, "Failed to fetch guild order!", -1, "", 0, false);
			return;
		}
		while($row = $result->fetch_assoc()){
			foreach($this->list as $key => $guild){
				if($row['guildId'] == $key){
					if(!is_null($row['Pos'])) $this->list[$key]->pos = $row['Pos'];
					$this->list[$key]->folderparent = $row['guildFolder'];
				}
			}
		}
	}
}
class guild {
	function __construct($conn, $name="Failed to load your guilds", $id=-1, $icon="", $pos=100, $valid=true)
	{
		$this->conn = $conn;
		$this->name = $name;
		$this->id = $id;
		$this->icon = "https://cdn.discordapp.com/icons/".$id.'/'.$icon.'.png';
		$this->pos = $pos;
		$this->folderparent = null;
		$this->gameservers = new gameservers($conn);
		$this->valid = $valid;
	}
}
?>