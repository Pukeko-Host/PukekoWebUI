<?php
require_once(dirname(__DIR__)."/api/api.php");
require_once(dirname(__DIR__)."/api/error.php");
require_once(dirname(__DIR__)."/api/guilds.php");
// Authorize with discord (define('OAUTH2_CLIENT_ID') and define('OAUTH2_CLIENT_SECRET'))
require_once(__DIR__."/../../takahe.discord.php");

ini_set('max_execution_time', 300);

define("CALLBACK_URL", "https://pukeko.yiays.com/api/account/callback/");
define("API_URL", "https://discord.com/api");

class account extends Handler {
	function __construct($conn)
	{
		$this->conn = $conn;
		
		$this->logged_in = false;
		$this->token = null;
		$this->return = null;
		
		$this->Id = 0;
		$this->DiscordId = 0;
		$this->Username = "Not logged in";
		$this->Discriminator = null;
		$this->Email = "";
		$this->Avatar = "";
		$this->Verified = false;
		$this->MFA = false;
		$this->Locale = "en-US";
		$this->Guilds = new guilds($conn);
		$this->Balance = 0.00;
	}
	
	function resolve($ctx)
	{
		if(count($ctx->params)==1){
			$result = (array)$this;
			unset($result['conn']);
			unset($result['token']);
			unset($result['return']);
			unset($result['Guilds']);
			return json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}
		switch($ctx->params[1]){
			case 'guild':
			case 'guilds':
				$this->Guilds->conn = $this->conn;
				return $this->Guilds->resolve($ctx);
			break;
			case 'login':
				return json_encode($this->login(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			break;
			case 'app_login':
				if($ctx->method!='POST') return generic_error(UNKNOWN_REQUEST);
				if(!isset($_POST['access_token'])) return specific_error(VALIDATION_ERROR, http_build_query($_POST));
				return json_encode($this->app_login($_POST['access_token']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			break;
			case 'logout':
				return json_encode($this->logout(), JSON_PRETTY_PRINT);
			break;
			case 'callback':
				$result = $this->login_callback(get('code'));
				if(isset($result['redirect'])){
					header("Location: $result[redirect]");
				}else{
					print(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
				}
			break;
			default:
				return generic_error(UNKNOWN_REQUEST);
		}
	}
	
	function login($return='/account/', $username=null, $password=null){
		if($this->logged_in) specific_error(VALIDATION_ERROR, "You're already logged in!");
		
		$this->return = $return;
		
		$params = array(
			'client_id' => OAUTH2_CLIENT_ID,
			'redirect_url' => CALLBACK_URL,
			'response_type' => 'code',
			'scope' => 'identify email guilds'
		);
		
		return ['redirect'=>API_URL."/oauth2/authorize?".http_build_query($params)];
	}
	
	function login_callback($code){
		if($this->logged_in) return ['redirect'=>"/account/"];
		
		if(is_null($code)) specific_error(UNAUTHORIZED, "A return code from discord is required to continue. Try logging in again.");
		
		$response = $this->apiRequest(API_URL."/oauth2/token", array(
			"grant_type" => "authorization_code",
			"client_id" => OAUTH2_CLIENT_ID,
			"client_secret" => OAUTH2_CLIENT_SECRET,
			"redirect_uri" => CALLBACK_URL,
			"code" => $code
		));
		
		if(isset($response->access_token)){
			$this->token = $response->access_token;
			
			if($this->token){
				$this->logged_in = true;
				$this->get_discord_user();
				$this->get_discord_guilds();
			}
			
			return ['redirect'=>$this->return];
		}else{
			specific_error(VALIDATION_ERROR, (array)$response);
		}
	}
	
	function app_login($token){
		//TODO: Check if token is valid before storing it
		
		$this->token = $token;
		
		$this->logged_in = true;
		
		$this->get_discord_user();
		$this->get_discord_guilds();
		
		return ['desc'=>"Logged in!"];
	}
	
	function logout(){
		if(!$this->logged_in) specific_error(VALIDATION_ERROR, "Already logged out!");
		
		if(!is_null($this->token)) $this->apiRequest(API_URL."/oauth2/token/revoke", ['access_token'=>$this->token]);
		
		$this->__construct($this->conn); // Reset the account object
		
		return ['msg'=>"Logged out!"];
	}
	
	function get_discord_user(){
		$response = $this->apiRequest(API_URL."/users/@me");
		$this->DiscordId = $response->id;
		$this->Username = $response->username;
		$this->Discriminator = $response->discriminator;
		$this->Email = $response->email;
		$this->Avatar = "https://cdn.discordapp.com/avatars/$this->DiscordId/$response->avatar.jpg?size=256";
		$this->Verified = $response->verified;
		$this->MFA = $response->mfa_enabled;
		$this->Locale = $response->locale;
		
		// Sync results with database
		$result = $this->conn->query("INSERT pukekohost.user(DiscordId, Username, Discriminator, Email) VALUES ($this->DiscordId, \"".$this->conn->escape_string($this->Username)."\", $this->Discriminator, \"".$this->conn->escape_string($this->Email)."\") ON DUPLICATE KEY UPDATE Username = VALUES(Username), Discriminator = VALUES(Discriminator), Email = VALUES(Email)");
		if(!$result){
			$this->logout();
			specific_error(SERVER_ERROR, "Failed to record your login in the database, please try again later.");
		}
		$result = $this->conn->query("SELECT Id FROM user WHERE DiscordId = $this->DiscordId");
		$this->Id = $result->fetch_array()[0][0];
	}
	
	function get_discord_guilds(){
		$response = $this->apiRequest(API_URL."/users/@me/guilds"); //TODO: Error handling
		$this->Guilds->conn = $this->conn;
		$this->Guilds->get_userguilds($this->DiscordId, $response);
	}
}

function get($key, $default=NULL) {
	return array_key_exists($key, $_GET) ? (empty($_GET[$key]) ? True : $_GET[$key]) : $default;
}

?>