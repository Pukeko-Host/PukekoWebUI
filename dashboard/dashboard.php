<?php
$title = "Dashboard";
$description = "Manage your game servers on Pukeko Host, ";
$tags = "dashboard,control panel,settings,customize,customise,admin,operator,op,terminal,command line";
$compactheader = true;
$headerextra = '<link rel="stylesheet" href="/css/dashboard.css?v=25">';
require_once('../includes/header.php');
if(!isset($_SESSION['access_token'])){
?>
<div class="wrapper">
    <h2>Please log in to use the dashboard.</h2>
    <p>Currently, a discord account is required to access the dashboard. <a href="/account/?login&return=%2Fdashboard%2F">Login with Discord</a>.</p>
</div>
<?php
}else{
?>
<div class="dashboard">
    <?php
    echo '<div class="guilds">';
    $result = $conn->query("SELECT guild.* FROM guild RIGHT JOIN userguild ON guild.GuildId = userguild.guildId WHERE userguild.userId = ".$_SESSION['user']->id);
    if(!$result){
        echo '<div class="invalid guild"><span class="tooltip">Failed to load your guilds; '.$conn->error.'</span></div>';
    }else{
        while($row = $result->fetch_assoc()){
            echo '<div class="guild" data-id="'.$row['GuildId'].'"><img src="https://cdn.discordapp.com/icons/'.$row['GuildId'].'/'.$row['Icon'].'.png" alt="Discord server icon"><span class="tooltip">'.$row['Name'].'</span></div>';
        }
    }
    echo '</div>';
    $result = $conn->query("SELECT gameserver.*,game.Name AS gamename,game.Icon FROM (gameserver LEFT JOIN game ON gameserver.GameId = game.Id) LEFT JOIN guild ON gameserver.GuildId = guild.GuildId GROUP BY gameserver.Id");
    if(!$result){
        echo '<div class="default gameservers">Failed to load the list of servers; '.$conn->error.'</div>';
    }else{
        echo '<div class="default gameservers">';
        echo '  <div class="dotted-outline flex-center">';
        echo '      This discord server doesn\'t appear to have any game servers!';
        echo '      <br><br><a href="#" class="btn add-server">Add one</a>';
        echo '  </div>';
        
        $lastguild = 0;
        while($row = $result->fetch_assoc()){
            if($row['GuildId']!=$lastguild) echo '</div><div class="gameservers hidden" data-guild="'.$row['GuildId'].'">';
            
            echo '<a class="gameserver" href="#" data-id="'.$row['Id'].'">';
            echo '  '.$row['Name'].' <i class="dim">('.$row['gamename'].')</i>';
            echo '</a>';

            $lastguild = $row['GuildId'];
        }
        echo '</div>';
    }
    ?>
</div>
<?php
}
$footerextra = '<script src="/js/dashboard.js?v=1"></script>';
require_once('../includes/footer.php');
?>