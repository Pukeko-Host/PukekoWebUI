<?php
require_once(dirname(__DIR__)."/api/error.php");
// Authorize with discord (define('OAUTH2_CLIENT_ID') and define('OAUTH2_CLIENT_SECRET'))
require_once(__DIR__."/../../takahe.discord.php");

ini_set('max_execution_time', 300);

define("CALLBACK_URL", "https://pukeko.yiays.com/api/account/callback/");
define("API_URL", "https://discordapp.com/api");

class account {
	function __construct($conn)
	{
		$this->conn = $conn;
		
		$this->logged_in = false;
		$this->token = null;
		$this->return = null;
		
		$this->id = 0;
		$this->discordId = 0;
		$this->username = "Not logged in";
		$this->discriminator = "";
		$this->email = "";
		$this->avatar = "";
		$this->verified = false;
		$this->mfa = false;
		$this->locale = "en-US";
		$this->guilds = [];
		$this->balance = 0.00;
	}
	
	function resolve($ctx)
	{
		if(count($ctx->params)==1){
			return json_encode([
				'id'=>$this->id,
				'discordId'=>$this->discordId,
				'username'=>$this->username,
				'discriminator'=>$this->discriminator,
				'email'=>$this->email,
				'avatar'=>$this->avatar,
				'verified'=>$this->verified,
				'mfa'=>$this->mfa,
				'locale'=>$this->locale,
				'balance'=>$this->balance
			]);
		}
		switch($ctx->params[1]){
			case "guilds":
				return json_encode($this->guilds);
			break;
			case "login":
				return json_encode($this->login());
			break;
			case "logout":
				return json_encode($this->logout());
			break;
			case "callback":
				$result = $this->login_callback(get('code'));
				if(isset($result['redirect'])){
					header("Location: $result[redirect]");
				}else{
					print(json_encode($result));
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
		
			$this->logged_in = true;
			
			if($this->token){
				$this->get_discord_user();
				$this->get_discord_guilds();
			}
			
			return ['redirect'=>$this->return];
		}else{
			return $response;
			//specific_error(VALIDATION_ERROR, "This login has expired.");
		}
	}
	
	function logout(){
		if(!$this->logged_in) specific_error(VALIDATION_ERROR, "Already logged out!");
		
		if(!is_null($this->token)) $this->apiRequest(API_URL."/oauth2/token/revoke", ['access_token'=>$this->token]);
		
		$this->__construct($this->conn); // Reset the account object
		
		return ['msg'=>"Logged out!"];
	}
	
	function get_discord_user(){
		$response = $this->apiRequest(API_URL."/users/@me");
		$this->discordId = $response->id;
		$this->username = $response->username;
		$this->discriminator = $response->discriminator;
		$this->email = $response->email;
		$this->avatar = "https://cdn.discordapp.com/avatars/$this->discordId/$response->avatar.jpg?size=256";
		$this->verified = $response->verified;
		$this->mfa = $response->mfa_enabled;
		$this->locale = $response->locale;
		
		// Sync results with database
		$result = $this->conn->query("CALL AddUser($this->discordId, \"".$this->conn->escape_string($this->username)."\", $this->discriminator, \"".$this->conn->escape_string($this->email)."\", @UserId)");
		if(!$result){
			$this->logout();
			specific_error(SERVER_ERROR, "Failed to record your login in the database, please try again later.");
		}
		$result = $this->conn->query("SELECT @UserId");
		$this->id = $result->fetch_array()[0][0];
	}
	
	function get_discord_guilds(){
		$response = $this->apiRequest(API_URL."/users/@me/guilds"); //TODO: Error handling
		$this->guilds = $response;
		foreach($response as &$guild){
			$this->conn->query("CALL AddGuild($guild->id, $this->discordId)");
		}
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

function get($key, $default=NULL) {
	return array_key_exists($key, $_GET) ? (empty($_GET[$key]) ? True : $_GET[$key]) : $default;
}

?>