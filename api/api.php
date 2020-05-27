<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // This is for debug, on release this should be *.yiays.com

require_once("games.php");
require_once("gsmses.php");
require_once("account.php");

session_start();

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
                $games = new games($conn);
                print($games->resolve($ctx));
            break;
            case "gsmses":
            case "gsms":
                $gsmses = new gsmses($conn);
                print($gsmses->resolve($ctx));
            break;
            case "account":
                if(!isset($_SESSION['account'])){
                    $_SESSION['account'] = new account($conn);
                }else{
                    $_SESSION['account']->conn = $conn;
                }
                print($_SESSION['account']->resolve($ctx));
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
            'GET /gsms/{id}/',
            'GET /account/',
            'GET /account/login/',
            'GET /account/logout/',
            'GET /account/guilds/'
        ]]));
    }
    $conn->close();
}
?>