<?php
$params = explode('/', substr($_SERVER['REQUEST_URI'], 6));
$apiname = count($params)>0? $params[0]: NULL;
$gsms = count($params)>1? $params[1]: NULL;
$tier = count($params)>2? $params[2]: NULL;
$remainingdeals = NULL;

if(!isset($apiname)){
  header("Location: /", true, 303);
  die();
}

require_once('../../takahe.conn.php');
require_once('../api/games.php');

$gamesapi = new games($conn);
$game = $gamesapi->get_game(null, $apiname);
if(!$game || count($game)<1){
  header("Location: /", true, 303);
  die();
}

$gsmses = $gamesapi->get_game_gsmses($game['Id']);
$gsmsname = "";

$tiers = $gamesapi->get_game_tiers($game['Id']);
$tiername = "";

$title = $game['Name'];
$description = "Get a casual ".$game['Name']." server and pay by the hour when you want to hop on.\nLink the game server to a discord server and any member of the server can pay for an hour when they want to play, or everyone can pool together some change to keep the server running.";
$image = "https://pukeko.yiays.com".$game['Background'];
$tags = $game['Name'];
$subtitle = $game['Name']." Hosting";
require_once("../includes/header.php");
?>
<div class="wrapper main">
  <div class="slider">
    <div class="card full" style="background: #134FB0;background: linear-gradient(to left, #134FB0 0%,#30475D 100%);min-width: 50rem;text-align:left;">
      <?php
        echo "<div class=\"game card third\" style=\"margin: -1rem 1rem -1rem -1rem;float:left;\">";
        echo "  <div class=\"background\" style=\"background-image:url('$game[Background]');\">";
        echo "    <img class=\"foreground\" src=\"$game[Foreground]\" alt=\"$game[Name]\">";
        echo "  </div>";
        echo "  <div class=\"content\">";
        echo "    <h3>$game[Name]</h3>";
        echo "    <ul>".str_replace(" - ","<li>",str_replace("\n","</li>",$game['Perks']))."</li></ul>";
        echo "  </div>";
        echo "</div>";
        echo "<p class=\"dark\">".str_replace("\n","</p><p class=\"dark\">",$game['Description'])."</p>";
      ?>
    </div>
  </div>
  <hr>
  <h2>1. Select a server</h2>
  <p>Select a server with the best ping and specs for your needs. <i>Keep in mind this server may not be dedicated solely to you.</i></p>
  <div class="slider">
    <?php
      if(!$gsmses || count($gsmses)<1){
        echo "<p><b>All of our servers appear to be fully-booked for this game!</b></p>";
        echo "<p>Please check back later!</p>";
      }else{
        foreach($gsmses as $row){
          if($gsms == $row['Id']) $gsmsname = $row['DomainName'];
          echo "<div class=\"card third gsms".($gsms == $row['Id']? ' selected': '')."\">";
          echo "  <table style=\"min-height: 12rem;\">";
          echo "    <tr>";
          echo "      <td colspan=\"2\">";
          echo "        <b>".$row['DomainName']."</b>";
          echo "        <div class=\"float-right\">";
          echo "          &nbsp;| <b><span class=\"ping good\"></span>Ping:</b>";
          echo "          <span class=\"serverping\" data-server=\"".$row['DomainName']."\">Waiting...</span>";
          echo "        </div>";
          echo "        <label class=\"float-right\" for=\"nerdstats".$row['Id']."\">👓</label>";
          echo "        <input class=\"togglestats float-right\" type=\"checkbox\" id=\"nerdstats".$row['Id']."\">";
          echo "        <ul class=\"perks\">".str_replace(" - ","<li>",str_replace("\n","</li>",$row['Perks']))."</li></ul>";
          echo "        <ul class=\"specs\">".str_replace(" - ","<li>",str_replace("\n","</li>",$row['Specs']))."</li></ul>";
          echo "      </td>";
          echo "    </tr>";
          echo "    <tr height=\"3rem\">";
          echo "      <td width=\"50%\">";
          echo "        Setup: <span style=\"font-size: 2rem;\">$".sprintf("%.2f",$row['InitRate'])."</span> fee";
          echo "      </td>";
          echo "      <td>";
          echo "        Costs <span style=\"font-size: 2rem;\">".strval($row['UptimeRate']/0.05)."x</span> the regular rate";
          echo "      </td>";
          echo "    </tr>";
          echo "  </table>";
          echo "  <div class=\"footer\">";
          echo "    <div class=\"left\">";
          $result = $conn->query("SELECT gametier.TierNumber,gametier.Icon,COUNT(gameserverport.Port) AS IsAvailable
                                  FROM gametier
                                    INNER JOIN gameserverport ON gametier.GameId = gameserverport.GameId AND gametier.TierNumber = gameserverport.TierId
                                    LEFT JOIN gameserver ON gameserverport.Port = gameserver.Port AND gameserver.GMSId = ".$row['Id']."
                                  WHERE gametier.GameId = $game[Id]
                                    AND gameserver.Port IS NULL
                                  GROUP BY gametier.TierNumber"
                                );
          if(!$result){
            die($conn->error);
          }
          while($row2 = $result->fetch_assoc()){
            if($gsms == $row['Id'] && $tier == $row2['TierNumber']) $remainingdeals = $row2['IsAvailable'];
            echo "    <a href=\"/game/$apiname/".$row['Id']."/".$row2['TierNumber']."/\" class=\"btn\">";
            echo "      <img class=\"nointerpolate\" style=\"height:2em;margin:-0.5em;margin-right:0;\" src=\"".$row2['Icon']."\"> ".$row2['IsAvailable'];
            echo "    </a>";
          }
          echo "    </div>";
          echo "    <a href=\"/game/$apiname/".$row['Id']."/".($tier? $tier.'/': '')."\" class=\"btn\">Select</a>";
          echo "  </div>";
          echo "</div>";
        }
      }
    ?>
  </div>
  <h2>2. Select a tier</h2>
  <p>Select a tier of hosting that suits your needs, the higher the price, the less compromises. <i id="pricedisclaimer">Exact prices will vary depending on the server you select.</i></p>
  <div class="slider">
    <?php
      if(!$tiers || count($tiers)<1){
        echo "<p><b>Our tiered offerings for this game are still on their way.</b></p>";
        echo "<p>Please check back later!</p>";
      }else{
        foreach($tiers as $row){
          if($tier == $row['TierNumber']){
            $tiername = $row['Name'];
          }
          echo "<div class=\"card third tier".($tier == $row['TierNumber']? ' selected': '')."\">";
          echo "  <table style=\"min-height: 12rem;\">";
          echo "    <tr>";
          echo "      <td rowspan=\"2\" width=\"30%\" style=\"padding-right:1rem;text-align:center;\">";
          echo "        <b>".$row['Name']." Tier</b><br><br>";
          echo "        <img src=\"".$row['Icon']."\" style=\"width:100%;\" class=\"nointerpolate\" alt=\"Icon for the ".$row['Name']." tier\">";
          echo "      </td>";
          echo "      <td>";
          echo "        <ul>".str_replace(" - ","<li>",str_replace("\n","</li>",$row['Perks']))."</li></ul>";
          echo "      </td>";
          echo "    </tr>";
          echo "    <tr height=\"3rem\">";
          echo "      <td>";
          echo "        <span style=\"font-size: 2.5rem;\">~$".sprintf("%.2f",0.05*$row['PriceMultiplier'])."</span><span style=\"font-size: 1.5rem;margin-top:1.5rem;\"> / hour</span>";
          echo "      </td>";
          echo "    </tr>";
          echo "  </table>";
          echo "  <div class=\"footer\">";
          echo "    <a href=\"/game/$apiname/$gsms/".$row['TierNumber']."/\" class=\"btn\">Select</a>";
          echo "  </div>";
          echo "</div>";
        }
      }
    ?>
  </div>
