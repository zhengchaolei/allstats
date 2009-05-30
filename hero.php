<?php 
/*********************************************
<!-- 
*   	DOTA ALLSTATS
*   
*	Developers: Reinert, Dag Morten, Netbrain, Billbabong.
*	Contact: developer@miq.no - Reinert
*
*	
*	Please see http://forum.codelain.com/index.php?topic=4752.0
*	and post your webpage there, so I know who's using it.
*
*	Files downloaded from http://dotastats.miq.no/download/
*
*	Copyright (C) 2009  Reinert, Dag Morten , Netbrain, Billbabong
*
*
*	This file is part of DOTA ALLSTATS.
*
* 
*	 DOTA ALLSTATS is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    DOTA ALLSTATS is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with DOTA ALLSTATS.  If not, see <http://www.gnu.org/licenses/>
*
-->
**********************************************/

if($dbType == 'sqlite')
{
	$heroid=sqlite_escape_string($_GET["hid"]);
	$sortcat=sqlite_escape_string($_GET["s"]);
	$order=sqlite_escape_string($_GET["o"]);
}
else
{
	$heroid=mysql_real_escape_string($_GET["hid"]);
	$sortcat=mysql_real_escape_string($_GET["s"]);
	$order=mysql_real_escape_string($_GET["o"]);
}
require_once("functions.php");
require_once("config.php");

$heroName = "";
$heroDescription = "";

//Check If alias
if($dbType == 'sqlite')
{
	$heroid=checkIfAliasSQLite($heroid, $dbType, $dbHandle);
}
else
{
	$heroid=checkIfAliasMySQL($heroid);
}

//Get hero name and description
$sql = "select description, summary from originals where heroid='$heroid'";
if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$heroName = $row["description"];
		$heroDescription = $row["summary"];
	}
}
else
{
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
	{
		$heroName = $row["description"];
		$heroDescription = $row["summary"];
	}
}
//find if this is an alias
$aliasheroes="";
$sql = "select heroid from originals where original='$heroid'";
if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$aliasheroes="$aliasheroes or hero='".$row["heroid"]."'";
	}
}
else
{
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
	{
		$aliasheroes="$aliasheroes or hero='".$row["heroid"]."'";
	}
}
?>
<?php
//Get overall hero stats
$sql =" Select *, (totgames-wins) as losses, (kills*1.0/deaths) as kdratio, (wins*1.0/totgames) as winratio From 
	(SELECT description, summary, skills, stats, hero, count(*) as totgames, 
	SUM(case when((c.winner = 1 and a.newcolour < 6) or (c.winner = 2 and a.newcolour > 6)) then 1 else 0 end) as wins, SUM(kills) as kills, SUM(deaths) as deaths, 
	SUM(assists) as assists, SUM(creepkills) as creepkills, SUM(creepdenies) as creepdenies, SUM(neutralkills) as neutralkills, SUM(towerkills) as towerkills, SUM(raxkills) as raxkills, SUM(courierkills) as courierkills
	FROM dotaplayers AS a LEFT JOIN originals as b ON hero = heroid LEFT JOIN dotagames as c ON c.gameid = a.gameid
	LEFT JOIN gameplayers as d ON d.gameid = a.gameid and a.colour = d.colour LEFT JOIN games as e ON d.gameid = e.id where hero='$heroid' $aliasheroes group by description) as z order by description asc";
	if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$totgames=$row["totgames"];
		$wins=$row["wins"];
		$losses=$row["losses"];
		$winratio=$row["winratio"];
		$kills=$row["kills"];
		$deaths=$row["deaths"];
		$assists=$row["assists"];
		$kdratio=$row["kdratio"];
		$creepkills=$row["creepkills"];
		$creepdenies=$row["creepdenies"];
		$neutralkills=$row["neutralkills"];
		$towerkills=$row["towerkills"];
		$raxkills=$row["raxkills"];
		$courierkills=$row["courierkills"];
		$summary=$row["summary"];
		$stats=$row["stats"];
		$skills=$row["skills"];
	}
}
else
{	
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$totgames=$row["totgames"];
		$wins=$row["wins"];
		$losses=$row["losses"];
		$winratio=$row["winratio"];
		$kills=$row["kills"];
		$deaths=$row["deaths"];
		$assists=$row["assists"];
		$kdratio=$row["kdratio"];
		$creepkills=$row["creepkills"];
		$creepdenies=$row["creepdenies"];
		$neutralkills=$row["neutralkills"];
		$towerkills=$row["towerkills"];
		$raxkills=$row["raxkills"];
		$courierkills=$row["courierkills"];
		$summary=$row["summary"];
		$stats=$row["stats"];
		$skills=$row["skills"];
	}
	mysql_free_result($result);
}
	?>
