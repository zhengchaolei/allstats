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

require_once("functions.php");
require_once("config.php");

if($dbType == 'sqlite')
{
	$games=sqlite_escape_string($_GET["g"]);
	$sortcat=sqlite_escape_string($_GET["s"]);
	$order=sqlite_escape_string($_GET["o"]);
}
else
{
	$games=mysql_real_escape_string($_GET["g"]);
	$sortcat=mysql_real_escape_string($_GET["s"]);
	$order=mysql_real_escape_string($_GET["o"]);

}
?>

<table class="table" id="theader">
<tr>
<td colspan=14>
<h2>Top Player Statistics <?php if($ignorePubs){ print "for Private Games";} else if($ignorePrivs){ print "for Public Games";} else { print "for All Games";} ?>:</h2>
</td>
</tr>
<tr>
<td class="rowuh" colspan=2>

<FORM action="" method=POST id=form1 name=form1>
     Minimum Games Played:
	 <input type="text" id=games name=games value="<?php print $games;?>">
	   
	 <input type="button" id=button name=button onClick="gamesPlayed(document.getElementById('games').value)" value="Update" style="width:80px; color:#daa701;">
</FORM>
</td>
<td colspan=4 class="tableheader">Total:</td>
<td colspan=9 class="tableheader">Average Per Game:</td>
</tr>
<tr  class="tableheader">
	<td width=25px>#</td>