</div>
<div class="overlay flex flex-center<?php if(!($apiname&&$gsms&&$tier)) echo ' hidden'; ?>">
  <div class="card half light<?php if(!$remainingdeals) echo ' hidden'; ?>">
    <div class="background themegradient dark" style="padding: 2rem; height: auto;">
      <a href="<?php echo "/game/$apiname/$gsms/"; ?>" class="close">&times;</a>
      <h1>Selection completed!</h1>
      <?php echo "$tiername Tier Hosting on $gsmsname"; ?>
    </div>
    <div class="content">
      <!--<p>Please <a href="/account/?login&return=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">Login</a> to continue.</p>-->
      <p>
        Here's what to do to continue...
        <ol>
          <li><a href="https://discordapp.com/oauth2/authorize?client_id=700549643045568522&scope=bot&permissions=67161088">Add Mr. Pukeko to the desired discord server</a> if it isn't there already. <i>(Ask an admin if it isn't yours!)</i></li>
          <li>Type in the following command in a channel associated with the game...<br>
            <code id="createserver">p/server create <?php echo "$apiname gsms-$gsms tier-$tier"; ?></code> <button class="js-only copy" data-for="createserver">&#x1f4cb;</button></li>
          <li>Follow the instructions provided by Mr. Pukeko from there.</li>
        </ol>
      </p>
    </div>
  </div>
  <div class="card half light<?php if($remainingdeals) echo ' hidden'; ?>">
    <div class="background themegradient-error dark" style="padding: 2rem; height: auto;">
      <a href="<?php echo "/game/$apiname/$gsms/"; ?>" class="close">&times;</a>
      <h1>This selection is unavailable.</h1>
    </div>
    <div class="content">
      <p>Unfortunately, this tier on this server is currently unavailable.</p>
      <p>You'll need to select either a different tier or a different server to continue.</p>
    </div>
  </div>
</div>
<?php
  $footerextra = '<script src="/js/pingserver.js?v=8"></script>';
  require_once("../includes/footer.php");
?>