<table class="table" id="introtable">
	<tr class="rowuh"> 
	<td colspan=12 align="center" >
	<table class="rowuh" width=100%>
	<tr>
		<td width=64px align="left" class="rowuh"> <img src=./img/heroes/<?php print $heroid ?>.gif alt="" width=64px height=64px></td>
		<td width=99%><h2><?php print $heroName; ?></h2></td>
		<td width=64px align="left" class="rowuh"> <img src=./img/heroes/<?php print $heroid ?>.gif alt="" width=64px height=64px></td>
	</tr>
	</table>
    </td>
  </tr>
  <tr>
	<td class="tableheader"colspan=1 width=33%>All Time Statistics:</td>
	<td class="tableheader" colspan=1><?php print $heroName; ?> Information:</td>
  </tr>
  <tr>
	<td>
	<table class="rowuh" width=100%>
		  <tr class="rowuh">
			<td>Games:</td>
			<td><?php print $totgames; ?></td>
			<td>Win/Loss Ratio:</td>
			<td ><?php print ROUND($winratio,2); ?></td>
		  </tr>
		  <tr class="rowuh">
			<td>Wins:</td>
			<td ><?php print $wins; ?></td>
			<td>Losses:</td>
			<td ><?php print $losses; ?></td>
		  </tr>
		  <tr class="rowuh" height=10px>
		  </tr>
		  <tr class="rowuh">
			<td>Kills:</td>
			<td ><?php print ROUND($kills,2); ?></td>
			<td>Deaths:</td>
			<td ><?php print ROUND($deaths,2); ?></td>
		  </tr>
		  <tr class="rowuh">
			<td>Assists:</td>
			<td ><?php print ROUND($assists,2); ?></td>
			<td>Kill/Death Ratio:</td>
			<td ><?php print ROUND($kdratio,2); ?></td>
		  </tr>
		  <tr class="rowuh" height=10px>
		  </tr>
		  <tr class="rowuh">
			<td>Creep Kills:</td>
			<td ><?php print ROUND($creepkills,2); ?></td>
			<td>Creep Denies:</td>
			<td ><?php print ROUND($creepdenies,2);  ?></td>
		  </tr>
		  <tr class="rowuh">
			<td>Neutral Kills:</td>
			<td ><?php print ROUND($neutralkills,2); ?></td>
			<td>Tower Kills:</td>
			<td ><?php print ROUND($towerkills,2); ?></td>
		  </tr>
		  <tr class="rowuh">
			<td>Rax Kills:</td>
			<td ><?php print ROUND($raxkills,2); ?></td>
			<td>Courier Kills:</td>
			<td ><?php print ROUND($courierkills,2); ?></td>
		  </tr>
		</td>
		</table>
	</td>
	<td>
	<table class="rowuh">
	<tr>
	<td colspan=2><?php print $summary; ?></td>
	</tr>
	<tr class="rowuh" height=10px>
	</tr>
	<tr>
		<td width=33%><?php print $stats; ?></td>
		<td><?php print $skills; ?></td>
	</tr>
	</table
	</td>
		
	
  </tr>
</table>
<table class="table" id="theader" align="center">
<tr class="rowuh" height=20px>
		  </tr>
  <tr class="rowuh">
  <td colspan=15 align="center" >
	<h3><strong>Player History:</strong></h3>
  </td>
  <tr class="tableheader">
<?php
  //Player Name
	if($sortcat == "name")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=hero&hid=$heroid&s=name&o=desc\">Player</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=hero&hid=$heroid&s=name&o=asc\">Player</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=hero&hid=$heroid&s=name&o=desc\">Player</a></td>");
	}
	//Game Name
	if($sortcat == "gamename")
	{
		if($order == "asc")
		{
			print("<td><a href=\"?p=hero&hid=$heroid&s=gamename&o=desc\">Game Name</a></td>");
		}
		else
		{
			print("<td><a href=\"?p=hero&hid=$heroid&s=gamename&o=asc\">Game Name</a></td>");
		}
	}
	else
	{
		print("<td><a href=\"?p=hero&hid=$heroid&s=gamename&o=asc\">Game Name</a></td>");
	}
	//Game Type
	if($sortcat == "type")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=type&o=desc\">Type</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=type&o=asc\">Type</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=type&o=desc\">Type</a></td>");
	}
	//result
	if($sortcat == "result")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=result&o=desc\">Result</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=result&o=asc\">Result</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=result&o=desc\">Result</a></td>");
	}
	//Kills
	if($sortcat == "kills")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=kills&o=desc\">Kills</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=kills&o=asc\">Kills</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=kills&o=desc\">Kills</a></td>");
	}
	
	//Deaths
	if($sortcat == "deaths")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=deaths&o=desc\">Deaths</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=deaths&o=asc\">Deaths</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=deaths&o=desc\">Deaths</a></td>");
	}
	
	//Assists
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=assists&o=desc\">Assists</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=assists&o=asc\">Assists</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=assists&o=desc\">Assists</a></td>");
	}
	//KDRatio
	if($sortcat == "kdratio")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=kdratio&o=desc\">K/D Ratio</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=kdratio&o=asc\">K/D Ratio</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=kdratio&o=desc\">K/D Ratio</a></td>");
	}
	//Creep Kills
	if($sortcat == "creepkills")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=creepkills&o=desc\">Creep<br>Kills</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=creepkills&o=asc\">Creep<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=creepkills&o=desc\">Creep<br>Kills</a></td>");
	}
	//Creep Denies
	if($sortcat == "creepdenies")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=creepdenies&o=desc\">Creep<br>Denies</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=creepdenies&o=asc\">Creep<br>Denies</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=creepdenies&o=desc\">Creep<br>Denies</a></td>");
	}
	//Neutral Kills
	if($sortcat == "neutralkills")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=neutralkills&o=desc\">Neutral<br>Kills</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=neutralkills&o=asc\">Neutral<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=hero&hid=$heroid&s=neutralkills&o=desc\">Neutral<br>Kills</a></td>");
	}
  ?>
  </tr>
  </table>
