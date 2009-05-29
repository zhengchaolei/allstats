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
	<td colspan=6>
	<h2>Recent Games:</h2>
	</td>
	</tr>
<?php

$sql = "SELECT COUNT( DISTINCT id ) as totgames from games";

if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$totgames=$row["totgames"];
		?>
		<tr>
			<td colspan=6>
			<h5>Number of Games Played: <?php print $totgames; ?> </h5>
			</td>
		</tr>

		<?php
	}
}
else
{
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$totgames=$row["totgames"];
	?>
	<tr>
		<td colspan=6>
		<h5>Number of Games Played: <?php print $totgames; ?> </h5>
		</td>
	</tr>

	<?php
	}
	mysql_free_result($result);
}
?>


  <tr class="tableheader">
 <?php
	//Time
	if($sortcat == "datetime")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=games&s=datetime&o=desc\">Time</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=games&s=datetime&o=asc\">Time</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=games&s=datetime&o=desc\">Time</a></td>");
	}
	//Map
	if($sortcat == "map")
	{
		if($order == "asc")
		{
			print("<td width=35%><a href=\"?p=games&s=map&o=desc\">Map</a></td>");
		}
		else
		{
			print("<td width=35%><a href=\"?p=games&s=map&o=asc\">Map</a></td>");
		}
	}
	else
	{
		print("<td width=35%><a href=\"?p=games&s=map&o=asc\">Map</a></td>");
	}
	//Game Type
	if($sortcat == "type")
	{
		if($order == "asc")
		{
			print("<td width=5%><a href=\"?p=games&s=type&o=desc\">Type</a></td>");
		}
		else
		{
			print("<td width=5%><a href=\"?p=games&s=type&o=asc\">Type</a></td>");
		}
	}
	else
	{
		print("<td width=5%><a href=\"?p=games&s=type&o=desc\">Type</a></td>");
	}	
	//Game
	if($sortcat == "gamename")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=games&s=gamename&o=desc\">Game</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=games&s=gamename&o=asc\">Game</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=games&s=gamename&o=asc\">Game</a></td>");
	}
	
	//Duration
	if($sortcat == "duration")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=games&s=duration&o=desc\">Duration</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=games&s=duration&o=asc\">Duration</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=games&s=duration&o=desc\">Duration</a></td>");
	}
	
	//Creator
	if($sortcat == "creatorname")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=games&s=creatorname&o=desc\">Creator</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=games&s=creatorname&o=asc\">Creator</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=games&s=creatorname&o=asc\">Creator</a></td>");
	}
 
?> 
  </tr>
  </table>

  <div id="datawrapper">
	<table class="table" id="data">
 <?php 
 
$sql = "SELECT g.id, map, datetime, gamename, ownername, duration, creatorname, dg.winner, CASE when(gamestate = '17') then 'PRIV' else 'PUB' end as type FROM games as g LEFT JOIN dotagames as dg ON g.id = dg.gameid where map LIKE '%dota allstars%' ORDER BY $sortcat $order, datetime desc";

if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$gameid=$row["id"]; 
		$map=$row["map"];
		$type=$row["type"];
		$gametime=$row["datetime"];
		$gamename=$row["gamename"];
		$ownername=$row["ownername"];
		$duration=$row["duration"];
		$creator=$row["creatorname"];
		$winner=$row["winner"];
 ?> 
		<tr class="row">
			<td width=15% align=center><?php print $gametime;?></td>
			<td width=35% align=center><?php print $map;?></td>
			<td width=5% align=center><?php print $type;?></td>
			<td width=15% align=center><a href="?p=gameinfo&gid=<?php print $gameid; ?>" target="_self" <?php if($winner==1){print 'style="color:#cc6666"';}elseif($winner==2){print 'style="color:#66cc66"';}?>><Strong><?php print $gamename;?></strong></a></td>
			<td width=15% align=center><?php print secondsToTime($duration);?></td>
			<td width=15% align=center><a href="?p=user&u=<?php print $creator; ?>&s=datetime&o=desc" target="_self"><Strong><?php print $creator;?></strong></a></td>
		</tr>
		<?php
	}
}
else
{
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$gameid=$row["id"]; 
		$map=$row["map"];
		$type=$row["type"];
		$gametime=$row["datetime"];
		$gamename=$row["gamename"];
		$ownername=$row["ownername"];
		$duration=$row["duration"];
		$creator=$row["creatorname"];
		$winner=$row["winner"];
 ?> 
 <tr class="row">
 <td width=15% align=center><?php print $gametime;?></td>
   
    <td width=35% align=center><?php print $map;?></td>
	<td width=5% align=center><?php print $type;?></td>
    <td width=15% align=center><a href="?p=gameinfo&gid=<?php print $gameid; ?>" target="_self" <?php if($winner==1){print 'style="color:#cc6666"';}elseif($winner==2){print 'style="color:#66cc66"';}?>><Strong><?php print $gamename;?></strong></a></td>
	<td width=15% align=center><?php print secondsToTime($duration);?></td>
    <td width=15% align=center><a href="?p=user&u=<?php print $creator; ?>&s=datetime&o=desc" target="_self"><Strong><?php print $creator;?></strong></a></td>
	</tr>
	<?php
	}
	mysql_free_result($result);
}
	?>
</table>
</div>
