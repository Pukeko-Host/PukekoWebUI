<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // This is for debug, on release this should be *.yiays.com

// API response framework
require_once(dirname(__DIR__)."/api/error.php");
require_once(dirname(__DIR__)."/../takahe.conn.php");

class Request {
    function __construct($url, $method)
    {
        // URL must start with '/api/'
        if(substr($url, 0, 5) == "/api/"){
            $this->url = $url;
            $this->method = $method;
            $this->params = explode('/', substr(strtolower($url), 5));
            if(end($this->params) == '') array_pop($this->params);
        }else{
            throw new Exception('Invalid api path.');
        }
    }
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $ctx = new Request($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

    if(count($ctx->params)>0){
        switch($ctx->params[0]){
            case "test":
                http_response_code(VALID_RESPONSE);
                print(json_encode(['desc' => $ctx->method]));
            break;
            case "games":
            case "game":
                require_once("games.php");
                $games = new games($conn, $ctx);
                print($games->run());
            break;
            case "gsmses":
            case "gsms":
                require_once("gsmses.php");
                $gsmses = new gsmses($conn, $ctx);
                print($gsmses->run());
            break;
            default:
                generic_error(UNKNOWN_REQUEST);
        }
    }else{
        print(json_encode(['desc'=>"Welcome to PukekoAPI!",
        'queries'=>[
            'GET /games/',
            'GET /game/{id}/',
            'GET /game/{id}/gsmses',
            'GET /game/{id}/tiers',
            'GET /game/{apiname}/',
            'GET /gsmses/',
            'GET /gsms/{id}/'
        ]]));
    }
    $conn->close();
}
?>