<div id="datawrapper">
	<table class="table" id="data" align="center">
 <?php 
  $sql = "Select CASE WHEN (deaths = 0 and kills = 0) THEN 0 WHEN (deaths = 0) then 1000 ELSE (kills*1.0/deaths) end as kdratio, a.gameid as gameid, gamename, kills, deaths, assists, creepkills, neutralkills, creepdenies, towerkills, raxkills, courierkills, name, CASE when(gamestate = '17') then 'PRIV' else 'PUB' end as type, 
  CASE when (winner=1 and newcolour < 6) or (winner=2 and newcolour > 5) then 'WON' when  winner=0 then 'DRAW' else 'LOST' end as result
 FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN dotagames AS c ON c.gameid = a.gameid 
 LEFT JOIN games AS d ON d.id = a.gameid LEFT JOIN originals as e ON a.hero = heroid where heroid = '$heroid' ORDER BY $sortcat $order, name DESC";
if($dbType == 'sqlite')
{
foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
	$gameid = $row["gameid"];
	$kills=$row["kills"];
	$death=$row["deaths"];
    $assists=$row["assists"];
	$kdratio=$row["kdratio"];
	$gamename=$row["gamename"];
	$name=$row["name"];
	$winner=$row["result"];
	$type=$row["type"];
	$creepkills=$row["creepkills"];
	$creepdenies=$row["creepdenies"];
	$neutralkills=$row["neutralkills"];
 ?> 
 <tr class="row">
    <td width=15%><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc"><?php print $name;?></td>
    <td><a href="?p=gameinfo&gid=<?php print $gameid; ?>" target="_self"><?php print $gamename;?></a></td>
    <td width=70px><?php print $type;?></td>
	<td width=70px> <span <?php if($winner == 'LOST'){print 'style="color:#cc6666"';}elseif($winner == 'WON'){print 'style="color:#66cc66"';} else{print 'style="color:#daa701"';} ?>><?php print $winner;?></span></td>
    <td width=70px><?php print $kills;?></td>
    <td width=70px><?php print $death;?></td>
    <td width=70px><?php print $assists;?></td>
	<td width=70px><?php print ROUND($kdratio,2);?></td>
	<td width=70px><?php print $creepkills;?></td>
    <td width=70px><?php print $creepdenies;?></td>
	<td width=70px><?php print $neutralkills;?></td>
 </tr>	
	<?php
	}
}
else
{
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
$gameid = $row["gameid"];
	$kills=$row["kills"];
	$death=$row["deaths"];
    $assists=$row["assists"];
	$kdratio=$row["kdratio"];
	$gamename=$row["gamename"];
	$name=$row["name"];
	$winner=$row["result"];
	$type=$row["type"];
	$creepkills=$row["creepkills"];
	$creepdenies=$row["creepdenies"];
	$neutralkills=$row["neutralkills"];
	$towerkills=$row["towerkills"];
	$raxkills=$row["raxkills"];

 ?> 
 <tr class="row">
    <td width=15%><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc"><?php print $name;?></td>
    <td><a href="?p=gameinfo&gid=<?php print $gameid; ?>" target="_self"><?php print $gamename;?></a></td>
    <td width=70px><?php print $type;?></td>
	<td width=70px> <span <?php if($winner == 'LOST'){print 'style="color:#cc6666"';}elseif($winner == 'WON'){print 'style="color:#66cc66"';} else{print 'style="color:#daa701"';} ?>><?php print $winner;?></span></td>
    <td width=70px><?php print $kills;?></td>
    <td width=70px><?php print $death;?></td>
    <td width=70px><?php print $assists;?></td>
	<td width=70px><?php print ROUND($kdratio,2);?></td>
	<td width=70px><?php print $creepkills;?></td>
    <td width=70px><?php print $creepdenies;?></td>
	<td width=70px><?php print $neutralkills;?></td>
 </tr>	
	<?php
	}
}
	?>
</table>
</div>
}