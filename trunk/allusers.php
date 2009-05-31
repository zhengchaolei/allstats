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

$sql = "SELECT COUNT( DISTINCT name ) as players from gameplayers as gp LEFT JOIN games as g ON gp.gameid = g.id, dotaplayers as dp where dp.gameid = gp.gameid and dp.colour = gp.colour";
if($ignorePubs)
{
$sql = $sql." and g.gamestate = '17'";
}
else if($ignorePrivs)
{
$sql = $sql." and g.gamestate = '16'";
}

if($dbType == 'sqlite')
{
foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$count=$row["players"];
	}	
}
else
{
	$result = mysql_query($sql);

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$count=$row["players"];
		}
	mysql_free_result($result);
}

	$pages = ceil($count/$allPlayerResultSize);
	?>
	<table class="table" id="theader">
		<tr>
			<td colspan=11>
				<table class="rowuh" width=100%>
					<tr>
						<td width=25%>
							<table class="rowuh" width = 235px style="float:left">
								<tr>
									<td>
									<?php
									if($offset == 'all')
									{
										print "Showing All Players";
									}
									else
									{
										print "<a href=\"?p=allusers&s=".$sortcat."&o=".$order."&n=all\"><strong>Show All Players</strong></a>";
									}
									?>
									</td>
								</tr>
							</table>
						</td>
						<td width=50%>
							<h2>Player Statistics <?php if($ignorePubs){ print "for Private Games";} else if($ignorePrivs){ print "for Public Games";} else { print "for All Games";} ?>:</h2>
						</td>
						<td width=25% class="rowuh">
							<table class="rowuh" width = 235px style="float:right">
							<h4>
							<tr>
								<td colspan=7>
								<?php
								if($offset == 'all')
								{
									print "Show Players Page:";
								}
								else
								{
									$min = $offset*$allPlayerResultSize+1;
									$max = $offset*$allPlayerResultSize+$allPlayerResultSize;
									if($max > $count)
									{
										$max = $count;
									}
									print "Showing Players: ".$min." - ".$max;
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
									print "<td width=35px><a href=\"?p=allusers&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
									}
								}
								print "<td width=35px><span style=\"color:#ddd;\">></span></td>";
							}
							else
							{
								if($offset > 0)
								{
									print "<td width=35px><a href=\"?p=allusers&s=".$sortcat."&o=".$order."&n=".($offset-1)."\"><strong><</strong></a>";
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
												print "<td width=35px><a href=\"?p=allusers&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
											}
										}
									}
									if($offset == 1)
									{
										print "<td width=35px><a href=\"?p=allusers&s=".$sortcat."&o=".$order."&n=0\">1</a></td>";
										print "<td width=35px><span style=\"color:#ddd;\">2</span></td>";
										for($counter = 3; $counter < 6; $counter++)
										{
											if($counter-1 < $pages)
											{
											print "<td width=35px><a href=\"?p=allusers&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
											
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
											print "<td width=35px><a href=\"?p=allusers&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
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
												print "<td width=35px><a href=\"?p=allusers&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
											}
										}
										print "<td width=35px><span style=\"color:#ddd;\">".($offset+1)."</span>";
										print "<td width=35px><a href=\"?p=allusers&s=".$sortcat."&o=".$order."&n=".($offset+1)."\">".($offset+2)."</a></td>";
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
											print "<td width=35px><a href=\"?p=allusers&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
										}
									}
								}
								if(($offset+1)*$allPlayerResultSize < $count)
								{
									print "<td width=35px><a href=\"?p=allusers&s=".$sortcat."&o=".$order."&n=".($offset+1)."\"><strong>></strong></a></td>";
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
		<tr>
			<td class="rowuh"colspan=1><h5>Total Players: <?php print $count; ?></h5></td>
			<td class="tableheader" colspan=1>Total:</td>
			<td class="tableheader" colspan=9>Average Per Game:</td>
		</tr>
<tr class="tableheader">

<?php
 if($offset == 'all')
 {
	//User Name
	if($sortcat == "name")
	{
		if($order == "asc")
		{
			print("<td><a href=\"?p=allusers&s=name&o=desc&n=all\">Name</a></td>");
		}
		else
		{
			print("<td><a href=\"?p=allusers&s=name&o=asc&n=all\">Name</a></td>");
		}
	}
	else
	{
		print("<td><a href=\"?p=allusers&s=name&o=asc&n=all\">Name</a></td>");
	}
	
	//Games
	if($sortcat == "totgames")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=totgames&o=desc&n=all\">Games</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=totgames&o=asc&n=all\">Games</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=totgames&o=desc&n=all\">Games</a></td>");
	}
	
	//Kills
	if($sortcat == "kills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=kills&o=desc&n=all\">Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=kills&o=asc&n=all\">Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=kills&o=desc&n=all\">Kills</a></td>");
	}
	
	//Deaths
	if($sortcat == "deaths")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=deaths&o=desc&n=all\">Deaths</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=deaths&o=asc&n=all\">Deaths</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=deaths&o=desc&n=all\">Deaths</a></td>");
	}
	
	//Assists
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=assists&o=desc&n=all\">Assists</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=assists&o=asc&n=all\">Assists</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=assists&o=desc&n=all\">Assists</a></td>");
	}
	
	//Creep Kills
	if($sortcat == "creepkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=creepkills&o=desc&n=all\">Creep<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=creepkills&o=asc&n=all\">Creep<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=creepkills&o=desc&n=all\">Creep<br>Kills</a></td>");
	}
	
	//Creep Denies
	if($sortcat == "creepdenies")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=creepdenies&o=desc&n=all\">Creep<br>Denies</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=creepdenies&o=asc&n=all\">Creep<br>Denies</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=creepdenies&o=desc&n=all\">Creep<br>Denies</a></td>");
	}
	
	//Neutral Kills
	if($sortcat == "neutralkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=neutralkills&o=desc&n=all\">Neutral<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=neutralkills&o=asc&n=all\">Neutral<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=neutralkills&o=desc&n=all\">Neutral<br>Kills</a></td>");
	}
	
	//Tower Kills
	if($sortcat == "towerkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=towerkills&o=desc&n=all\">Tower<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=towerkills&o=asc&n=all\">Tower<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=towerkills&o=desc&n=all\">Tower<br>Kills</a></td>");
	}
	
	//Rax Kills
	if($sortcat == "raxkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=raxkills&o=desc&n=all\">Rax<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=raxkills&o=asc&n=all\">Rax<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=raxkills&o=desc&n=all\">Rax<br>Kills</a></td>");
	}
	
	//Courier Kills
	if($sortcat == "courierkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=courierkills&o=desc&n=all\">Courier<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=courierkills&o=asc&n=all\">Courier<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=courierkills&o=desc&n=all\">Courier<br>Kills</a></td>");
	}
}
else
{
	//User Name
	if($sortcat == "name")
	{
		if($order == "asc")
		{
			print("<td><a href=\"?p=allusers&s=name&o=desc&n=0\">Name</a></td>");
		}
		else
		{
			print("<td><a href=\"?p=allusers&s=name&o=asc&n=0\">Name</a></td>");
		}
	}
	else
	{
		print("<td><a href=\"?p=allusers&s=name&o=asc&n=0\">Name</a></td>");
	}
	
	//Games
	if($sortcat == "totgames")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=totgames&o=desc&n=0\">Games</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=totgames&o=asc&n=0\">Games</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=totgames&o=desc&n=0\">Games</a></td>");
	}
	
	//Kills
	if($sortcat == "kills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=kills&o=desc&n=0\">Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=kills&o=asc&n=0\">Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=kills&o=desc&n=0\">Kills</a></td>");
	}
	
	//Deaths
	if($sortcat == "deaths")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=deaths&o=desc&n=0\">Deaths</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=deaths&o=asc&n=0\">Deaths</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=deaths&o=desc&n=0\">Deaths</a></td>");
	}
	
	//Assists
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=assists&o=desc&n=0\">Assists</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=assists&o=asc&n=0\">Assists</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=assists&o=desc&n=0\">Assists</a></td>");
	}
	
	//Creep Kills
	if($sortcat == "creepkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=creepkills&o=desc&n=0\">Creep<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=creepkills&o=asc&n=0\">Creep<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=creepkills&o=desc&n=0\">Creep<br>Kills</a></td>");
	}
	
	//Creep Denies
	if($sortcat == "creepdenies")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=creepdenies&o=desc&n=0\">Creep<br>Denies</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=creepdenies&o=asc&n=0\">Creep<br>Denies</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=creepdenies&o=desc&n=0\">Creep<br>Denies</a></td>");
	}
	
	//Neutral Kills
	if($sortcat == "neutralkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=neutralkills&o=desc&n=0\">Neutral<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=neutralkills&o=asc&n=0\">Neutral<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=neutralkills&o=desc&n=0\">Neutral<br>Kills</a></td>");
	}
	
	//Tower Kills
	if($sortcat == "towerkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=towerkills&o=desc&n=0\">Tower<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=towerkills&o=asc&n=0\">Tower<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=towerkills&o=desc&n=0\">Tower<br>Kills</a></td>");
	}
	
	//Rax Kills
	if($sortcat == "raxkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=raxkills&o=desc&n=0\">Rax<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=raxkills&o=asc&n=0\">Rax<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=raxkills&o=desc&n=0\">Rax<br>Kills</a></td>");
	}
	
	//Courier Kills
	if($sortcat == "courierkills")
	{
		if($order == "asc")
		{
			print("<td width=8%><a href=\"?p=allusers&s=courierkills&o=desc&n=0\">Courier<br>Kills</a></td>");
		}
		else
		{
			print("<td width=8%><a href=\"?p=allusers&s=courierkills&o=asc&n=0\">Courier<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=8%><a href=\"?p=allusers&s=courierkills&o=desc&n=0\">Courier<br>Kills</a></td>");
	}
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
LEFT JOIN dotagames AS c ON c.gameid = a.gameid LEFT JOIN games as d ON d.id = c.gameid where name <> ''";
if($ignorePubs)
{
$sql = $sql." and d.gamestate = '17'";
}
else if($ignorePrivs)
{
$sql = $sql." and d.gamestate = '16'";
}
$sql = $sql." group by name ORDER BY $sortcat $order, name asc";

if($offset!='all')
{
$sql = $sql." LIMIT ".$allPlayerResultSize*$offset.", $allPlayerResultSize";
}

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
		<td><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $name; ?></a></td>
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
		<td><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $name; ?></a></td>
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