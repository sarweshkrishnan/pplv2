<?php
session_start();
require './../includes/dbconfig.php';
$user = mysql_real_escape_string($_SESSION['user']);
if(!isset($user))
{
    header('Location: ./../login.php');
}

$idString = mysql_real_escape_string($_POST['c11']);
//echo($idString."\n");
$ids = explode(',',$idString);
sort($ids);

//// For Debugging
//for($i=0;$i<16;$i++)
//    echo($ids[$i]."\n");

// Getting User Balance and data.

$userQuery = "SELECT * FROM userData WHERE userId1='$user' or userId2='$user' ; ";
$userResult = mysql_query($userQuery);

if(isset($userResult))
{
    $userBalance = mysql_result($userResult,0,"actualBalance");
    $userTeamId  = mysql_result($userResult,0,"teamId");
    $transferNum = mysql_result($userResult,0,"transferNum");
    $p11status   = mysql_result($userResult,0,"p11");
}


if($p11status==1)
{
    exit("You have already confirmed your playing 11");
}


//checking for duplicate ids

$ids = array_values(array_unique($ids));

if(count($ids)!=11)
{
    exit("You have sent corrupt data: Error (Count mismatch P11)");
}

$inSquadQuery  = "SELECT * from confirmedSquad where teamId=$userTeamId";
$inSquadResult = mysql_query($inSquadQuery);
$confSquad = [];

for($i=0;$i<mysql_num_rows($inSquadResult);$i++)
{
    $confSquad[] = mysql_result($inSquadResult,$i,"playerId");
}

for($i=0;$i<count($ids);$i++)
{
    if(!in_array($ids[$i],$confSquad))
    {
        exit("One of your players is not in your squad. ");
    }
}
//Deleting existing confirmedSquad before Copying And clearing the show P11

$deleteQuery    = "DELETE FROM confirmedP11 where teamId=".$userTeamId.";";
$deleteResult   = mysql_query($deleteQuery);

$clearP11Query  = "DELETE FROM userP11 where teamId=".$userTeamId.";";
$clearP11Result = mysql_query($clearP11Query);


//echo($spent."spent");
//Checking Ratios

//Defining Checking vars
$batsman = 0;
$bowler = 0;
$captain =0;
$wkeeper = 0;
$rounder = 0;

for($i=0;$i<count($ids);$i++)
{
    $pid = $ids[$i];

    $playerTypeQuery = "SELECT * FROM players WHERE playerId='$pid';";
    $playerTypeResult = mysql_query($playerTypeQuery);

    $playerType = mysql_result($playerTypeResult,0,'type');
    $playerTypeCaptain = mysql_result($playerTypeResult,0,'captain');
//    echo($playerType);
//    echo($playerTypeCaptain);

    if(strpos($playerType,'tsman')) $batsman++;
    else if(strpos($playerType,'keeper')) $wkeeper++;
    else if(strpos($playerType,'ounder')) $rounder++;
    else if(strpos($playerType,'owler')) $bowler++;

    if(strpos($playerTypeCaptain,'aptain')) $captain++;
}


$checkString = $batsman.$bowler.$rounder.$wkeeper;
//echo('\nCHeckString'.$checkString);

if($captain<1)
{
    exit('You need atleast one captain');
}

if(!((strcmp($checkString,'5411')==0)||(strcmp($checkString,'5321')==0)||(strcmp($checkString,'4331')==0)))
{
    exit("Not a Correct Ratio ".$checkString);
}
// Adding the new players into the confirmed squad table.

for($i=0;$i<count($ids);$i++)
{

    $playerId = $ids[$i];

    $playerQuery = "SELECT * FROM playerData WHERE playerId='$playerId' ;";
    $playerResult = mysql_query($playerQuery);


    if(isset($playerResult))
    {
        $playerCost       = mysql_result($playerResult,0,"cost");
        $playerForm       = mysql_result($playerResult,0,"form");
        $playerConfidence = mysql_result($playerResult,0,"confidence");
    }

    $transferQuery = "INSERT INTO confirmedP11 VALUES ('$userTeamId','$playerId'); ";
    $transferresult = mysql_query($transferQuery);
}

//Update the p11 status

$p11UpdateQuery = "UPDATE userData SET p11=1 WHERE teamId=$userTeamId";
$p11UpdateResult = mysql_query($p11UpdateQuery);

echo("Your playing 11 has been confirmed\n");
//echo("You may now choose your playing 11");
//echo($newBalance);
//echo($clearP11Query);
//echo($deleteQuery);
