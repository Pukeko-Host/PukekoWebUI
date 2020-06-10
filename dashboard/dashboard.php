<?php
$params = explode('/', substr($_SERVER['REQUEST_URI'], 11));
define('GUILD', count($params)>0? $params[0]: NULL);
define('GAMESERVER', count($params)>1? $params[1]: NULL);

$title = "Dashboard";
$description = "Manage Pukeko servers you share with your fellow discord users. Anyone on any discord server can create a game server to play with others on the spot.";
$tags = "dashboard,control panel,settings,customize,customise,admin,operator,op,terminal,command line";
$compactheader = true;
$headerextra = '<link rel="stylesheet" href="/css/dashboard.css?v=106">';
require_once('../includes/header.php');
if(!$_SESSION['account']->logged_in){
    // Refresh conn so more queries can be made.
    $_SESSION['account']->conn = $conn;
    $_SESSION['account']->guilds->conn = $conn;
?>
<div class="dashboard">
    <div class="guilds">
        <label for="showgameservers" style="display: inline-block;">
            <div class="guild hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </label>
        <div class="guild"><img class="placeholder"></div>
        <div class="guild"><img class="placeholder"></div>
        <div class="guild"><img class="placeholder"></div>
        <div class="guild"><img class="placeholder"></div>
        <div class="guild"><img class="placeholder"></div>
        <div class="guild"><img class="placeholder"></div>
        <div class="guild"><img class="placeholder"></div>
    </div>
    <input type="checkbox" id="showgameservers" checked style="display:none;">
    <div class="gameserverscontainer">
        <div class="default gameservers">
            <div class="gameserver">
                <span class="placeholder text"></span>
                <span class="placeholder text" style="font-size: 0.8rem;"></span>
            </div>
            <div class="gameserver">
                <span class="placeholder text"></span>
                <span class="placeholder text" style="font-size: 0.8rem;"></span>
            </div>
            <div class="gameserver">
                <span class="placeholder text"></span>
                <span class="placeholder text" style="font-size: 0.8rem;"></span>
            </div>
        </div>
    </div>
    <div class="statuses">
        <div class="status default">
            <div class="jumbotron">
                <div class="content">
                    <h2>Please log in to use the dashboard.</h2>
                    <p>Logging in with your discord account is required to access the dashboard. <a href="/account/?login&return=<?php echo urlencode($_SERVER['REQUEST_URI']);?>">Login with Discord</a>.</p>
                </div>
                <div class="background"></div>
            </div>
        </div>
    </div>
</div>
<?php
}else{
?>
<div class="dashboard">
    <div class="guilds">
        <label for="showgameservers" style="display: inline-block;">
            <div class="guild hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </label>
    <?php
    foreach($_SESSION['account']->guilds->list as $guild){
        echo "<a href=\"/dashboard/$guild->id/\" class=\"guild".($guild->valid?'':' invalid').($guild->id==GUILD?' active':'')."\" data-id=\"$guild->id\" style=\"order:$guild->pos;\" draggable=\"true\">";
        echo "  <img src=\"$guild->icon?size=64\" alt=\"Discord server icon\" draggable=\"false\"><span class=\"tooltip\">$guild->name</span>";
        echo "</a>";
    }
    
    echo '</div>';
    echo '<input type="checkbox" id="showgameservers" checked style="display:none;">';
    echo '<div class="gameserverscontainer">';
        ?>
        <div class="default gameservers<?php if(!is_null(GUILD) && array_filter($_SESSION['account']->guilds->list, function($g){return $g->id==GUILD;})) echo " hidden"; ?>">
            <div class="dashed-outline text-center rounded">
                Select a discord server from the left!
            </div>
        </div>
        <?php
        foreach($_SESSION['account']->guilds->list as $guild){
            $guild->conn = $conn;
            $guild->gameservers->conn = $conn;
            $guild->gameservers->get_gameservers($guild->id);
            echo '<div class="gameservers'.(GUILD == $guild->id?"":" hidden").'" data-guild="'.$guild->id.'">';

            foreach(['active','archived'] as $active){
                echo "<h3>".ucfirst($active)." Servers</h3>";
                $i = 0;
                foreach($guild->gameservers->list[$active] as $gameserver){
                    echo "<a class=\"gameserver\" href=\"/dashboard/$guild->id/$gameserver->id/\" data-id=\"$gameserver->id\">";
                    if($gameserver->state=='running') echo "<span class=\"speening\"></span>";
                    echo "<img src=\"{$gameserver->tier['Icon']}\" style=\"height:1em;\" class=\"nointerpolate\" alt=\"Icon for the {$gameserver->tier['Name']} tier\">";
                    echo "$gameserver->name<br><span class=\"ip\">{$gameserver->gsms['DomainName']}</span>";
                    echo "<br><i class=\"dim\" style=\"font-size: 0.8em;\">{$gameserver->game['Name']}</i>";
                    echo "</a>";
                    $i++;
                }
                if($i==0){
                    echo "<a class=\"gameserver invalid\" href=\"/dashboard/$guild->id/\">";
                    if($active=='active') echo "<i>Any server purchased by anyone on this discord server will appear here.<br>The server address is reserved for your game for up to 2 weeks of inactivity.</i>";
                    else echo "<i>If a server is not used for 2 weeks, it will be archived here.<br>From here, you can ressurect the server, or download the save file.</i>";
                    echo "</a>";
                }
            }
            echo "</div>";
        }
        echo "</div>";
    ?>
    <div class="statuses">
        <div class="default status<?php if(!is_null(GAMESERVER) && array_filter(array_values($_SESSION['account']->guilds->gameservers->list), function($g){return $g->id==GUILD;})) echo " hidden";?>">
        <div class="jumbotron dark">
            <div class="content">
                <h2>View the status of a game server here</h2>
                <p>Here you can view the status of servers you can join, manage servers you own and add hours of uptime to your favourite servers.</p>
            </div>
            <div class="background"></div>
        </div>
        </div>
        <?php
        foreach($_SESSION['account']->guilds->list as $guild){
            foreach(['active','archived'] as $active){
                foreach($guild->gameservers->list[$active] as $gameserver){
                    echo "<div class=\"status".(GAMESERVER == $gameserver->id?"":" hidden")."\" data-id=\"$gameserver->id\">";
                    echo "  <div class=\"jumbotron dark\">";
                    echo "      <div class=\"content\">";
                    echo "          <h2>$gameserver->name</h2>";
                    echo "          <p>{$gameserver->tier['Name']} Tier <i>{$gameserver->game['Name']}</i> Hosting Plan</p>";
                    echo "      </div>";
                    echo "      <div class=\"footer\">";
                    echo "          <a class=\"btn\">Stop Server</a>";
                    echo "      </div>";
                    echo "      <div class=\"background\"></div>";
                    echo "  </div>";
                    echo "  <div class=\"card terminal dark\">";
                    echo "      <div class=\"header\"><h4>Terminal</h4></div>";
                    echo "      <div class=\"content\">";
                    echo "          <div class=\"row\">Minecraft Console</div>";
                    echo "          <div class=\"row\">Connecting to terminal...</div>";
                    echo "          <form class=\"input\" method=\"POST\">";
                    echo "              <span class=\"gt\">&gt;</span><input type=\"text\" name=\"cmd\" placeholder=\"p/help\">";
                    echo "              <input type=\"submit\" class=\"btn\" value=\"Send\">";
                    echo "          </form>";
                    echo "      </div>";
                    echo "  </div>";
                    echo "</div>";
                }
            }
        }
        ?>
    </div>
</div>
<?php
}
$footerextra = '<script src="/js/dashboard.js?v=43"></script>';
require_once('../includes/footer.php');
?>