<?php
	//User Name
	if($sortcat == "name")
	{
		if($order == "asc")
		{
			print("<td><a href=\"?p=top&s=name&o=desc&g=".$games."\">Name</a></td>");
		}
		else
		{
			print("<td><a href=\"?p=top&s=name&o=asc&g=".$games."\">Name</a></td>");
		}
	}
	else
	{
		print("<td><a href=\"?p=top&s=name&o=asc&g=".$games."\">Name</a></td>");
	}
	//Score
	if($sortcat == "totalscore")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=totalscore&o=desc&g=".$games."\">Score</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=totalscore&o=asc&g=".$games."\">Score</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=totalscore&o=desc&g=".$games."\">Score</a></td>");
	}
	//Games
	if($sortcat == "totgames")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=totgames&o=desc&g=".$games."\">Games</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=totgames&o=asc&g=".$games."\">Games</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=totgames&o=desc&g=".$games."\">Games</a></td>");
	}
	//Wins
	if($sortcat == "wins")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=wins&o=desc&g=".$games."\">Wins</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=wins&o=asc&g=".$games."\">Wins</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=wins&o=desc&g=".$games."\">Wins</a></td>");
	}
	//Losses
	if($sortcat == "losses")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=losses&o=desc&g=".$games."\">Losses</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=losses&o=asc&g=".$games."\">Losses</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=losses&o=desc&g=".$games."\">Losses</a></td>");
	}
	//Kills
	if($sortcat == "kills")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=kills&o=desc&g=".$games."\">Kills</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=kills&o=asc&g=".$games."\">Kills</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=kills&o=desc&g=".$games."\">Kills</a></td>");
	}
	//Deaths
	if($sortcat == "deaths")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=deaths&o=desc&g=".$games."\">Deaths</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=deaths&o=asc&g=".$games."\">Deaths</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=deaths&o=desc&g=".$games."\">Deaths</a></td>");
	}
	//Assists
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=assists&o=desc&g=".$games."\">Assists</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=assists&o=asc&g=".$games."\">Assists</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=assists&o=desc&g=".$games."\">Assists</a></td>");
	}
	//KDRatio
	if($sortcat == "killdeathratio")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=killdeathratio&o=desc&g=".$games."\">Kills/<br>Death</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=killdeathratio&o=asc&g=".$games."\">Kills/<br>Death</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=killdeathratio&o=desc&g=".$games."\">Kills/<br>Death</a></td>");
	}
	//Creep Kills
	if($sortcat == "creepkills")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=creepkills&o=desc&g=".$games."\">Creep<br>Kills</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=creepkills&o=asc&g=".$games."\">Creep<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=creepkills&o=desc&g=".$games."\">Creep<br>Kills</a></td>");
	}
	//Denies
	if($sortcat == "creepdenies")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=creepdenies&o=desc&g=".$games."\">Creep<br>Denies</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=creepdenies&o=asc&g=".$games."\">Creep<br>Denies</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=creepdenies&o=desc&g=".$games."\">Creep<br>Denies</a></td>");
	}
	//Neutral Kills
	if($sortcat == "neutralkills")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=neutralkills&o=desc&g=".$games."\">Neutral<br>Kills</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=neutralkills&o=asc&g=".$games."\">Neutral<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=neutralkills&o=desc&g=".$games."\">Neutral<br>Kills</a></td>");
	}
	//Tower Kills
	if($sortcat == "towerkills")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=towerkills&o=desc&g=".$games."\">Tower<br>Kills</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=towerkills&o=asc&g=".$games."\">Tower<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=towerkills&o=desc&g=".$games."\">Tower<br>Kills</a></td>");
	}
	//Rax Kills
	if($sortcat == "raxkills")
	{
		if($order == "asc")
		{
			print("<td width=6%><a href=\"?p=top&s=raxkills&o=desc&g=".$games."\">Rax<br>Kills</a></td>");
		}
		else
		{
			print("<td width=6%><a href=\"?p=top&s=raxkills&o=asc&g=".$games."\">Rax<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=6%><a href=\"?p=top&s=raxkills&o=desc&g=".$games."\">Rax<br>Kills</a></td>");
	}
?>
</tr>
</table>
<div id="datawrapper">
	<table class="table" id="data">
	
<?php
if($scoreFromDB)	//Using score table
{
$sql = "select *,(kills/deaths) as killdeathratio, (totgames-wins) as losses from (
select gp.name as name,gp.gameid as gameid, gp.colour as colour, avg(dp.courierkills) as courierkills, avg(dp.raxkills) as raxkills,
avg(dp.towerkills) as towerkills, avg(dp.assists) as assists, avg(dp.creepdenies) as creepdenies, avg(dp.creepkills) as creepkills,
avg(dp.neutralkills) as neutralkills, avg(dp.deaths) as deaths, avg(dp.kills) as kills, sc.score as totalscore, 
count(*) as totgames, SUM(case when((dg.winner = 1 and dp.newcolour < 6) or (dg.winner = 2 and dp.newcolour > 6)) then 1 else 0 end) as wins
from gameplayers as gp, dotagames as dg, games as ga,dotaplayers as dp, scores as sc where dg.winner <> 0 and dp.gameid = gp.gameid 
and dg.gameid = dp.gameid and dp.gameid = ga.id and gp.gameid = dg.gameid and gp.colour = dp.colour and sc.name = gp.name group by gp.name 
having totgames >= $games) 
as h ORDER BY $sortcat $order, name asc";
}
else			//Using score formula
{
$sql = "select *, ($scoreFormula) as totalscore from(select *, (kills/deaths) as killdeathratio, (totgames-wins) as losses from (
select gp.name as name,gp.gameid as gameid, gp.colour as colour, avg(dp.courierkills) as courierkills, avg(dp.raxkills) as raxkills,
avg(dp.towerkills) as towerkills, avg(dp.assists) as assists, avg(dp.creepdenies) as creepdenies, avg(dp.creepkills) as creepkills,
avg(dp.neutralkills) as neutralkills, avg(dp.deaths) as deaths, avg(dp.kills) as kills,
count(*) as totgames, SUM(case when((dg.winner = 1 and dp.newcolour < 6) or (dg.winner = 2 and dp.newcolour > 6)) then 1 else 0 end) as wins
from gameplayers as gp, dotagames as dg, games as ga,dotaplayers as dp where dg.winner <> 0 and dp.gameid = gp.gameid 
and dg.gameid = dp.gameid and dp.gameid = ga.id and gp.gameid = dg.gameid and gp.colour = dp.colour";

if($ignorePubs)
{
$sql = $sql." and gamestate = '17'";
}
else if($ignorePrivs)
{
$sql = $sql." and gamestate = '16'";
}

$sql = $sql." group by gp.name 
having totgames >= $games) 
as h) as i ORDER BY $sortcat $order, name asc";
}

