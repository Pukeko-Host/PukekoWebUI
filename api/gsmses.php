<?php
require_once(dirname(__DIR__)."/api/error.php");

class gsmses {
	function __construct($conn, $ctx=null)
	{
		$this->ctx = $ctx;
		$this->conn = $conn;
	}
	
	function run()
	{
		switch($this->ctx->params[0]){
			case "gsmses":
				switch($this->ctx->method){
					case 'GET':
						return json_encode($this->get_gsmses());
					break;
					default:
						return generic_error(UNKNOWN_REQUEST);
				}
			break;
			case "gsms":
				switch($this->ctx->method){
					case "GET":
						if(count($this->ctx->params)>1)
							return json_encode($this->get_gsms($this->ctx->params[1]));
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
	
	function get_gsmses(){
		$result = $this->conn->query("SELECT * FROM gsms");
		if(!$result){
			specific_error(SERVER_ERROR, $result->error);
		}
		$resultobject = array();
		while($row = $result->fetch_assoc()){
			$resultobject[]=$row;
		}
		return $resultobject;
	}
	
	function get_gsms($id){
		$id = intval($id);
		$result = $this->conn->query("SELECT * FROM gsms WHERE Id = $id");
		
		if(!$result){
			specific_error(SERVER_ERROR, $result->error);
		}
		return $result->fetch_assoc();
	}
}

?>