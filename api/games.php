<?php
require_once(dirname(__DIR__)."/api/error.php");

class games {
	function __construct($conn, $ctx=null)
	{
		$this->ctx = $ctx;
		$this->conn = $conn;
	}
	
	function run()
	{
		switch($this->ctx->params[0]){
			case "games":
				switch($this->ctx->method){
					case 'GET':
						return json_encode($this->get_games());
					break;
					default:
						return generic_error(UNKNOWN_REQUEST);
				}
			break;
			case "game":
				switch($this->ctx->method){
					case "GET":
						if(count($this->ctx->params)>1)
							return json_encode($this->get_game($this->ctx->params[1]));
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
}

?>