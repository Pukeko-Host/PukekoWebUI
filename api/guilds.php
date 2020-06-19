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
				return json_encode($this->show_guilds(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			break;
			case "guild":
				switch(count($ctx->params)){
					case 3:
						return json_encode($this->show_guild($ctx->params[2]), JSON_PRETTY_PRINT| JSON_UNESCAPED_SLASHES);
					break;
					case 5:
						switch($ctx->params[3]){
							case 'gameservers':
								if($ctx->params[4] == 'active' || $ctx->params[4] == 'archived'){
									if(isset($this->list[$ctx->params[2]])){
										$target = $this->list[$ctx->params[2]];
										$target->Gameservers->conn = $this->conn;
										$target->Gameservers->get_gameservers($target->Id);
										return json_encode($target->Gameservers->show_gameservers($ctx->params[4]), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
									}
									else return specific_error(UNKNOWN_REQUEST, "Unable to find a guild by the id '".$ctx->params[2]."'!");
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
		unset($result['Gameservers']);
		return $result;
	}
	
	function get_userguilds($userid, $apiguilds){
		$defaultpos = 1;
		foreach($apiguilds as &$apiguild){
			$this->conn->query("CALL AddGuild($apiguild->id, $userid)");
			$this->list[$apiguild->id] = new guild($this->conn, $apiguild->name, $apiguild->id, $apiguild->icon, $defaultpos);
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
					if(!is_null($row['Pos'])) $this->list[$key]->Pos = $row['Pos'];
					$this->list[$key]->FolderParent = $row['GuildFolder'];
				}
			}
		}
	}
}
class guild {
	function __construct($conn, $name="Failed to load your guilds", $id=-1, $icon="", $pos=100, $valid=true)
	{
		$this->conn = $conn;
		$this->Name = $name;
		$this->Id = $id;
		$this->Icon = "https://cdn.discordapp.com/icons/".$id.'/'.$icon.'.png';
		$this->Pos = $pos;
		$this->FolderParent = null;
		$this->Gameservers = new gameservers($conn);
		$this->Valid = $valid;
	}
}
?>