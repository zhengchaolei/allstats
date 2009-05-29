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
	$sortcat=sqlite_escape_string($_GET["s"]);
	$order=sqlite_escape_string($_GET["o"]);
}
else
{
	$sortcat=mysql_real_escape_string($_GET["s"]);
	$order=mysql_real_escape_string($_GET["o"]);
}
?>

<table class="table" id="theader">
	<tr>
	<td colspan=13>
	<h2>Hero Statistics <?php if($ignorePubs){ print "for Private Games";} else if($ignorePrivs){ print "for Public Games";} else { print "for All Games";} ?>:</h2>
	</td>
	</tr>
  <tr>
	<td class="rowuh"colspan=2></td>
	<td class="tableheader" colspan=4>Total:</td>
	<td class="tableheader" colspan=7>Average Per Game:</td>
  </tr>
  <tr class="tableheader">
  <?php
	//Hero name
	if($sortcat == "description")
	{
		if($order == "asc")
		{
			print("<td colspan=2><a href=\"?p=heroall&s=description&o=desc\">Hero</a></td>");
		}
		else
		{
			print("<td colspan=2><a href=\"?p=heroall&s=description&o=asc\">Hero</a></td>");
		}
	}
	else
	{
		print("<td colspan=2><a href=\"?p=heroall&s=description&o=asc\">Hero</a></td>");
	}
	
	//Times played
	if($sortcat == "totgames")
	{
		if($order == "asc")
		{
			print("<td width=7%><a href=\"?p=heroall&s=totgames&o=desc\">Times<br>Played</a></td>");
		}
		else
		{
			print("<td width=7%><a href=\"?p=heroall&s=totgames&o=asc\">Times<br>Played</a></td>");
		}
	}
	else
	{
		print("<td width=7%><a href=\"?p=heroall&s=totgames&o=desc\">Times<br>Played</a></td>");
	}
	
	//Wins
	if($sortcat == "wins")
	{
		if($order == "asc")
		{
			print("<td width=7%><a href=\"?p=heroall&s=wins&o=desc\">Wins</a></td>");
		}
		else
		{
			print("<td width=7%><a href=\"?p=heroall&s=wins&o=asc\">Wins</a></td>");
		}
	}
	else
	{
		print("<td width=7%><a href=\"?p=heroall&s=wins&o=desc\">Wins</a></td>");
	}
	
	//Losses
	if($sortcat == "losses")
	{
		if($order == "asc")
		{
			print("<td width=7%><a href=\"?p=heroall&s=losses&o=desc\">Losses</a></td>");
		}
		else
		{
			print("<td width=7%><a href=\"?p=heroall&s=losses&o=asc\">Losses</a></td>");
		}
	}
	else
	{
		print("<td width=7%><a href=\"?p=heroall&s=losses&o=desc\">Losses</a></td>");
	}
	
	//Win/Game ratio
	if($sortcat == "winratio")
	{
		if($order == "asc")
		{
			print("<td width=7%><a href=\"?p=heroall&s=winratio&o=desc\">Win/Game<br>Ratio</a></td>");
		}
		else
		{
			print("<td width=7%><a href=\"?p=heroall&s=winratio&o=asc\">Win/Game<br>Ratio</a></td>");
		}
	}
	else
	{
		print("<td width=7%><a href=\"?p=heroall&s=winratio&o=desc\">Win/Game<br>Ratio</a></td>");
	}
	
	//Kills
	if($sortcat == "kills")
	{
		if($order == "asc")
		{
			print("<td width=7%><a href=\"?p=heroall&s=kills&o=desc\">Kills</a></td>");
		}
		else
		{
			print("<td width=7%><a href=\"?p=heroall&s=kills&o=asc\">Kills</a></td>");
		}
	}
	else
	{
		print("<td width=7%><a href=\"?p=heroall&s=kills&o=desc\">Kills</a></td>");
	}
	
	//Deaths
	if($sortcat == "deaths")
	{
		if($order == "asc")
		{
			print("<td width=7%><a href=\"?p=heroall&s=deaths&o=desc\">Deaths</a></td>");
		}
		else
		{
			print("<td width=7%><a href=\"?p=heroall&s=deaths&o=asc\">Deaths</a></td>");
		}
	}
	else
	{
		print("<td width=7%><a href=\"?p=heroall&s=deaths&o=desc\">Deaths</a></td>");
	}
	
	//Assists
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=7%><a href=\"?p=heroall&s=assists&o=desc\">Assists</a></td>");
		}
		else
		{
			print("<td width=7%><a href=\"?p=heroall&s=assists&o=asc\">Assists</a></td>");
		}
	}
	else
	{
		print("<td width=7%><a href=\"?p=heroall&s=assists&o=desc\">Assists</a></td>");
	}
	
	//KDRatio
	if($sortcat == "kdratio")
	{
		if($order == "asc")
		{
			print("<td width=7%><a href=\"?p=heroall&s=kdratio&o=desc\">K/D<br>Ratio</a></td>");
		}
		else
		{
			print("<td width=7%><a href=\"?p=heroall&s=kdratio&o=asc\">K/D<br>Ratio</a></td>");
		}
	}
	else
	{
		print("<td width=7%><a href=\"?p=heroall&s=kdratio&o=desc\">K/D<br>Ratio</a></td>");
	}
	
	//Creep Kills
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=7%><a href=\"?p=heroall&s=creepkills&o=desc\">Creep<br>Kills</a></td>");
		}
		else
		{
			print("<td width=7%><a href=\"?p=heroall&s=creepkills&o=asc\">Creep<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=7%><a href=\"?p=heroall&s=creepkills&o=desc\">Creep<br>Kills</a></td>");
	}
	
	//Creep Denies
	if($sortcat == "creepdenies")
	{
		if($order == "asc")
		{
			print("<td width=7%><a href=\"?p=heroall&s=creepdenies&o=desc\">Creep<br>Denies</a></td>");
		}
		else
		{
			print("<td width=7%><a href=\"?p=heroall&s=creepdenies&o=asc\">Creep<br>Denies</a></td>");
		}
	}
	else
	{
		print("<td width=7%><a href=\"?p=heroall&s=creepdenies&o=desc\">Creep<br>Denies</a></td>");
	}
	
	//Neutral Kills
	if($sortcat == "neutralkills")
	{
		if($order == "asc")
		{
			print("<td width=7%><a href=\"?p=heroall&s=neutralkills&o=desc\">Neutral<br>Kills</a></td>");
		}
		else
		{
			print("<td width=7%><a href=\"?p=heroall&s=neutralkills&o=asc\">Neutral<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=7%><a href=\"?p=heroall&s=neutralkills&o=desc\">Neutral<br>Kills</a></td>");
	}
	
?>
  </tr>
</table>
<div id="datawrapper">
	<table class="table" id="data">
	<?php
	$sql = "Select *, (totgames-wins) as losses, (kills*1.0/deaths) as kdratio, (wins*1.0/totgames) as winratio From 
	(SELECT description, heroid, count(*) as totgames, 
	SUM(case when((c.winner = 1 and a.newcolour < 6) or (c.winner = 2 and a.newcolour > 6)) then 1 else 0 end) as wins, AVG(kills) as kills, AVG(deaths) as deaths, 
	AVG(assists) as assists, AVG(creepkills) as creepkills, AVG(creepdenies) as creepdenies, AVG(neutralkills) as neutralkills
	FROM dotaplayers AS a LEFT JOIN originals as b ON hero = heroid LEFT JOIN dotagames as c ON c.gameid = a.gameid 
	LEFT JOIN gameplayers as d ON d.gameid = a.gameid and a.colour = d.colour LEFT JOIN games as e ON d.gameid = e.id
	WHERE description <>  'NULL' and c.winner <> 0";
	if($ignorePubs)
	{
	$sql = $sql." and gamestate = '17'";
	}
	else if($ignorePrivs)
	{
	$sql = $sql." and gamestate = '16'";
	}
	$sql= $sql." group by description) as z where z.totgames > 0 order by $sortcat $order, description asc";
if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
	$hero=$row["description"];
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
		$hid=$row["heroid"]

	?>
	<tr class="row">
	<td><a href="?p=hero&hid=<?php print $hid;?>&s=kdratio&o=desc"><img width="28px" height="28px" src=./img/heroes/<?php print $hid ?>.gif></a></td>
	<td><a href="?p=hero&hid=<?php print $hid; ?>&s=kdratio&o=desc"><?php print $hero; ?></a></td>
	<td width=7%><?php print $totgames; ?></td>
	<td width=7%><?php print $wins; ?></td>
	<td width=7%><?php print $losses; ?></td>
	<td width=7%><?php print ROUND($winratio,2); ?></td>
	<td width=7%><?php print ROUND($kills,2); ?></td>
	<td width=7%><?php print ROUND($deaths,2); ?></td>
	<td width=7%><?php print ROUND($assists,2); ?></td>
	<td width=7%><?php print ROUND($kdratio,2); ?></td>
	<td width=7%><?php print ROUND($creepkills,2); ?></td>
	<td width=7%><?php print ROUND($creepdenies,2); ?></td>
	<td width=7%><?php print ROUND($neutralkills,2); ?></td>

	</tr>
<?php
	}
}
else
{	
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$hero=$row["description"];
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
		$hid=$row["heroid"]

	?>
	<tr class="row">
	<td><a href="?p=hero&hid=<?php print $hid;?>&s=kdratio&o=desc"><img width="28px" height="28px" src=./img/heroes/<?php print $hid ?>.gif></a></td>
	<td><a href="?p=hero&hid=<?php print $hid; ?>&s=kdratio&o=desc"><?php print $hero; ?></a></td>
	<td width=7%><?php print $totgames; ?></td>
	<td width=7%><?php print $wins; ?></td>
	<td width=7%><?php print $losses; ?></td>
	<td width=7%><?php print ROUND($winratio,2); ?></td>
	<td width=7%><?php print ROUND($kills,2); ?></td>
	<td width=7%><?php print ROUND($deaths,2); ?></td>
	<td width=7%><?php print ROUND($assists,2); ?></td>
	<td width=7%><?php print ROUND($kdratio,2); ?></td>
	<td width=7%><?php print ROUND($creepkills,2); ?></td>
	<td width=7%><?php print ROUND($creepdenies,2); ?></td>
	<td width=7%><?php print ROUND($neutralkills,2); ?></td>

	</tr>

	<?php

	}
	mysql_free_result($result);
}
	?>
	</table>
	</table>
</div>