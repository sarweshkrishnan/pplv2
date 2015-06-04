<?php
session_start();
require './../includes/dbconfig.php';
$user = 
mysqli_real_escape_string($link,$_SESSION['user']);
if(!isset($user))
{
    header('Location: ./../login.php');
}
$round1avg = 0;
$round2avg = 0;
$round3avg = 0;
$round4avg = 0;
$round5avg = 0;
$round6avg = 0;
$round7avg = 0;
$round8avg = 0;

$rank = 0;
$count = 0;

$batsman = 0;
$bowler = 0;
$keeper = 0;
$all_rounder = 0;

$userQuery = "SELECT * FROM userData WHERE 1";
$userResult = mysqli_query($link,$userQuery);

$rows = mysqli_num_rows($userResult);

$teamId = "SELECT * FROM userData WHERE userId1 = $user or userId2 = $user";
$teamIdQuery = mysqli_query($link,$teamId);

$team = 
mysql_result($teamIdQuery,0,"teamId");
$score = 
mysql_result($teamIdQuery,0,"score");
$roundScore = 
mysql_result($teamIdQuery,0,"round1Score");

for($i = 0;$i < $rows ;$i++){
$user1 = 
mysql_result($userResult,$i,"userId1");
$user2 = 
mysql_result($userResult,$i,"userId2");

if(
mysql_result($userResult,$i,"score") > $score )
    $count++;

    $round1avg  = $round1avg + 
mysql_result($userResult,$i,"round1Score")/$rows;
    $round2avg  = $round2avg + 
mysql_result($userResult,$i,"round2Score")/$rows;
    $round3avg  = $round3avg + 
mysql_result($userResult,$i,"round3Score")/$rows;
    $round4avg  = $round4avg + 
mysql_result($userResult,$i,"round4Score")/$rows;
    $round5avg  = $round5avg + 
mysql_result($userResult,$i,"round5Score")/$rows;
    $round6avg  = $round6avg + 
mysql_result($userResult,$i,"round6Score")/$rows;
    $round7avg  = $round7avg + 
mysql_result($userResult,$i,"round7Score")/$rows;
    $round8avg  = $round8avg + 
mysql_result($userResult,$i,"round8Score")/$rows;

if($user == $user1 || $user == $user2){
    $round1 = 
mysql_result($userResult,$i,"round1Score");
    $round2 = 
mysql_result($userResult,$i,"round2Score");
    $round3 = 
mysql_result($userResult,$i,"round3Score");
    $round4 = 
mysql_result($userResult,$i,"round4Score");
    $round5 = 
mysql_result($userResult,$i,"round5Score");
    $round6 = 
mysql_result($userResult,$i,"round6Score");
    $round7 = 
mysql_result($userResult,$i,"round7Score");
    $round8 = 
mysql_result($userResult,$i,"round8Score");
}
}

$player = "SELECT playerId FROM confirmedP11_back WHERE teamId = '".$team."' ";
$playerQuery = mysqli_query($link,$player);

