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
?><!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <title>Hosting <?php echo $row['Name']; ?> | Codename Pukeko</title>
  <meta name="description" content="Get and pay by the hour for flexible <?php echo $row['Name']; ?> hosting on Codename Pukeko!">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="manifest" href="/site.webmanifest">
  <link rel="apple-touch-icon" href="/icon.png">
  <!-- Place favicon.ico in the root directory -->

  <link href="https://fonts.googleapis.com/css?family=Roboto:100,400,400i,700,700i&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/normalize.css">
  <link rel="stylesheet" href="/css/main.css?v=55">

  <meta name="theme-color" content="#134FB0">
</head>

<body>
  <!--[if IE]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->

  <!-- Add your site or application content here -->
  <div class="jumbotron dark">
    <div class="content">
      <h1 style="font-size: min(10vw,10rem);"><span class="dim">Codename: </span>Pukeko</h1>
      <h2><?php echo $row['Name']; ?> Hosting</h2>
    </div>
    <div class="background" style="background: #134FB0;background: linear-gradient(to left, #134FB0 0%,#30475D 100%);">

    </div>
  </div>
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
            echo "<div class=\"card third\">";
            echo "  <table style=\"min-height: 12rem;\">";
            echo "    <tr>";
            echo "      <td colspan=\"2\">";
            /*echo "        <div class=\"card float-right\" style=\"width:4rem;margin:0;\">";
            echo "          <b><span class=\"ping good\"></span>Ping:</b><br>";
            echo "          <span class=\"serverping\" data-server=\"".$row['DomainName']."\">Waiting...</span>";
            echo "        </div>";*/
            echo "        <b>".$row['DomainName']."</b>";
            echo "        <ul>".str_replace(" - ","<li>",str_replace("\n","</li>",$row['Specs']))."</li></ul>";
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
            echo "        <img src=\"".$row['Icon']."\" alt=\"Icon for the ".$row['Name']." tier\">";
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
    <?php
      $conn->close();
    ?>
  <script src="/js/vendor/modernizr-3.8.0.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="js/vendor/jquery-3.4.1.min.js"><\/script>')</script>
  <script src="/js/plugins.js"></script>
  <script src="/js/main.js"></script>
  <!--<script src="/js/pingserver.js?v=5"></script>-->
</body>

</html>
