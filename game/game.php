<?php
if(!isset($_GET['game'])){
  header("Location: /", true, 303);
  die();
}

require_once('../../takahe.conn.php');

$api = $conn->escape_string($_GET['game']);
$result = $conn->query("SELECT * FROM game WHERE API = \"$api\"");
if(!$result || $result->num_rows<1){
  header("Location: /", true, 303);
  die();
}

$row = $result->fetch_assoc();
$gameId = $row['Id'];

$title = $row['Name'];
$description = "Get a casual ".$row['Name']." server and pay by the hour when you want to hop on.\nLink the game server to a discord server and any member of the server can pay for an hour when they want to play, or everyone can pool together some change to keep the server running.";
$image = "https://pukeko.yiays.com".$row['Background'];
$tags = $row['Name'];
$subtitle = $row['Name']." Hosting";
require_once("../includes/header.php");
?>
  <div class="wrapper main">
    <div class="slider">
      <div class="card full" style="background: #134FB0;background: linear-gradient(to left, #134FB0 0%,#30475D 100%);min-width: 50rem;text-align:left;">
        <?php
          echo "<div class=\"game card third\" style=\"margin: -1rem 1rem -1rem -1rem;float:left;\">";
          echo "  <div class=\"background\" style=\"background-image:url('".$row['Background']."');\">";
          echo "    <img class=\"foreground\" src=\"".$row['Foreground']."\" alt=\"".$row['Name']."\">";
          echo "  </div>";
          echo "  <div class=\"content\">";
          echo "    <h3>".$row['Name']."</h3>";
          echo "    <ul>".str_replace(" - ","<li>",str_replace("\n","</li>",$row['Perks']))."</li></ul>";
          echo "  </div>";
          echo "</div>";
          echo "<p class=\"dark\">".str_replace("\n","</p><p class=\"dark\">",$row['Description'])."</p>";
        ?>
      </div>
    </div>
    <hr>
    <h2>1. Select a server</h2>
    <p>Select a server with the best ping and specs for your needs. <i>Keep in mind this server may not be dedicated solely to you.</i></p>
    <div class="slider">
      <?php
        $result = $conn->query("SELECT * FROM gms LEFT JOIN gamesupport ON gms.Id = gamesupport.ServerId WHERE gamesupport.GameId = ".$gameId);
        if(!$result || $result->num_rows<1){
          echo "<p><b>All of our servers appear to be fully-booked for this game!</b></p>";
          echo "<p>Please check back later!</p>";
        }else{
          while($row = $result->fetch_assoc()){
            echo "<div class=\"card third gsms\">";
            echo "  <table style=\"min-height: 12rem;\">";
            echo "    <tr>";
            echo "      <td colspan=\"2\">";
            /*echo "        <div class=\"card float-right\" style=\"width:4rem;margin:0;\">";
            echo "          <b><span class=\"ping good\"></span>Ping:</b><br>";
            echo "          <span class=\"serverping\" data-server=\"".$row['DomainName']."\">Waiting...</span>";
            echo "        </div>";*/
            echo "        <b>".$row['DomainName']."</b>";
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
            $result = $conn->query("SELECT gametier.Icon,COUNT(gameserverport.Port) AS IsAvailable
                                    FROM gametier
                                      INNER JOIN gameserverport ON gametier.GameId = gameserverport.GameId AND gametier.TierNumber = gameserverport.TierId
                                      LEFT JOIN gameserver ON gameserverport.Port = gameserver.Port AND gameserver.GMSId = ".$row['Id']."
                                    WHERE gametier.GameId = $gameId
                                      AND gameserver.Port IS NULL
                                    GROUP BY gametier.TierNumber"
                                  );
            if(!$result){
              die($conn->error);
            }
            while($row2 = $result->fetch_assoc()){
              echo "    <a class=\"btn\"><img class=\"nointerpolate\" style=\"height:100%;\" src=\"".$row2['Icon']."\"> ".$row2['IsAvailable']."</a>";
            }
            echo "    </div>";
            echo "    <a class=\"btn\">Select</a>";
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
        $result = $conn->query("SELECT * FROM gametier WHERE gameId = ".$gameId." ORDER BY TierNumber ASC");
        if(!$result || $result->num_rows<1){
          echo "<p><b>Our tiered offerings for this game are still on their way.</b></p>";
          echo "<p>Please check back later!</p>";
        }else{
          while($row = $result->fetch_assoc()){
            echo "<div class=\"card third\">";
            echo "  <table style=\"min-height: 12rem;\">";
            echo "    <tr>";
            echo "      <td rowspan=\"2\" width=\"30%\">";
            echo "        <b>".$row['Name']." Tier</b>";
            echo "        <img src=\"".$row['Icon']."\" style=\"width:100%;\" class=\"nointerpolate\" alt=\"Icon for the ".$row['Name']." tier\">";
            echo "      </td>";
            echo "      <td>";
            echo "        <ul>".str_replace(" - ","<li>",str_replace("\n","</li>",$row['TierPerks']))."</li></ul>";
            echo "      </td>";
            echo "    </tr>";
            echo "    <tr height=\"3rem\">";
            echo "      <td>";
            echo "        <span style=\"font-size: 2.5rem;\">~$".sprintf("%.2f",0.05*$row['PriceMultiplier'])."</span><span style=\"font-size: 1.5rem;margin-top:1.5rem;\"> / hour</span>";
            echo "      </td>";
            echo "    </tr>";
            echo "  </table>";
            echo "  <div class=\"footer\">";
            echo "    <a class=\"btn\">Select</a>";
            echo "  </div>";
            echo "</div>";
          }
        }
      ?>
    </div>
  </div>
<?php
  $conn->close();
  $footerextra = '<!--<script src="/js/pingserver.js?v=5"></script>-->';
  require_once("../includes/footer.php");
?>