$confirm_rows = mysqli_num_rows($playerQuery);
if($confirm_rows != 0){
for($i = 0; $i < 11; $i++){
    $playerId[$i] = 
mysql_result($playerQuery,$i,"playerId");
    $playername = "SELECT * FROM players WHERE playerId = '".$playerId[$i]."' ";
    $playerquery = mysqli_query($link,$playername);

    $playerName1[$i] = 
mysql_result($playerquery,0,"name");
    $playerName2 = explode(" ",$playerName1[$i]);
    $pos = substr_count($playerName1[$i]," ");

    $playerName[$i] = $playerName2[$pos];
    $playerScore[$i] = 
mysql_result($playerquery,0,"roundOne");

    if(
mysql_result($playerquery,0,"type") == "Batsman")
        $batsman = $batsman + $playerScore[$i];
    else if(
mysql_result($playerquery,0,"type") == "All-Rounder")
        $all_rounder = $all_rounder + $playerScore[$i];
    else if(
mysql_result($playerquery,0,"type") == "Wicketkeeper")
        $keeper = $keeper + $playerScore[$i];
    else
        $bowler = $bowler + $playerScore[$i];
    }

$favourites_query = "SELECT playerId,COUNT(playerId) FROM confirmedP11_back GROUP BY playerId ORDER BY COUNT(playerId) DESC";
$favourites_res = mysqli_query($link,$favourites_query);

$favourites[0] = 
mysql_result($favourites_res,0,"playerId") - 1000;
$favourites[1] = 
mysql_result($favourites_res,1,"playerId") - 1000;
$favourites[2] = 
mysql_result($favourites_res,2,"playerId") - 1000;

$player_row_query = "SELECT * FROM players WHERE 1";
$player_row = mysqli_query($link,$player_row_query);
$favourites_img[0] = 
mysql_result($player_row, $favourites[0],"photoUrl");
$favourites_img[1] = 
mysql_result($player_row, $favourites[1],"photoUrl");
$favourites_img[2] = 
mysql_result($player_row, $favourites[2],"photoUrl");
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>PPL'15</title>

        <script src='Chart.min.js'></script>
        <script src="./../includes/jquery-2.1.1.min.js"></script>

        <!-- Bootstrap core CSS -->
        <link href="../includes/css/bootstrap.css" rel="stylesheet">
        <link href="../includes/css/common.css" rel="stylesheet">
        <link href="analysis.css" rel="stylesheet">
        <script src="../includes/bootstrap.js"></script>
    </head>
    <body>

    <nav class="header navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="../home/">PPL '15</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="../transfers/">Transfers</a></li>
            <li><a href="../matchday/">Matchday</a></li>
            <li><a href="../leaderboard/">Leaderboard</a></li>
            <li><a href="../wclive/">WCLive</a></li>
            <li><a href="../instructions/">Instructions</a></li>
            </ul>
            <div class="navbar-header navbar-right">
                <div class="dropdown" style="margin-top:10%">
                <button class="btn dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                    <b>PID :</b> <?php echo $user ?>
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li class="drop" role="presentation"><a role="menuitem" tabindex="-1" href="../analysis/">Analysis</a></li>
                    <li class="drop" role="presentation"><a role="menuitem" tabindex="-1" href="./../contact/">Contact</a></li>
                    <li class="drop" role="presentation" class="divider"></li>
                    <li class="drop" role="presentation"><a role="menuitem" tabindex="-1" href="./../logout.php">Logout</a></li>
                </ul>
                </div>
            </div>
      </div>
      </div>
    </nav>
<?php if($confirm_rows != 0){ ?>
    <div class="statBoard">
        <div class="stat">
        <h4><b><?php echo $rows; ?></b></h4>
        <p>Online Players</p>
        </div>
        <div class="stat">
        <h4><b><?php echo $score; ?></b></h4>
        <p>Total Score</p>
        </div>
        <div class="stat">
        <h4><b><?php echo $roundScore; ?></b></h4>
        <p>Round Score</p>
        </div>
        <div class="stat">
        <h4><b><?php echo ++$count; ?></b></h4>
        <p>Rank</p>
        </div>
    </div>

    <p style="position:absolute;top:23%;margin-left:4%;"><b><u>Relative Performance</u></b></p>
    <p style="position:absolute;top:23%;margin-left:54%;"><b><u>Team Performance</u></b></p>
    <p style="position:absolute;top:88%;margin-left:4%;"><b><u>Favourites</u></b></p>
    <p style="position:absolute;top:88%;margin-left:54%;"><b><u>Department-wise Performance</u></b></p>

    <div id="canvasContainer">
        <canvas id="playerPerformance" class="lineGraph"></canvas>
        <!-- bar chart canvas element -->
        <canvas id="teamPerformance" class="lineGraph"></canvas>

        <div class="favourites">
        <img style="margin-right:1%;border-radius:5%;" id="favourites1" />
        <img style="margin-right:1%;border-radius:5%;" id="favourites2" />
        <img style="margin-right:1%;border-radius:5%;" id="favourites3" />
        </div>

        <canvas id="pie" class="lineGraph"></canvas>
    </div>
<?php }
else
    echo '<h1 style="margin-top:20%;text-align:center;font-family:Roboto;">Analysis is currently not available.<br /></h1>';
?>
    <nav class="footer navbar navbar-default navbar-fixed-bottom">
        <div class="footer">
            <p>Developed by <b>Delta Force.</b></p>
        </div>
    </nav>
</body>
<?php if($confirm_rows!=0){?>
<script>
        var round1 = <?php echo $round1 ; ?> ;
        var round2 = <?php echo $round2 ; ?> ;
        var round3 = <?php echo $round3 ; ?> ;
        var round4 = <?php echo $round4 ; ?> ;
        var round5 = <?php echo $round5 ; ?> ;
        var round6 = <?php echo $round6 ; ?> ;
        var round7 = <?php echo $round7 ; ?> ;
        var round8 = <?php echo $round8 ; ?> ;

        var round1avg = <?php echo $round1avg ; ?> ;
        var round2avg = <?php echo $round2avg ; ?> ;
        var round3avg = <?php echo $round3avg ; ?> ;
        var round4avg = <?php echo $round4avg ; ?> ;
        var round5avg = <?php echo $round5avg ; ?> ;
        var round6avg = <?php echo $round6avg ; ?> ;
        var round7avg = <?php echo $round7avg ; ?> ;
        var round8avg = <?php echo $round8avg ; ?> ;

        var player0 = "<?php echo $playerName[0] ; ?>" ;
        var player1 = "<?php echo $playerName[1] ; ?>" ;
        var player2 = "<?php echo $playerName[2] ; ?>" ;
        var player3 = "<?php echo $playerName[3] ; ?>" ;
        var player4 = "<?php echo $playerName[4] ; ?>" ;
        var player5 = "<?php echo $playerName[5] ; ?>" ;
        var player6 = "<?php echo $playerName[6] ; ?>" ;
        var player7 = "<?php echo $playerName[7] ; ?>" ;
        var player8 = "<?php echo $playerName[8] ; ?>" ;
        var player9 = "<?php echo $playerName[9] ; ?>" ;
        var player10 = "<?php echo $playerName[10] ; ?>" ;

        var playerscore0 = "<?php echo $playerScore[0] ; ?>" ;
        var playerscore1 = "<?php echo $playerScore[1] ; ?>" ;
        var playerscore2 = "<?php echo $playerScore[2] ; ?>" ;
        var playerscore3 = "<?php echo $playerScore[3] ; ?>" ;
        var playerscore4 = "<?php echo $playerScore[4] ; ?>" ;
        var playerscore5 = "<?php echo $playerScore[5] ; ?>" ;
        var playerscore6 = "<?php echo $playerScore[6] ; ?>" ;
        var playerscore7 = "<?php echo $playerScore[7] ; ?>" ;
        var playerscore8 = "<?php echo $playerScore[8] ; ?>" ;
        var playerscore9 = "<?php echo $playerScore[9] ; ?>" ;
        var playerscore10 = "<?php echo $playerScore[10] ; ?>" ;

            var w = window.innerWidth;
            var h = window.innerHeight;

            // line chart data
            var playerData = {
                labels : ["Match 1","Match 2","Match 3","Match 4","Match 5","Match 6","Match 7","Match 8"],
                datasets : [
                {
                    fillColor : "#c8c8c8",
                    strokeColor : "#099",
                    pointColor : "#099",
                    pointStrokeColor : "#9DB86D",
                    data : [round1,round2,round3,round4,round5,round6,round7,round8]
                },
                {
                    fillColor : "#a0a0a0",
                    strokeColor : "#a0a0a0",
                    pointColor : "#404040",
                    pointStrokeColor : "#9DB86D",
                    data : [round1avg,round2avg,round3avg,round4avg,round5avg,round6avg,round7avg,round8avg]
                }
            ]
            }
            // get line chart canvas
            var playerPerformance = document.getElementById('playerPerformance').getContext('2d');

            var canvas = document.getElementsByTagName('canvas')[0];
            canvas.width  = 0.45*w;
            canvas.height = 0.45*h;


            // draw line chart
            new Chart(playerPerformance).Line(playerData);

           // bar chart data
            var barData = {
                labels : [player0,player1,player2,player3,player4,player5,player6,player7,player8,player9,player10],
                datasets : [
                    {
                        fillColor : "#48A497",
                        data : [playerscore0,playerscore1,playerscore2,playerscore3,playerscore4,playerscore5,playerscore6,playerscore7,playerscore8,playerscore9,playerscore10]
                    }
                ]
            }
            // get bar chart canvas
            var teamPerformance = document.getElementById("teamPerformance").getContext("2d");
            canvas = document.getElementsByTagName('canvas')[1];
            canvas.width  = 0.45*w;
            canvas.height = 0.45*h;
            // draw bar chart
            new Chart(teamPerformance).Bar(barData);

            var batsman = <?php echo $batsman; ?>;
            var bowler = <?php echo $bowler; ?>;
            var keeper = <?php echo $keeper; ?>;
            var all_rounder = <?php echo $all_rounder; ?>;
            var data = [
            {
                value: batsman,
                color:"#FF2500",
                label: "Batsmen"
            },
            {
                value: bowler,
                color: "#00FFA5",
                label: "Bowlers"
            },
            {
                value: keeper,
                color: "#FFA500",
                label: "Wicket Keeper"
            },
            {
                value: all_rounder,
                color: "#005AFF",
                label: "All-rounders"
            }
            ]
            var pie = document.getElementById("pie").getContext("2d");
            canvas = document.getElementsByTagName('canvas')[2];
            canvas.width  = 0.3*w;
            canvas.height = 0.3*h;

            var pieOptions = {
                 segmentShowStroke : false,
                 animateScale : true
            }
            new Chart(pie).Pie(data,pieOptions);

            var img_1 ="<?php echo $favourites_img[0]; ?>";
            document.getElementById("favourites1").src = img_1;
            var img_2 ="<?php echo $favourites_img[1]; ?>";
            document.getElementById("favourites2").src = img_2;
            var img_3 ="<?php echo $favourites_img[2]; ?>";
            document.getElementById("favourites3").src = img_3;
</script>
<?php } ?>
</html>
