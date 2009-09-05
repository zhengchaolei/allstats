<?php
/*********************************************
<!-- 
*   	DOTA ALLSTATS
*   
*	Developers: Reinert, Dag Morten, Netbrain, Billbabong, Boltergeist.
*	Contact: developer@miq.no - Reinert
*
*	
*	Please see http://forum.codelain.com/index.php?topic=4752.0
*	and post your webpage there, so I know who's using it.
*
*	Files downloaded from http://code.google.com/p/allstats/
*
*	Copyright (C) 2009  Reinert, Dag Morten , Netbrain, Billbabong, Boltergeist
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
	$offset=sqlite_escape_string($_GET["n"]);
	$interval=sqlite_escape_string($_GET["i"]);

	if($interval == 'week' || $interval == 'Week')
	{
		$interval="Week";
		$sqlGroupBy1="strftime('%Y', datetime)";
		$sqlGroupBy2="strftime('%W', datetime)";
		$sqlGroupBy3="strftime('%W', datetime)";
	}
	else
	{
		$interval="Month";
		$sqlGroupBy1="strftime('%Y', datetime)";
		$sqlGroupBy2="strftime('%m', datetime)";
		$sqlGroupBy3="strftime('%m', datetime)";
	}
}
else
{
	$offset=mysql_real_escape_string($_GET["n"]);
	$interval=mysql_real_escape_string($_GET["i"]);

	if($interval == 'week' || $interval == 'Week')
	{
		$interval="Week";
		$sqlGroupBy1="YEAR(datetime)";
		$sqlGroupBy2="WEEK(datetime,3)";
		$sqlGroupBy3="WEEK(datetime,3)";
	}
	else
	{
		$interval="Month";
		$sqlGroupBy1="YEAR(datetime)";
		$sqlGroupBy2="MONTH(datetime)";
		$sqlGroupBy3="MONTHNAME(datetime)";
	}
}


$sql = "select count(*) as count from( SELECT ".$sqlGroupBy1." as y, ".$sqlGroupBy2." as m, ".$sqlGroupBy3." as mn FROM games";
if($ignorePubs)
{
	$sql = $sql." WHERE gamestate = '17'";
}
else if($ignorePrivs)
{
	$sql = $sql." WHERE gamestate = '16'";
}
$sql = $sql." group by ".$sqlGroupBy1.", ".$sqlGroupBy2." 
	order by ".$sqlGroupBy1." desc, ".$sqlGroupBy2." desc) as h";	

if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$count=$row["count"];
	}	
}
else
{
	$result = mysql_query($sql);

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
	{
		$count=$row["count"];
	}
	mysql_free_result($result);
}

$pages = ceil($count/$monthlyTopsResultSize);

?>
<div class="header" id="header">
	<table width=1016px>
		<tr>
			<td width=25%>
				<table class="rowuh" width = 235px style="float:left">
					<h4>
					<tr>
						<td>
						<?php
						if($offset == 'all')
						{
							print "Showing All ".$interval."s";
						}
						else
						{
							print "<a href=\"?p=monthlytop&n=all&i=".$interval."\">Show All ".$interval."s</a>";
						}
						print "<br/><br/>";
						if($interval == 'Week')
						{
							print "<a href=\"?p=monthlytop&n=".$offset."&i=month\">Toggle Montly/Weekly View</a>";
						}
						else
						{
							print "<a href=\"?p=monthlytop&n=".$offset."&i=week\">Toggle Montly/Weekly View</a>";
						}
						?>
						</td>
					</tr>
					</h4>
				</table>
			</td>
			<td width=50%>
				<h2><?php print $interval; ?>ly Tops for <?php if($ignorePubs){ print "Private Games";} else if($ignorePrivs){ print "Public Games";} else { print "All Games";} ?></h2>
			</td>
			<td width=25% class="rowuh">
				<table class="rowuh" width = 235px style="float:right">
				<h4>
				<tr>
					<td colspan=7>
					<?php
					if($offset == 'all')
					{
						print "Show ".$interval."s Page:";
					}
					else
					{
						$min = $offset*$monthlyTopsResultSize+1;
						$max = $offset*$monthlyTopsResultSize+$monthlyTopsResultSize;
						if($max > $count)
						{
							$max = $count;
						}
						print "Showing ".$interval."s: ".$min." - ".$max;
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
						print "<td width=35px><a href=\"?p=monthlytop&n=".($counter-1)."&i=".$interval."\">".$counter."</a></td>";
						}
					}
					print "<td width=35px><span style=\"color:#ddd;\">></span></td>";
				}
				else
				{
					if($offset > 0)
					{
						print "<td width=35px><a href=\"?p=monthlytop&n=".($offset-1)."&i=".$interval."\"><</a>";
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
									print "<td width=35px><a href=\"?p=monthlytop&n=".($counter-1)."&i=".$interval."\">".$counter."</a></td>";
								}
							}
						}
						if($offset == 1)
						{
							print "<td width=35px><a href=\"?p=monthlytop&n=0&i=".$interval."\">1</a></td>";
							print "<td width=35px><span style=\"color:#ddd;\">2</span></td>";
							for($counter = 3; $counter < 6; $counter++)
							{
								if($counter-1 < $pages)
								{
								print "<td width=35px><a href=\"?p=monthlytop&n=".($counter-1)."&i=".$interval."\">".$counter."</a></td>";
								
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
								print "<td width=35px><a href=\"?p=monthlytop&n=".($counter-1)."&i=".$interval."\">".$counter."</a></td>";
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
									print "<td width=35px><a href=\"?p=monthlytop&n=".($counter-1)."&i=".$interval."\">".$counter."</a></td>";
								}
							}
							print "<td width=35px><span style=\"color:#ddd;\">".($offset+1)."</span>";
							print "<td width=35px><a href=\"?p=monthlytop&n=".($offset+1)."&i=".$interval."\">".($offset+2)."</a></td>";
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
								print "<td width=35px><a href=\"?p=monthlytop&n=".($counter-1)."&i=".$interval."\">".$counter."</a></td>";
							}
						}
					}
					if(($offset+1)*$monthlyTopsResultSize < $count)
					{
						print "<td width=35px><a href=\"?p=monthlytop&n=".($offset+1)."&i=".$interval."\">></a></td>";
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
</div>
<div class="pageholder" id="pageholder">
	<div id="theader">

	</div>

	<div id="datawrapper">
		


<?php
if($dbType == 'sqlite')
{
	$sql0 = "SELECT ".$sqlGroupBy1." as y, ".$sqlGroupBy2." as m, ".$sqlGroupBy3." as mn FROM games";
	if($ignorePubs)
	{
		$sql0 = $sql0." WHERE gamestate = '17'";
	}
	else if($ignorePrivs)
	{
		$sql0 = $sql0." WHERE gamestate = '16'";
	}
	$sql0 = $sql0." group by ".$sqlGroupBy1.", ".$sqlGroupBy2." 
		order by ".$sqlGroupBy1." desc, ".$sqlGroupBy2." desc";
	if($offset!='all')
	{
		$sql0 = $sql0." LIMIT ".$monthlyTopsResultSize*$offset.", $monthlyTopsResultSize";
	}

	foreach ($dbHandle->query($sql0, PDO::FETCH_ASSOC) as $row0)
	{

		$year=$row0["y"];
		$month=$row0["m"];
		$monthname=$row0["mn"];


	?>

	<table class="table" width=1000px>
		<tr>
			<td colspan=5 class="contentspanheadercell">
				<h3><?php print $monthname; ?> <?php print $year; ?></h3>
			</td>
		</tr>

<?php
		if($monthlyRow1) // ############################################ SQLITE Stats Row 1 #####################################################
		{
?>

		<tr>
			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Kills</td>
					</tr>
<?php

			//Top Kills in a game
			$sql = "SELECT hero, kills, name, a.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'"; 
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY kills DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
	
			foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
			{
				$mostKillsCount = $row["kills"];
				$mostKillsUser = $row["name"];
				$mostKillsHero = $row["hero"];
				$mostKillsGame = $row["gameid"];
				$mostKillsHero = checkIfAliasSQLite($mostKillsHero, $dbType, $dbHandle);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostKillsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostKillsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostKillsGame;?>">(<?php print $mostKillsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostKillsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostKillsUser;?></a>
						</td>
					</tr>
<?php
			}
?>

				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Assists</td>
					</tr>
<?php

			//Top Assists in a game
			$sql = "SELECT hero, assists, b.name, a.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY assists DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
			foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
			{
				$mostAssistsCount = $row["assists"];
				$mostAssistsUser = $row["name"];
				$mostAssistsHero = $row["hero"];
				$mostAssistsGame = $row["gameid"];
				$mostAssistsHero = checkIfAliasSQLite($mostAssistsHero, $dbType, $dbHandle);
	?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostAssistsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostAssistsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostAssistsGame;?>">(<?php print $mostAssistsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostAssistsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostAssistsUser;?></a>
						</td>
					</tr>
<?php
			}
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Deaths</td>
					</tr>
<?php

			//Top Deaths in a game
			$sql = "SELECT hero, deaths, b.name, b.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY deaths DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
			foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
			{
				$mostDeathsCount = $row["deaths"];
				$mostDeathsUser = $row["name"];
				$mostDeathsHero = $row["hero"];
				$mostDeathsGame = $row["gameid"];
				$mostDeathsHero = checkIfAliasSQLite($mostDeathsHero, $dbType, $dbHandle);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostDeathsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostDeathsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostDeathsGame;?>">(<?php print $mostDeathsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostDeathsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostDeathsUser;?></a>
						</td>
					</tr>
<?php
			}
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Creep Kills</td>
					</tr>
<?php

			//Top CreepKills in a game
			$sql = "SELECT hero, creepkills, b.name, a.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY creepkills DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
			foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
			{
				$mostCreepKillsCount = $row["creepkills"];
				$mostCreepKillsUser = $row["name"];
				$mostCreepKillsHero = $row["hero"];
				$mostCreepKillsGame = $row["gameid"];
				$mostCreepKillsHero = checkIfAliasSQLite($mostCreepKillsHero, $dbType, $dbHandle);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostCreepKillsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostCreepKillsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostCreepKillsGame;?>">(<?php print $mostCreepKillsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostCreepKillsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostCreepKillsUser;?></a>
						</td>
					</tr>
<?php
			}
?>
				</table>
			</td>
			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Creep Denies</td>
					</tr>

<?php

			//Top CreepDenies in a game
			
			$sql = "SELECT hero, creepdenies, b.name, b.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY creepdenies DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
			foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
			{
				$mostCreepDeniesCount = $row["creepdenies"];
				$mostCreepDeniesUser = $row["name"];
				$mostCreepDeniesHero = $row["hero"];
				$mostCreepDeniesGame = $row["gameid"];
				$mostCreepDeniesHero = checkIfAliasSQLite($mostCreepDeniesHero, $dbType, $dbHandle);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostCreepDeniesHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostCreepDeniesHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostCreepDeniesGame;?>">(<?php print $mostCreepDeniesCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostCreepDeniesUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostCreepDeniesUser;?></a>
						</td>
					</tr>
<?php
			}
?>				</table>
			</td>
		</tr>		

<?php
		}
		if($monthlyRow2) // ############################################ SQLITE Stats Row 2 #####################################################
		{
?>		

		<tr>
			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Gold</td>
					</tr>
<?php

			//Top Gold in a game
			$sql = "SELECT hero, gold, name, a.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'"; 
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY gold DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
			foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
			{
				$mostGoldCount = $row["gold"];
				$mostGoldUser = $row["name"];
				$mostGoldHero = $row["hero"];
				$mostGoldGame = $row["gameid"];
				$mostGoldHero = checkIfAliasSQLite($mostGoldHero, $dbType, $dbHandle);
	?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostGoldHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostGoldHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostGoldGame;?>">(<?php print $mostGoldCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostGoldUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostGoldUser;?></a>
						</td>
					</tr>
<?php
			}
?>

				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Neutral Kills</td>
					</tr>
<?php

			//Top Neutral Kills in a game
			$sql = "SELECT hero, neutralkills, b.name, a.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY neutralkills DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
			foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
			{
				$mostNeutralKillsCount = $row["neutralkills"];
				$mostNeutralKillsUser = $row["name"];
				$mostNeutralKillsHero = $row["hero"];
				$mostNeutralKillsGame = $row["gameid"];
				$mostNeutralKillsHero = checkIfAliasSQLite($mostNeutralKillsHero, $dbType, $dbHandle);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostNeutralKillsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostNeutralKillsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostNeutralKillsGame;?>">(<?php print $mostNeutralKillsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostNeutralKillsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostNeutralKillsUser;?></a>
						</td>
					</tr>
<?php
			}
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Deaths</td>
					</tr>
<?php

			//Top Tower Kills in a game
			$sql = "SELECT hero, towerkills, b.name, b.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY towerkills DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
			foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
			{
				$mostTowerKillsCount = $row["towerkills"];
				$mostTowerKillsUser = $row["name"];
				$mostTowerKillsHero = $row["hero"];
				$mostTowerKillsGame = $row["gameid"];
				$mostTowerKillsHero = checkIfAliasSQLite($mostTowerKillsHero, $dbType, $dbHandle);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostTowerKillsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostTowerKillsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostTowerKillsGame;?>">(<?php print $mostTowerKillsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostTowerKillsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostTowerKillsUser;?></a>
						</td>
					</tr>
<?php
			}
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Neutral Kills</td>
					</tr>
<?php

			//Top Rax Kills in a game
			$sql = "SELECT hero, raxkills, b.name, a.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY raxkills DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
			foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
			{
				$mostRaxKillsCount = $row["raxkills"];
				$mostRaxKillsUser = $row["name"];
				$mostRaxKillsHero = $row["hero"];
				$mostRaxKillsGame = $row["gameid"];
				$mostRaxKillsHero = checkIfAliasSQLite($mostRaxKillsHero, $dbType, $dbHandle);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostRaxKillsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostRaxKillsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostRaxKillsGame;?>">(<?php print $mostRaxKillsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostRaxKillsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostRaxKillsUser;?></a>
						</td>
					</tr>
<?php
			}
?>
				</table>
			</td>
			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Creep Denies</td>
					</tr>

<?php

			//Top Courier Kills in a game
			
			$sql = "SELECT hero, courierkills, b.name, b.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY courierkills DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
			foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
			{
				$mostCourierKillsCount = $row["courierkills"];
				$mostCourierKillsUser = $row["name"];
				$mostCourierKillsHero = $row["hero"];
				$mostCourierKillsGame = $row["gameid"];
				$mostCourierKillsHero = checkIfAliasSQLite($mostCourierKillsHero, $dbType, $dbHandle);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostCourierKillsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostCourierKillsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostCourierKillsGame;?>">(<?php print $mostCourierKillsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostCourierKillsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostCourierKillsUser;?></a>
						</td>
					</tr>
<?php
			}
?>
				</table>
			</td>
		</tr>
<?php
		}
?>
	</table>

<?php
	}
}
else  // #################################################### MYSQL #########################################################
{
	$sql0 = "SELECT ".$sqlGroupBy1." as y, ".$sqlGroupBy2." as m, ".$sqlGroupBy3." as mn FROM games";
	if($ignorePubs)
	{
		$sql0 = $sql0." WHERE gamestate = '17'";
	}
	else if($ignorePrivs)
	{
		$sql0 = $sql0." WHERE gamestate = '16'";
	}
	$sql0 = $sql0." group by ".$sqlGroupBy1.", ".$sqlGroupBy2.", ".$sqlGroupBy3." 
		order by ".$sqlGroupBy1." desc, ".$sqlGroupBy2." desc";
	if($offset!='all')
	{
		$sql0 = $sql0." LIMIT ".$monthlyTopsResultSize*$offset.", $monthlyTopsResultSize";
	}

	$result0 = mysql_query($sql0);
	while ($row0 = mysql_fetch_array($result0, MYSQL_ASSOC)) 
	{

		$year=$row0["y"];
		$month=$row0["m"];
		$monthname=$row0["mn"];


	?>

	<table class="table" width=1000px>
		<tr>
			<td colspan=5 class="contentspanheadercell">
				<h3><?php print $monthname; ?> <?php print $year; ?></h3>
			</td>
		</tr>

<?php
		if($monthlyRow1) // ############################################ MYSQL Stats Row 1 #####################################################
		{
?>
		<tr>
			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Kills</td>
					</tr>
<?php

			//Top Kills in a game
			$sql = "SELECT hero, kills, name, a.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month"; 
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY kills DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
	
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$mostKillsCount = $row["kills"];
				$mostKillsUser = $row["name"];
				$mostKillsHero = $row["hero"];
				$mostKillsGame = $row["gameid"];
				$mostKillsHero = checkIfAliasMySQL($mostKillsHero);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostKillsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostKillsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostKillsGame;?>">(<?php print $mostKillsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostKillsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostKillsUser;?></a>
						</td>
					</tr>
<?php	
			}
			mysql_free_result($result);
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Assists</td>
					</tr>
<?php

			//Top Assists in a game
			$sql = "SELECT hero, assists, b.name, a.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY assists DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
	
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$mostAssistsCount = $row["assists"];
				$mostAssistsUser = $row["name"];
				$mostAssistsHero = $row["hero"];
				$mostAssistsGame = $row["gameid"];
				$mostAssistsHero = checkIfAliasMySQL($mostAssistsHero);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostAssistsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostAssistsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostAssistsGame;?>">(<?php print $mostAssistsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostAssistsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostAssistsUser;?></a>
						</td>
					</tr>
<?php
			}
			mysql_free_result($result);
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Deaths</td>
					</tr>
<?php

			//Top Deaths in a game
			$sql = "SELECT hero, deaths, b.name, b.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY deaths DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
	
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$mostDeathsCount = $row["deaths"];
				$mostDeathsUser = $row["name"];
				$mostDeathsHero = $row["hero"];
				$mostDeathsGame = $row["gameid"];
				$mostDeathsHero = checkIfAliasMySQL($mostDeathsHero);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostDeathsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostDeathsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostDeathsGame;?>">(<?php print $mostDeathsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostDeathsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostDeathsUser;?></a>
						</td>
					</tr>
<?php
			}
			mysql_free_result($result);
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Creep Kills</td>
					</tr>
<?php

			//Top CreepKills in a game
			$sql = "SELECT hero, creepkills, b.name, a.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY creepkills DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
	
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$mostCreepKillsCount = $row["creepkills"];
				$mostCreepKillsUser = $row["name"];
				$mostCreepKillsHero = $row["hero"];
				$mostCreepKillsGame = $row["gameid"];
				$mostCreepKillsHero = checkIfAliasMySQL($mostCreepKillsHero);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostCreepKillsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostCreepKillsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostCreepKillsGame;?>">(<?php print $mostCreepKillsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostCreepKillsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostCreepKillsUser;?></a>
						</td>
					</tr>
<?php
			}
			mysql_free_result($result);
?>
				</table>
			</td>
			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Creep Denies</td>
					</tr>

<?php

			//Top CreepDenies in a game
			$sql = "SELECT hero, creepdenies, b.name, b.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY creepdenies DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
	
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$mostCreepDeniesCount = $row["creepdenies"];
				$mostCreepDeniesUser = $row["name"];
				$mostCreepDeniesHero = $row["hero"];
				$mostCreepDeniesGame = $row["gameid"];
				$mostCreepDeniesHero = checkIfAliasMySQL($mostCreepDeniesHero);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostCreepDeniesHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostCreepDeniesHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostCreepDeniesGame;?>">(<?php print $mostCreepDeniesCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostCreepDeniesUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostCreepDeniesUser;?></a>
						</td>
					</tr>
<?php
			}
			mysql_free_result($result);
?>
				
				</table>
			</td>
		</tr>
<?php
		}
		if($monthlyRow2) // ############################################ MYSQL Stats Row 2 #####################################################
		{
?>
		<tr>
			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Gold</td>
					</tr>
<?php

			//Top Gold in a game
			$sql = "SELECT hero, gold, name, a.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month"; 
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY gold DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
	
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$mostGoldCount = $row["gold"];
				$mostGoldUser = $row["name"];
				$mostGoldHero = $row["hero"];
				$mostGoldGame = $row["gameid"];
				$mostGoldHero = checkIfAliasMySQL($mostGoldHero);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostGoldHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostGoldHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostGoldGame;?>">(<?php print $mostGoldCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostGoldUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostGoldUser;?></a>
						</td>
					</tr>
<?php	
			}
			mysql_free_result($result);
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Neutral Kills</td>
					</tr>
<?php

			//Top Neutral Kills in a game
			$sql = "SELECT hero, neutralkills, b.name, a.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY neutralkills DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
	
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$mostNeutralKillsCount = $row["neutralkills"];
				$mostNeutralKillsUser = $row["name"];
				$mostNeutralKillsHero = $row["hero"];
				$mostNeutralKillsGame = $row["gameid"];
				$mostNeutralKillsHero = checkIfAliasMySQL($mostNeutralKillsHero);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostNeutralKillsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostNeutralKillsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostNeutralKillsGame;?>">(<?php print $mostNeutralKillsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostNeutralKillsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostNeutralKillsUser;?></a>
						</td>
					</tr>
<?php
			}
			mysql_free_result($result);
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Tower Kills</td>
					</tr>
<?php

			//Top Tower Kills in a game
			$sql = "SELECT hero, towerkills, b.name, b.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY towerkills DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
	
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$mostTowerKillsCount = $row["towerkills"];
				$mostTowerKillsUser = $row["name"];
				$mostTowerKillsHero = $row["hero"];
				$mostTowerKillsGame = $row["gameid"];
				$mostTowerKillsHero = checkIfAliasMySQL($mostTowerKillsHero);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostTowerKillsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostTowerKillsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostTowerKillsGame;?>">(<?php print $mostTowerKillsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostTowerKillsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostTowerKillsUser;?></a>
						</td>
					</tr>
<?php
			}
			mysql_free_result($result);
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Rax Kills</td>
					</tr>
<?php

			//Top Rax Kills in a game
			$sql = "SELECT hero, raxkills, b.name, a.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY raxkills DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
	
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$mostRaxKillsCount = $row["raxkills"];
				$mostRaxKillsUser = $row["name"];
				$mostRaxKillsHero = $row["hero"];
				$mostRaxKillsGame = $row["gameid"];
				$mostRaxKillsHero = checkIfAliasMySQL($mostRaxKillsHero);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostRaxKillsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostRaxKillsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostRaxKillsGame;?>">(<?php print $mostRaxKillsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostRaxKillsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostRaxKillsUser;?></a>
						</td>
					</tr>
<?php
			}
			mysql_free_result($result);
?>
				</table>
			</td>
			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Courier Kills</td>
					</tr>

<?php

			//Top Courier Kills in a game
			$sql = "SELECT hero, courierkills, b.name, b.gameid FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN games as c on a.gameid = c.id where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." ORDER BY courierkills DESC, a.id ASC LIMIT ".$monthlyTopsListSize;
	
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$mostCourierKillsCount = $row["courierkills"];
				$mostCourierKillsUser = $row["name"];
				$mostCourierKillsHero = $row["hero"];
				$mostCourierKillsGame = $row["gameid"];
				$mostCourierKillsHero = checkIfAliasMySQL($mostCourierKillsHero);
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $mostCourierKillsHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostCourierKillsHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $mostCourierKillsGame;?>">(<?php print $mostCourierKillsCount;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $mostCourierKillsUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $mostCourierKillsUser;?></a>
						</td>
					</tr>
<?php
			}
			mysql_free_result($result);
?>
				</table>
			</td>
		</tr>
<?php
		}
		if($monthlyRow3) // ############################################ MYSQL Stats Row 3 #####################################################
		{
?>
		<tr>
			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Best Win Percentage</td>
					</tr>
<?php

			//Best Win Percentage
			$sql = "SELECT name as topUser, 100*wins/totgames as topValue from (Select b.name, MAX(a.id) as id,
				count(*) as totgames,
				SUM(case when(((d.winner = 1 and a.newcolour < 6) or (d.winner = 2 and a.newcolour > 6)) AND b.`left`/c.duration >= $minPlayedRatio) then 1 else 0 end) as wins 
				FROM dotaplayers AS a 
				LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
				LEFT JOIN games as c on a.gameid = c.id 
				LEFT JOIN dotagames as d on d.gameid = c.id
				where winner <> 0 AND ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." group by b.name having count(*) >= ".$montlyMinGames.") as subsel ORDER BY topValue DESC, id ASC LIMIT ".$monthlyTopsListSize;

			$rows = 0;
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$rows = $rows + 1;
				$topValue = ROUND($row["topValue"],0);
				$topUser = $row["topUser"];
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(<?php print $topValue;?> %)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
			}
			mysql_free_result($result);
			while($rows < $monthlyTopsListSize) // fill empty rows
			{ 
				$rows = $rows + 1;			
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
			}
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Best K/D ratio</td>
					</tr>
<?php

			//Best K/D ratio
			$sql = "SELECT name as topUser, totKills/totDeaths as topValue from (Select b.name, MAX(a.id) as id,
				SUM(kills) as totKills,
				SUM(deaths) as totDeaths 
				FROM dotaplayers AS a 
				LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
				LEFT JOIN games as c on a.gameid = c.id 
				LEFT JOIN dotagames as d on d.gameid = c.id
				where winner <> 0 AND ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." group by b.name having count(*) >= ".$montlyMinGames.") as subsel ORDER BY topValue DESC, id ASC LIMIT ".$monthlyTopsListSize;

			$rows = 0;
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$rows = $rows + 1;
				$topValue = ROUND($row["topValue"],2);
				$topUser = $row["topUser"];
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(<?php print $topValue;?>)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
			}
			mysql_free_result($result);
			while($rows < $monthlyTopsListSize) // fill empty rows
			{ 
				$rows = $rows + 1;			
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
			}
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Best A/D Ratio</td>
					</tr>
<?php

			//Best A/D Ratio
			$sql = "SELECT name as topUser, totAssists/totDeaths as topValue from (Select b.name, MAX(a.id) as id,
				SUM(assists) as totAssists,
				SUM(deaths) as totDeaths 
				FROM dotaplayers AS a 
				LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
				LEFT JOIN games as c on a.gameid = c.id 
				LEFT JOIN dotagames as d on d.gameid = c.id
				where winner <> 0 AND ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." group by b.name having count(*) >= ".$montlyMinGames.") as subsel ORDER BY topValue DESC, id ASC LIMIT ".$monthlyTopsListSize;
	
			$rows = 0;
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$rows = $rows + 1;
				$topValue = ROUND($row["topValue"],2);
				$topUser = $row["topUser"];
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(<?php print $topValue;?>)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
			}
			mysql_free_result($result);
			while($rows < $monthlyTopsListSize) // fill empty rows
			{ 
				$rows = $rows + 1;			
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
			}
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Most Games</td>
					</tr>
<?php

			//Most Games
			$sql = "SELECT name as topUser, totGames as topValue from (Select b.name, MAX(a.id) as id,
				COUNT(*) as totGames,
				SUM(deaths) as totDeaths 
				FROM dotaplayers AS a 
				LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
				LEFT JOIN games as c on a.gameid = c.id 
				LEFT JOIN dotagames as d on d.gameid = c.id
				where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." group by b.name having count(*) >= ".$montlyMinGames.") as subsel ORDER BY topValue DESC, id ASC LIMIT ".$monthlyTopsListSize;

			$rows = 0;
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$rows = $rows + 1;
				$topValue = ROUND($row["topValue"],2);
				$topUser = $row["topUser"];
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(<?php print $topValue;?>)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
			}
			mysql_free_result($result);
			while($rows < $monthlyTopsListSize) // fill empty rows
			{ 
				$rows = $rows + 1;			
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
			}
?>
				</table>
			</td>
			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Top Stay Percentage</td>
					</tr>

<?php

			//Top Stay Percentage
			$sql = "SELECT name as topUser, 100*playedTime/gameDuration as topValue from (Select b.name, MAX(a.id) as id,
				SUM(`left`) as playedTime,
				SUM(duration) as gameDuration 
				FROM dotaplayers AS a 
				LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
				LEFT JOIN games as c on a.gameid = c.id 
				LEFT JOIN dotagames as d on d.gameid = c.id
				where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." group by b.name having count(*) >= ".$montlyMinGames.") as subsel ORDER BY topValue DESC, id ASC LIMIT ".$monthlyTopsListSize;

			$rows = 0;
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$rows = $rows + 1;
				$topValue = ROUND($row["topValue"],1);
				$topUser = $row["topUser"];
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(<?php print $topValue;?> %)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
			}
			mysql_free_result($result);
			while($rows < $monthlyTopsListSize) // fill empty rows
			{ 
				$rows = $rows + 1;			
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
			}
?>
				</table>
			</td>
		</tr>
<?php
		}
		if($monthlyRow4) // ############################################ MYSQL Stats Row 4 #####################################################
		{
?>

		<tr>
			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Most Kills</td>
					</tr>
<?php

			//Most Kills
			$sql = "SELECT name as topUser, sumKills as topValue from (Select b.name, MAX(a.id) as id,
				SUM(kills) as sumKills 
				FROM dotaplayers AS a 
				LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
				LEFT JOIN games as c on a.gameid = c.id 
				LEFT JOIN dotagames as d on d.gameid = c.id
				where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." group by b.name having count(*) >= ".$montlyMinGames.") as subsel ORDER BY topValue DESC, id ASC LIMIT ".$monthlyTopsListSize;

			$rows = 0;
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$rows = $rows + 1;
				$topValue = $row["topValue"];
				$topUser = $row["topUser"];
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(<?php print $topValue;?>)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
			}
			mysql_free_result($result);
			while($rows < $monthlyTopsListSize) // fill empty rows
			{ 
				$rows = $rows + 1;			
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
			}
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Most Assists</td>
					</tr>
<?php

			//Most Assists
			$sql = "SELECT name as topUser, sumAssists as topValue from (Select b.name, MAX(a.id) as id,
				SUM(assists) as sumAssists 
				FROM dotaplayers AS a 
				LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
				LEFT JOIN games as c on a.gameid = c.id 
				LEFT JOIN dotagames as d on d.gameid = c.id
				where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." group by b.name having count(*) >= ".$montlyMinGames.") as subsel ORDER BY topValue DESC, id ASC LIMIT ".$monthlyTopsListSize;

			$rows = 0;
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$rows = $rows + 1;
				$topValue = $row["topValue"];
				$topUser = $row["topUser"];
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(<?php print $topValue;?>)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
			}
			mysql_free_result($result);
			while($rows < $monthlyTopsListSize) // fill empty rows
			{ 
				$rows = $rows + 1;			
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
			}
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Most Deaths</td>
					</tr>
<?php

			//Most Deaths
			$sql = "SELECT name as topUser, sumDeaths as topValue from (Select b.name, MAX(a.id) as id,
				SUM(deaths) as sumDeaths 
				FROM dotaplayers AS a 
				LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
				LEFT JOIN games as c on a.gameid = c.id 
				LEFT JOIN dotagames as d on d.gameid = c.id
				where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." group by b.name having count(*) >= ".$montlyMinGames.") as subsel ORDER BY topValue DESC, id ASC LIMIT ".$monthlyTopsListSize;

			$rows = 0;
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$rows = $rows + 1;
				$topValue = $row["topValue"];
				$topUser = $row["topUser"];
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(<?php print $topValue;?>)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
			}
			mysql_free_result($result);
			while($rows < $monthlyTopsListSize) // fill empty rows
			{ 
				$rows = $rows + 1;			
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
			}
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Most Creep Kills</td>
					</tr>
<?php

			//Most Creep Kills
			$sql = "SELECT name as topUser, sumCreepKills as topValue from (Select b.name, MAX(a.id) as id,
				SUM(creepkills) as sumCreepKills 
				FROM dotaplayers AS a 
				LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
				LEFT JOIN games as c on a.gameid = c.id 
				LEFT JOIN dotagames as d on d.gameid = c.id
				where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." group by b.name having count(*) >= ".$montlyMinGames.") as subsel ORDER BY topValue DESC, id ASC LIMIT ".$monthlyTopsListSize;

			$rows = 0;
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$rows = $rows + 1;
				$topValue = $row["topValue"];
				$topUser = $row["topUser"];
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(<?php print $topValue;?>)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
			}
			mysql_free_result($result);
			while($rows < $monthlyTopsListSize) // fill empty rows
			{ 
				$rows = $rows + 1;			
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
			}
?>
				</table>
			</td>
			<td width=20%>
				<table  width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell">Most Creep Denies</td>
					</tr>
<?php

			//Most Creep Denies
			$sql = "SELECT name as topUser, sumCreepDenies as topValue from (Select b.name, MAX(a.id) as id,
				SUM(creepdenies) as sumCreepDenies 
				FROM dotaplayers AS a 
				LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
				LEFT JOIN games as c on a.gameid = c.id 
				LEFT JOIN dotagames as d on d.gameid = c.id
				where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month";
			if($ignorePubs)
			{
				$sql = $sql." AND gamestate = '17'";
			}
			else if($ignorePrivs)
			{
				$sql = $sql." AND gamestate = '16'";
			}
			$sql = $sql." group by b.name having count(*) >= ".$montlyMinGames.") as subsel ORDER BY topValue DESC, id ASC LIMIT ".$monthlyTopsListSize;

			$rows = 0;
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$rows = $rows + 1;
				$topValue = $row["topValue"];
				$topUser = $row["topUser"];
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(<?php print $topValue;?>)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
			}
			mysql_free_result($result);
			while($rows < $monthlyTopsListSize) // fill empty rows
			{ 
				$rows = $rows + 1;			
?>
					<tr> 
						<td align=right width=15%>
							<img src="img/heroes/blank.gif" width="16" height="16">
						</td>
						<td align=center width=60px>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
			}
?>
				</table>
			</td>
		</tr>



<?php
		} // ############################################ __END__ MYSQL Stats Row 4 #####################################################

?>
	</table>
<?php
	}
	mysql_free_result($result0);
}
?>


	</div>
</div>

<div id="footer" class="footer">
		<h5>Total <?php print $interval; ?>s: <?php print $count; ?></h5>
</div>
