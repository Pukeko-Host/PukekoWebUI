<?php
require_once(dirname(__DIR__)."/api/games.php");
require_once(dirname(__DIR__)."/api/gsmses.php");
require_once(dirname(__DIR__)."/api/account.php");

// API response framework
require_once(dirname(__DIR__)."/api/error.php");
require_once(dirname(__DIR__)."/../takahe.conn.php");

session_start();

if(!isset($_SESSION['account'])) $_SESSION['account'] = serialize(new account($conn));
$account = unserialize($_SESSION['account']);
$account->conn = $conn;

// Requests provide context to Handlers
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

// Handlers are extended by modules in files in this directory
abstract class Handler {
    function __construct(mysqli $conn){
        $this->conn = $conn;
    }

    function resolve(Request $ctx){
        return generic_error(UNKNOWN_REQUEST);
    }
	
	function apiRequest($url, $post=FALSE, $headers=array()) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if($post){
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		}
		$headers[] = 'Accept: application/json';
		if(isset($this->token))
			$headers[] = "Authorization: Bearer $this->token";
		array_push($headers,"Content-Type: application/x-www-form-urlencoded");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		return json_decode($response);
	}
}

// All api requests are sent here (thanks to .htaccess)
// Only send a response if this file is master (not included by another file)
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    // Universal headers for any API response
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *"); // This is for debug, on release this should be *.yiays.com
    
    $ctx = new Request($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

    if(count($ctx->params)>0){
        switch($ctx->params[0]){
            case "test": //api/test/
                http_response_code(VALID_RESPONSE);
                print(json_encode(['desc' => $ctx->method], JSON_PRETTY_PRINT));
            break;
            case "games": //api/games/
            case "game": //api/game/{id}/
                $games = new games($conn);
                print($games->resolve($ctx));
            break;
            case "gsmses": //api/gsmses/
            case "gsms": //api/gsms/{id}/
                $gsmses = new gsmses($conn);
                print($gsmses->resolve($ctx));
            break;
            case "gameservers": //api/gameservers/
            case "gameserver": //api/gameserver/{id}/
                $gameservers = new gameservers($conn);
                print($gameservers->resolve($ctx));
            break;
            case "account": //api/account/
                print($account->resolve($ctx));
                $_SESSION['account'] = serialize($account);
            break;
            default:
                generic_error(UNKNOWN_REQUEST);
        }
    }else{
        print(json_encode(['desc'=>"Welcome to PukekoAPI!",
        'queries'=>[
            'GET /games/',
            'GET /game/{id}/',
            'GET /game/{id}/gsmses/',
            'GET /game/{id}/tiers/',
            'GET /game/{apiname}/',
            'GET /gsmses/',
            'GET /gsms/{id}/',
            'GET /gsms/{id}/gameservers/',
            'GET /account/',
            'GET /account/login/',
            'GET /account/logout/',
            'GET /account/guilds/',
            'GET /account/guild/{id}/',
            'GET /account/guild/{id}/gameservers/active/',
            'GET /account/guild/{id}/gameservers/archived/',
        ]], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
    $conn->close();
}
?>