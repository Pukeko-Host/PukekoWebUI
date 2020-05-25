<?php
require_once(dirname(__DIR__)."/api/error.php");

class games {
	function __construct($conn)
	{
		$this->conn = $conn;
	}
	
	function resolve($ctx)
	{
		switch($ctx->params[0]){
			case "games":
				switch($ctx->method){
					case 'GET':
						return json_encode($this->get_games());
					break;
					default:
						return generic_error(UNKNOWN_REQUEST);
				}
			break;
			case "game":
				switch($ctx->method){
					case "GET":
						if(count($ctx->params)==2)
							return json_encode($this->get_game($ctx->params[1]));
						elseif(count($ctx->params)==3){
							switch($ctx->params[2]){
								case "gsmses":
									return json_encode($this->get_game_gsmses($ctx->params[1]));
								break;
								case "tiers":
									return json_encode($this->get_game_tiers($ctx->prarms[1]));
								break;
								default:
									return generic_error(UNKNOWN_REQUEST);
								break;
							}
						}
						else
							return generic_error(VALIDATION_ERROR);
					break;
					default:
						return generic_error(UNKNOWN_REQUEST);
				}
			break;
			default:
				return generic_error(UNKNOWN_REQUEST);
		}
	}
	
	function get_games(){
		$result = $this->conn->query("SELECT * FROM game");
		if(!$result){
			specific_error(SERVER_ERROR, $result->error);
		}
		$resultobject = array();
		while($row = $result->fetch_assoc()){
			$resultobject[]=$row;
		}
		return $resultobject;
	}
	
	function get_game($id=null, $name=null){
		if(!is_null($id)){
			$id = intval($id);
			$result = $this->conn->query("SELECT * FROM game WHERE Id = $id");
		}
		if(!is_null($name)){
			$name = $this->conn->real_escape_string($name);
			$result = $this->conn->query("SELECT * FROM game WHERE API = \"$name\"");
		}
		if(!$result){
			specific_error(SERVER_ERROR, $result->error);
		}
		return $result->fetch_assoc();
	}
	
	function get_game_gsmses($id){
		$id = intval($id);
		$result = $this->conn->query("SELECT gsms.* FROM gamesupport LEFT JOIN gsms ON gamesupport.ServerId = gsms.Id  WHERE gamesupport.GameId = $id");
		
		if(!$result){
			specific_error(SERVER_ERROR, $result->error);
		}
		$resultobject = array();
		while($row = $result->fetch_assoc()){
			$resultobject[]=$row;
		}
		return $resultobject;
	}
	
	function get_game_tiers($id){
		$id = intval($id);
		$result = $this->conn->query("SELECT gametier.* FROM gametier WHERE GameId = $id ORDER BY TierNumber ASC");
		
		if(!$result){
			specific_error(SERVER_ERROR, $result->error);
		}
		$resultobject = array();
		while($row = $result->fetch_assoc()){
			$resultobject[]=$row;
		}
		return $resultobject;
	}
}

?>