<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <title>Codename Pukeko</title>
  <meta name="description" content="Pay for hosting for your favourite games when you're playing them and never when you aren't.">
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
    </div>
    <div class="background" style="background: #134FB0;background: linear-gradient(to left, #134FB0 0%,#30475D 100%);">

    </div>
  </div>
  <div class="wrapper main">
    <div class="slider">
      <div class="card third">
        <table>
          <tr>
            <td rowspan="2" width="30%">
              <img src="/img/pukeko-160px.png" alt="Image of a New Zealand native bird, the Pukeko">
              <p><b>Eeeeep!</b></p>
            </td>
            <td>
              <p>Start paying for your game servers when you're using them, <i>and never when you're not</i>.</p>
            </td>
          </tr>
          <tr>
            <td height="1rem" style="text-align: right;">
              <b> - Mr. Pukeko</b>
            </td>
          </tr>
        </table>
      </div>
      <div class="card third">
        <table>
          <tr>
            <td rowspan="2" width="30%">
              <img src="/img/pukeko-160px.png" alt="Image of a New Zealand native bird, the Pukeko">
              <p><b>Eeeeep!</b></p>
            </td>
            <td>
              <p>Pay by the hour instead of by the month. Every cent you spend will be on time you actually wanted.</p>
            </td>
          </tr>
          <tr>
            <td height="1rem" style="text-align: right;">
              <b> - Mr. Pukeko</b>
            </td>
          </tr>
        </table>
      </div>
      <div class="card third">
        <table>
          <tr>
            <td rowspan="2" width="30%">
              <img src="/img/pukeko-160px.png" alt="Image of a New Zealand native bird, the Pukeko">
              <p><b>Eeeeep!</b></p>
            </td>
            <td>
              <p>Anyone on your discord server can pay for the server when they want it, or everyone can chip in an hour!</p>
            </td>
          </tr>
          <tr>
            <td height="1rem" style="text-align: right;">
              <b> - Mr. Pukeko</b>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <hr>
    <h2>Supported games</h2>
    <div class="games">
      <?php 
        require_once('../takahe.conn.php');
        $result = $conn->query('SELECT * FROM game');
        if(!$result){
          print("<b>Failed to fetch list of games!</b>");
        }else{
          while($row = $result->fetch_assoc()){
            echo "<div class=\"game card\">";
            echo "  <div class=\"background\" style=\"background-image:url('".$row['Background']."');\">";
            echo "    <img class=\"foreground\" src=\"".$row['Foreground']."\" alt=\"".$row['Name']."\">";
            echo "  </div>";
            echo "  <div class=\"content\">";
            echo "    <h3>".$row['Name']."</h3>";
            echo "    <ul>".str_replace(" - ","<li>",str_replace("\n","</li>",$row['Perks']))."</li></ul>";
            echo "  </div>";
            echo "  <div class=\"footer\">";
            echo "    <a class=\"btn\" href=\"/game/".$row['API']."\">Learn more</a>";
            echo "  </div>";
            echo "</div>";
          }
        }
        $conn->close();
      ?>
      <div class="game card">
        <div class="background" style="background-color: #aaa; border: 0.5rem #666 dashed; height: 14rem;"></div>
        <div class="content">
          <h3>More games coming soon...</h3>
          <p>We're getting started with the games we're the most familiar with so we can support them the best. As the platform matures, we'll be able to support more games and we'll host polls here to vote on which we should prioritize.</p>
        </div>
      </div>
    </div>
  </div>
  <script src="/js/vendor/modernizr-3.8.0.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="js/vendor/jquery-3.4.1.min.js"><\/script>')</script>
  <script src="/js/plugins.js"></script>
  <script src="/js/main.js"></script>
</body>

</html>
