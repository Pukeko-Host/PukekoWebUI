<?php
$title = "Home";
require_once("includes/header.php");
?>
  <div class="wrapper main">
    <h2>What is PukekoHost?</h2>
    <p>
      PukekoHost is a game hosting service that makes it as easy to start servers for your favourite games quickly, share them with your Discord server, and <em>allow anyone to pay for uptime when they want to play</em>.
      We also charge by the hour, instead of by the month, which makes it much easier to divide up payments between multiple people.
    </p>
    <p>
      <b>Here's how to get started...</b>
    </p>
    <div class="slider">
      <div class="card third">
        <table>
          <tr>
            <td rowspan="2">
              <!--<img src="/img/pukeko-160px.png" alt="Image of a New Zealand native bird, the Pukeko">-->
              <b style="font-size: 3rem;">1.</b>
            </td>
            <td>
              <p>Add Mr. Pukeko to your discord server.</p>
            </td>
          </tr>
          <tr>
            <td height="1rem" style="text-align: right;">
              <a href="https://discordapp.com/oauth2/authorize?client_id=700549643045568522&scope=bot&permissions=67161088" target="_blank">Invite Mr. Pukeko</a>
            </td>
          </tr>
        </table>
      </div>
      <div class="card third">
        <table>
          <tr>
            <td rowspan="2">
              <b style="font-size: 3rem;">2.</b>
            </td>
            <td>
              <p>Select the game you want hosted below.</p>
            </td>
          </tr>
          <tr>
            <td height="1rem" style="text-align: right;">
              <a href="#games"><i>Supported Games</i></a>
            </td>
          </tr>
        </table>
      </div>
      <div class="card third">
        <table>
          <tr>
            <td rowspan="2">
              <b style="font-size: 3rem;">3.</b>
            </td>
            <td>
              <p>Choose the gameserver and tier that best suit your needs!</p>
            </td>
          </tr>
          <tr>
            <td height="1rem" style="text-align: right;">
              You're done!
            </td>
          </tr>
        </table>
      </div>
    </div>
    <hr>
    <h2 id="games">Supported games</h2>
    <div class="games">
      <?php
        require_once('api/games.php');
        $games = new games($conn);
        $result = $games->get_games();
        foreach($result as $row){
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
      ?>
      <div class="game card">
        <div class="background" style="background-color:#aaa;border:0.5rem #666 dashed;height:14rem;position:relative;overflow:hidden;">
          <span style="position:absolute;font-size:10rem;left:50%;top:50%;transform:translate(-50%,-50%);color:#666;opacity:0.5;">?</span>
        </div>
        <div class="content">
          <h3>More games coming soon...</h3>
          <p>We're getting started with the games we're the most familiar with so we can support them the best. As the platform matures, we'll be able to support more games and we'll host polls here to vote on which we should prioritize.</p>
        </div>
      </div>
    </div>
  </div>
<?php
require_once("includes/footer.php");
?>