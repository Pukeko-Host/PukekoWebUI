<?php
$title = "Dashboard";
$description = "Manage Pukeko servers you share with your fellow discord users. Anyone on any discord server can create a game server to play with others on the spot.";
$tags = "dashboard,control panel,settings,customize,customise,admin,operator,op,terminal,command line";
$compactheader = true;
$headerextra = '<link rel="stylesheet" href="/css/dashboard.css?v=58">';
require_once('../includes/header.php');
if(!isset($_SESSION['access_token'])){
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
            <a class="gameserver" href="#">
                <span class="placeholder text"></span>
                <span class="placeholder text" style="font-size: 0.8rem;"></span>
            </a>
            <a class="gameserver" href="#">
                <span class="placeholder text"></span>
                <span class="placeholder text" style="font-size: 0.8rem;"></span>
            </a>
            <a class="gameserver" href="#">
                <span class="placeholder text"></span>
                <span class="placeholder text" style="font-size: 0.8rem;"></span>
            </a>
        </div>
    </div>
    <div class="status">
        <h2>Please log in to use the dashboard.</h2>
        <p>Logging in with your discord account is required to access the dashboard. <a href="/account/?login&return=%2Fdashboard%2F">Login with Discord</a>.</p>
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
    $guilds = [];
    $result = $conn->query("SELECT guild.* FROM guild RIGHT JOIN userguild ON guild.GuildId = userguild.guildId WHERE userguild.userId = ".$_SESSION['user']->id);
    if(!$result){
        echo '<div class="invalid guild"><span class="tooltip">Failed to load your guilds; '.$conn->error.'</span></div>';
    }else{
        while($row = $result->fetch_assoc()){
            $guilds[$row['GuildId']]=array('id'=>$row['GuildId'],'name'=>$row['Name'],'icon'=>"https://cdn.discordapp.com/icons/".$row['GuildId']."/".$row['Icon'].".png",'gameservers'=>array('active'=>array(),'archived'=>array()));
            echo '<div class="guild" data-id="'.$row['GuildId'].'"><img src="https://cdn.discordapp.com/icons/'.$row['GuildId'].'/'.$row['Icon'].'.png" alt="Discord server icon"><span class="tooltip">'.$row['Name'].'</span></div>';
        }
    }
    echo '</div>';
    echo '<input type="checkbox" id="showgameservers" checked style="display:none;">';
    echo '<div class="gameserverscontainer">';
    $result = $conn->query("SELECT gameserver.*,game.Name AS gamename,gametier.Icon,gametier.Name AS tiername,gsms.DomainName FROM (((gameserver LEFT JOIN game ON gameserver.GameId = game.Id) LEFT JOIN gametier ON game.Id = gametier.GameId AND gameserver.TierId = gametier.TierNumber) LEFT JOIN gsms ON gameserver.GMSId = gsms.Id) LEFT JOIN guild ON gameserver.GuildId = guild.GuildId GROUP BY gameserver.Id ORDER BY gameserver.GuildId ASC, gameserver.Active DESC");
    if(!$result){
        echo '<div class="default gameservers">Failed to load the list of servers; '.$conn->error.'</div>';
    }else{
        while($row = $result->fetch_assoc()){
            $guilds[$row['GuildId']]['gameservers'][($row['Active']?'active':'archived')][$row['Id']]=array('id'=>$row['Id'],'name'=>$row['Name'],'gamename'=>$row['gamename'],'address'=>$row['DomainName'].':'.$row['Port'],'running'=>$row['Running'],'remainingminutes'=>$row['RemainingMinutes'],'tiericon'=>$row['Icon'],'tiername'=>$row['tiername']);
        }

        ?>
        <div class="default gameservers">
            <div class="dotted-outline flex-center">
                Select a discord server from the left!
            </div>
        </div>
        <?php
        foreach($guilds as $guild){
            echo '<div class="gameservers hidden" data-guild="'.$guild['id'].'">';

            foreach(['active','archived'] as $active){
                echo "<h3>".ucfirst($active)." Servers</h3>";
                $i = 0;
                foreach($guild['gameservers'][$active] as $gameserver){
                    echo '<a class="gameserver" href="#" data-id="'.$gameserver['id'].'">';
                    if($gameserver['running']) echo '<span class="speening"></span>';
                    echo '  <img src="'.$gameserver['tiericon'].'" style="height:1em;" class="nointerpolate" alt="Icon for the "'.$gameserver['tiername'].'" tier">';
                    echo '  '.$gameserver['name'].'<br><span class="ip">'.$gameserver['address'].'</span>';
                    echo '  <br><i class="dim" style="font-size: 0.8em;">'.$gameserver['gamename'].'</i>';
                    echo '</a>';
                    $i++;
                }
                if($i==0){
                    echo '<a class="gameserver invalid" href="#">';
                    if($active=='active') echo '<i>Any server purchased by anyone on this discord server will appear here.<br>The server address is reserved for your game for up to a week of inactivity.</i>';
                    else echo '<i>If a server is not used for a week, it will be archived here.<br>From here, you can ressurect the server, or download the save file.</i>';
                    echo '</a>';
                }
            }
            echo '</div>';
        }
        echo '</div>';
    }
    echo '<div class="default status"><h1>Hi hi</h1></div>';
    ?>
</div>
<?php
}
$footerextra = '<script src="/js/dashboard.js?v=2"></script>';
require_once('../includes/footer.php');
?>