if($dbType == 'sqlite')
{
	$rank = 1;
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$name=$row["name"];
		$totgames=$row["totgames"];
		$kills=$row["kills"];
		$death=$row["deaths"];
		$assists=$row["assists"];
		$creepkills=$row["creepkills"];
		$creepdenies=$row["creepdenies"];
		$neutralkills=$row["neutralkills"];
		$towerkills=$row["towerkills"];
		$raxkills=$row["raxkills"];
		$courierkills=$row["courierkills"];
		$wins=$row["wins"];
		$losses=$row["losses"];
		$totalscore=$row["totalscore"];
		$killdeathratio=ROUND($row["killdeathratio"],2);

	?>
	<tr class="row">
	<td width=25px><?php print $rank; ?></td>
	<td><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc"><?php print $name; ?></a></td>
	<td width=6%><?php print ROUND($totalscore,2); ?></td>
	<td width=6%><?php print $totgames;?></td>
	<td width=6%><?php print $wins; ?></td>
	<td width=6%><?php print $losses; ?></td>
	<td width=6%><?php print ROUND($kills,1); ?></td>
	<td width=6%><?php print ROUND($death,1); ?></td>
	<td width=6%><?php print ROUND($assists,1); ?></td>
	<td width=6%><?php print $killdeathratio; ?></td>

	<td width=6%><?php print ROUND($creepkills,1) ?></td>
	<td width=6%><?php print ROUND($creepdenies,1); ?></td>
	<td width=6%><?php print ROUND($neutralkills,1); ?></td>
	<td width=6%><?php print ROUND($towerkills,1); ?></td>
	<td width=6%><?php print ROUND($raxkills,1); ?></td>


	</tr>

	<?php
	$rank = $rank + 1;
	}
}
else
{
	$rank = 1;
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

		$name=$row["name"];
		$totgames=$row["totgames"];
		$kills=$row["kills"];
		$death=$row["deaths"];
		$assists=$row["assists"];
		$creepkills=$row["creepkills"];
		$creepdenies=$row["creepdenies"];
		$neutralkills=$row["neutralkills"];
		$towerkills=$row["towerkills"];
		$raxkills=$row["raxkills"];
		$courierkills=$row["courierkills"];
		$wins=$row["wins"];
		$losses=$row["losses"];
		$totalscore=$row["totalscore"];
		$killdeathratio=ROUND($row["killdeathratio"],2);

	?>
	<tr class="row">
	<td width=25px><?php print $rank; ?></td>
	<td><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc"><?php print $name; ?></a></td>
	<td width=6%><?php print ROUND($totalscore,2); ?></td>
	<td width=6%><?php print $totgames;?></td>
	<td width=6%><?php print $wins; ?></td>
	<td width=6%><?php print $losses; ?></td>
	<td width=6%><?php print ROUND($kills,1); ?></td>
	<td width=6%><?php print ROUND($death,1); ?></td>
	<td width=6%><?php print ROUND($assists,1); ?></td>
	<td width=6%><?php print $killdeathratio; ?></td>

	<td width=6%><?php print ROUND($creepkills,1) ?></td>
	<td width=6%><?php print ROUND($creepdenies,1); ?></td>
	<td width=6%><?php print ROUND($neutralkills,1); ?></td>
	<td width=6%><?php print ROUND($towerkills,1); ?></td>
	<td width=6%><?php print ROUND($raxkills,1); ?></td>


	</tr>

	<?php
		$rank = $rank + 1;
	}
	mysql_free_result($result);
}
?>
<tr class="row" height=10px>
</tr>
<tr class="tableheader">
<td colspan=15>
Only players who have played <?php print $games; ?> or more games on <?php print $botName; ?> will be displayed
</td>
</tr>
</table>
</div>
