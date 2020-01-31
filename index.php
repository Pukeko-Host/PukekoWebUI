<?php
$title = "Home";
require_once("includes/header.php");
?>
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