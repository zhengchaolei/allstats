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

$sql = "SELECT COUNT( DISTINCT name ) as players from gameplayers as gp LEFT JOIN games as g ON gp.gameid = g.id";
if($ignorePubs)
{
$sql = $sql." where g.gamestate = '17'";
}
else if($ignorePrivs)
{
$sql = $sql." where g.gamestate = '16'";
}

if($dbType == 'sqlite')
{
foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$totplayers=$row["players"];
?>

	<table class="table" id="theader">
		<tr>
		<td colspan=11>
			<h2>Player Statistics <?php if($ignorePubs){ print "for Private Games";} else if($ignorePrivs){ print "for Public Games";} else { print "for All Games";} ?>:</h2>
		</td>
		</tr>
		<tr>
			<td class="rowuh"colspan=1><h5>Total Players: <?php print $totplayers; ?></h5></td>
			<td class="tableheader" colspan=1>Total:</td>
			<td class="tableheader" colspan=9>Average Per Game:</td>
		</tr>
<?php
	}	
}
else
{
	$result = mysql_query($sql);

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$totplayers=$row["players"];
	?>

	<table class="table" id="theader">
		<tr>
		<td colspan=11>
			<h2>Player Statistics <?php if($ignorePubs){ print "for Private Games";} else if($ignorePrivs){ print "for Public Games";} else { print "for All Games";} ?>:</h2>
		</td>
		</tr>
		<tr>
			<td class="rowuh"colspan=1><h5>Total Players: <?php print $totplayers; ?></h5></td>
			<td class="tableheader" colspan=1>Total:</td>
			<td class="tableheader" colspan=9>Average Per Game:</td>
		</tr>
<?php
	}
	mysql_free_result($result);
}
?>

<tr class="tableheader">

<?php

	//User Name
	if($sortcat == "name")
	{
		if($order == "asc")
		{
			print("<td><a href=\"?p=allusers&s=name&o=desc\">Name</a></td>");
		}
		else
		{
			print("<td><a href=\"?p=allusers&s=name&o=asc\">Name</a></td>");
		}
	}
	else
	{
		print("<td><a href=\"?p=allusers&s=name&o=asc\">Name</a></td>");
	}
	
	//Games
	if($sortcat == "totgames")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=totgames&o=desc\">Games</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=totgames&o=asc\">Games</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=totgames&o=desc\">Games</a></td>");
	}
	
	//Kills
	if($sortcat == "kills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=kills&o=desc\">Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=kills&o=asc\">Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=kills&o=desc\">Kills</a></td>");
	}
	
	//Deaths
	if($sortcat == "deaths")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=deaths&o=desc\">Deaths</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=deaths&o=asc\">Deaths</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=deaths&o=desc\">Deaths</a></td>");
	}
	
	//Assists
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=assists&o=desc\">Assists</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=assists&o=asc\">Assists</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=assists&o=desc\">Assists</a></td>");
	}
	
	//Creep Kills
	if($sortcat == "creepkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=creepkills&o=desc\">Creep<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=creepkills&o=asc\">Creep<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=creepkills&o=desc\">Creep<br>Kills</a></td>");
	}
	
	//Creep Denies
	if($sortcat == "creepdenies")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=creepdenies&o=desc\">Creep<br>Denies</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=creepdenies&o=asc\">Creep<br>Denies</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=creepdenies&o=desc\">Creep<br>Denies</a></td>");
	}
	
	//Neutral Kills
	if($sortcat == "neutralkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=neutralkills&o=desc\">Neutral<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=neutralkills&o=asc\">Neutral<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=neutralkills&o=desc\">Neutral<br>Kills</a></td>");
	}
	
	//Tower Kills
	if($sortcat == "towerkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=towerkills&o=desc\">Tower<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=towerkills&o=asc\">Tower<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=towerkills&o=desc\">Tower<br>Kills</a></td>");
	}
	
	//Rax Kills
	if($sortcat == "raxkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=raxkills&o=desc\">Rax<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=raxkills&o=asc\">Rax<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=raxkills&o=desc\">Rax<br>Kills</a></td>");
	}
	
	//Courier Kills
	if($sortcat == "courierkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=courierkills&o=desc\">Courier<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=courierkills&o=asc\">Courier<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=courierkills&o=desc\">Courier<br>Kills</a></td>");
	}
	
?>
</tr>
</table>

<div id="datawrapper">
	<table class="table" id="data">
<?php
 
$sql = "SELECT COUNT(a.id) as totgames, AVG(kills) as kills, AVG(deaths) as deaths, AVG(assists) as assists,
AVG(creepkills) as creepkills, AVG(creepdenies) as creepdenies,  AVG(neutralkills) as neutralkills, AVG(towerkills) as towerkills, 
AVG(raxkills) as raxkills, AVG(courierkills) as courierkills, name 
FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
LEFT JOIN dotagames AS c ON c.gameid = a.gameid LEFT JOIN games as d ON d.id = c.gameid where c.winner <> 0 and name <> ''";
if($ignorePubs)
{
$sql = $sql." and d.gamestate = '17'";
}
else if($ignorePrivs)
{
$sql = $sql." and d.gamestate = '16'";
}
$sql = $sql." group by name ORDER BY $sortcat $order, name asc";

if($dbType == 'sqlite')
{
foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
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
		$name=$row["name"];
	?>

	<tr class="row">
		<td><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc"><?php print $name; ?></a></td>
		<td width=8%><?php print $totgames; ?></td>
		<td width=8%><?php print ROUND($kills, 1); ?></td>
		<td width=8%><?php print ROUND($death, 1); ?></td>
		<td width=8%><?php print ROUND($assists, 1); ?></td>
		<td width=8%><?php print ROUND($creepkills, 1); ?></td>
		<td width=8%><?php print ROUND($creepdenies, 1); ?></td>
		<td width=8%><?php print ROUND($neutralkills, 1); ?></td>
		<td width=8%><?php print ROUND($towerkills, 1); ?></td>
		<td width=8%><?php print ROUND($raxkills, 1); ?></td>
		<td width=8%><?php print ROUND($courierkills, 1); ?></td>
	</tr>

	<?php
	}
}
else
{
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
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
		$name=$row["name"];

	?>

	<tr class="row">
		<td><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc"><?php print $name; ?></a></td>
		<td width=8%><?php print $totgames; ?></td>
		<td width=8%><?php print ROUND($kills, 1); ?></td>
		<td width=8%><?php print ROUND($death, 1); ?></td>
		<td width=8%><?php print ROUND($assists, 1); ?></td>
		<td width=8%><?php print ROUND($creepkills, 1); ?></td>
		<td width=8%><?php print ROUND($creepdenies, 1); ?></td>
		<td width=8%><?php print ROUND($neutralkills, 1); ?></td>
		<td width=8%><?php print ROUND($towerkills, 1); ?></td>
		<td width=8%><?php print ROUND($raxkills, 1); ?></td>
		<td width=8%><?php print ROUND($courierkills, 1); ?></td>
	</tr>

	<?php

	}
	mysql_free_result($result);
}
?>
</table>
</div>
</center>
</strong>