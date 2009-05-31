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
	$offset=sqlite_escape_string($_GET["n"]);
}
else
{
	$sortcat=mysql_real_escape_string($_GET["s"]);
	$order=mysql_real_escape_string($_GET["o"]);
	$offset=mysql_real_escape_string($_GET["n"]);
}

$sql = "SELECT COUNT( DISTINCT id ) as totgames from games where map LIKE '%dota allstars%'";

if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$count=$row["totgames"];
	}
}
else
{
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$count=$row["totgames"];
	}
	mysql_free_result($result);
}
$pages = ceil($count/$gameResultSize);
?>

	<table class="table" id="theader">
		<tr>
			<td colspan=6>
				<table class="rowuh" width=100%>
					<tr>
						<td width=25%>
							<table class="rowuh" width = 235px style="float:left">
								<h4>
								<tr>
									<td>
									<?php
									if($offset == 'all')
									{
										print "Showing All Games";
									}
									else
									{
										print "<a href=\"?p=games&s=".$sortcat."&o=".$order."&n=all\"><strong>Show All Games</strong></a>";
									}
									?>
									</td>
								</tr>
								</h4>
							</table>
						</td>
						<td width=50%>
							<h2>Recent Games:</h2>
							<h5>Number of Games Played: <?php print $count; ?> </h5>
						</td>
						<td width=25% class="rowuh">
							<table class="rowuh" width = 235px style="float:right">
							<h4>
							<tr>
								<td colspan=7>
								<?php
								if($offset == 'all')
								{
									print "Show Games Page:";
								}
								else
								{
									$min = $offset*$gameResultSize+1;
									$max = $offset*$gameResultSize+$gameResultSize;
									if($max > $count)
									{
										$max = $count;
									}
									print "Showing Games: ".$min." - ".$max;
								}
								?>
								</td>
							</tr>
							<tr>
							<?php
							if($offset == 'all')
							{
								print "<td width=35px><span style=\"color:#ddd;\"><</span></td>";
								for($counter = 1; $counter < 6; $counter++)
								{
									if($counter <= $pages)
									{
									print "<td width=35px><a href=\"?p=games&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
									}
								}
								print "<td width=35px><span style=\"color:#ddd;\">></span></td>";
							}
							else
							{
								if($offset > 0)
								{
									print "<td width=35px><a href=\"?p=games&s=".$sortcat."&o=".$order."&n=".($offset-1)."\"><strong><</strong></a>";
								}
								else
								{
									print "<td width=35px><span style=\"color:#ddd;\"><</span></td>";
								}
								
								if($offset < 2)		//Close to start
								{
									if($offset == 0)
									{
										print "<td width=35px><span style=\"color:#ddd;\">1</span></td>";
										for($counter = 2; $counter < 6; $counter++)
										{
											if($counter-1 < $pages)
											{
												print "<td width=35px><a href=\"?p=games&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
											}
										}
									}
									if($offset == 1)
									{
										print "<td width=35px><a href=\"?p=games&s=".$sortcat."&o=".$order."&n=0\">1</a></td>";
										print "<td width=35px><span style=\"color:#ddd;\">2</span></td>";
										for($counter = 3; $counter < 6; $counter++)
										{
											if($counter-1 < $pages)
											{
											print "<td width=35px><a href=\"?p=games&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
											
											}
										}
									}
								}
								else if ($pages-$offset < 3) //Close to end
								{
									if($offset == $pages-1)
									{
										for($counter = $offset-3; $counter < $offset+1; $counter++)
										{
											if($counter >= 1)
											{
											print "<td width=35px><a href=\"?p=games&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
											}
										}
										print "<td width=35px><span style=\"color:#ddd;\">".$counter."</span></td>";
									}
									else
									{
										
										for($counter = $offset-2; $counter < $offset+1; $counter++)
										{
											if($counter >= 1)
											{
												print "<td width=35px><a href=\"?p=games&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
											}
										}
										print "<td width=35px><span style=\"color:#ddd;\">".($offset+1)."</span>";
										print "<td width=35px><a href=\"?p=games&s=".$sortcat."&o=".$order."&n=".($offset+1)."\">".($offset+2)."</a></td>";
									}
								}
								else
								{
									for($counter = ($offset-1); $counter < ($offset+4); $counter++)
										{
										if($counter == ($offset+1))
										{
											print "<td width=35px><span style=\"color:#ddd;\">".$counter."</span></td>";
										}
										else
										{
											print "<td width=35px><a href=\"?p=games&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
										}
									}
								}
								if(($offset+1)*$gameResultSize < $count)
								{
									print "<td width=35px><a href=\"?p=games&s=".$sortcat."&o=".$order."&n=".($offset+1)."\"><strong>></strong></a></td>";
								}
								else
								{
									print "<td width=35px><span style=\"color:#ddd;\">></span></td>";
								}
							}
							?>
							</tr>
							</h4>
							</table>
						</td>			
					</tr>
				</table>
			</td>
		</tr>

  <tr class="tableheader">
 <?php
 if($offset == 'all')
 {
	//Time
	if($sortcat == "datetime")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=games&s=datetime&o=desc&n=all\">Time</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=games&s=datetime&o=asc&n=all\">Time</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=games&s=datetime&o=desc&n=all\">Time</a></td>");
	}
	//Map
	if($sortcat == "map")
	{
		if($order == "asc")
		{
			print("<td width=35%><a href=\"?p=games&s=map&o=desc&n=all\">Map</a></td>");
		}
		else
		{
			print("<td width=35%><a href=\"?p=games&s=map&o=asc&n=all\">Map</a></td>");
		}
	}
	else
	{
		print("<td width=35%><a href=\"?p=games&s=map&o=asc&n=all\">Map</a></td>");
	}
	//Game Type
	if($sortcat == "type")
	{
		if($order == "asc")
		{
			print("<td width=5%><a href=\"?p=games&s=type&o=desc&n=all\">Type</a></td>");
		}
		else
		{
			print("<td width=5%><a href=\"?p=games&s=type&o=asc&n=all\">Type</a></td>");
		}
	}
	else
	{
		print("<td width=5%><a href=\"?p=games&s=type&o=desc&n=all\">Type</a></td>");
	}	
	//Game
	if($sortcat == "gamename")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=games&s=gamename&o=desc&n=all\">Game</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=games&s=gamename&o=asc&n=all\">Game</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=games&s=gamename&o=asc&n=all\">Game</a></td>");
	}
	
	//Duration
	if($sortcat == "duration")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=games&s=duration&o=desc&n=all\">Duration</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=games&s=duration&o=asc&n=all\">Duration</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=games&s=duration&o=desc&n=all\">Duration</a></td>");
	}
	
	//Creator
	if($sortcat == "creatorname")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=games&s=creatorname&o=desc&n=all\">Creator</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=games&s=creatorname&o=asc&n=all\">Creator</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=games&s=creatorname&o=asc&n=all\">Creator</a></td>");
	}
 }
 else
 {
 //Time
	if($sortcat == "datetime")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=games&s=datetime&o=desc&n=0\">Time</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=games&s=datetime&o=asc&n=0\">Time</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=games&s=datetime&o=desc&n=0\">Time</a></td>");
	}
	//Map
	if($sortcat == "map")
	{
		if($order == "asc")
		{
			print("<td width=35%><a href=\"?p=games&s=map&o=desc&n=0\">Map</a></td>");
		}
		else
		{
			print("<td width=35%><a href=\"?p=games&s=map&o=asc&n=0\">Map</a></td>");
		}
	}
	else
	{
		print("<td width=35%><a href=\"?p=games&s=map&o=asc&n=0\">Map</a></td>");
	}
	//Game Type
	if($sortcat == "type")
	{
		if($order == "asc")
		{
			print("<td width=5%><a href=\"?p=games&s=type&o=desc&n=0\">Type</a></td>");
		}
		else
		{
			print("<td width=5%><a href=\"?p=games&s=type&o=asc&n=0\">Type</a></td>");
		}
	}
	else
	{
		print("<td width=5%><a href=\"?p=games&s=type&o=desc&n=0\">Type</a></td>");
	}	
	//Game
	if($sortcat == "gamename")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=games&s=gamename&o=desc&n=0\">Game</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=games&s=gamename&o=asc&n=0\">Game</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=games&s=gamename&o=asc&n=0\">Game</a></td>");
	}
	
	//Duration
	if($sortcat == "duration")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=games&s=duration&o=desc&n=0\">Duration</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=games&s=duration&o=asc&n=0\">Duration</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=games&s=duration&o=desc&n=0\">Duration</a></td>");
	}
	
	//Creator
	if($sortcat == "creatorname")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=games&s=creatorname&o=desc&n=0\">Creator</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=games&s=creatorname&o=asc&n=0\">Creator</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=games&s=creatorname&o=asc&n=0\">Creator</a></td>");
	}
 }
?> 
  </tr>
  </table>

  <div id="datawrapper">
	<table class="table" id="data">
 <?php 
 
$sql = "SELECT g.id, map, datetime, gamename, ownername, duration, creatorname, dg.winner, CASE when(gamestate = '17') then 'PRIV' else 'PUB' end as type FROM games as g LEFT JOIN dotagames as dg ON g.id = dg.gameid where map LIKE '%dota allstars%' ORDER BY $sortcat $order, datetime desc";

if($offset!='all')
{
$sql = $sql." LIMIT ".$gameResultSize*$offset.", $gameResultSize";
}

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
			<td width=15% align=center><a href="?p=user&u=<?php print $creator; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>" target="_self"><Strong><?php print $creator;?></strong></a></td>
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
    <td width=15% align=center><a href="?p=user&u=<?php print $creator; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>" target="_self"><Strong><?php print $creator;?></strong></a></td>
	</tr>
	<?php
	}
	mysql_free_result($result);
}
	?>
</table>
</div>
