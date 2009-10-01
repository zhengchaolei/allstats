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
	$sortcat=sqlite_escape_string($_GET["s"]);
	$order=sqlite_escape_string($_GET["o"]);
	$offset=sqlite_escape_string($_GET["n"]);
	$interval=sqlite_escape_string($_GET["i"]);
	$username=sqlite_escape_string($_GET["u"]);

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
	$sortcat=mysql_real_escape_string($_GET["s"]);
	$order=mysql_real_escape_string($_GET["o"]);
	$offset=mysql_real_escape_string($_GET["n"]);
	$interval=mysql_real_escape_string($_GET["i"]);
	$username=mysql_real_escape_string($_GET["u"]);

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

$sql = "select count(*) as count from( select name from gameplayers as gp, dotagames as dg, games as ga,dotaplayers as dp where dg.winner <> 0 and dp.gameid = gp.gameid 
and dg.gameid = dp.gameid and dp.gameid = ga.id and gp.gameid = dg.gameid and gp.colour = dp.colour and gp.name = '$username'";

if($ignorePubs)
{
$sql = $sql." and gamestate = '17'";
}
else if($ignorePrivs)
{
$sql = $sql." and gamestate = '16'";
}

$sql = $sql." group by ".$sqlGroupBy1.", ".$sqlGroupBy2." having count(*) >= $historyMinGames) as h";

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

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$count=$row["count"];
		}
	mysql_free_result($result);
}

	$pages = ceil($count/$userHistoryResultSize);

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
							print "<a href=\"?p=userhistory&s=".$sortcat."&o=".$order."&i=".$interval."&u=".$username."&n=all\">Show All ".$interval."s";
						}
						print "<br/><br/>";
						if($interval == 'Week')
						{
							print "<a href=\"?p=userhistory&s=".$sortcat."&o=".$order."&u=".$username."&n=".$offset."&i=month\">Toggle Montly/Weekly View</a>";
						}
						else
						{
							print "<a href=\"?p=userhistory&s=".$sortcat."&o=".$order."&u=".$username."&n=".$offset."&i=week\">Toggle Montly/Weekly View</a>";
						}						
						?>
						</td>
					</tr>
					</h4>
				</table>
			</td>
			<td width=50%>
				<h2>User Stats History for: <a href="?p=user&u=<?php print $username; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $username; ?></a></h2>
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
						$min = $offset*$userHistoryResultSize+1;
						$max = $offset*$userHistoryResultSize+$userHistoryResultSize;
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
						print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($counter-1)."\">".$counter."</a></td>";
						}
					}
					print "<td width=35px><span style=\"color:#ddd;\">></span></td>";
				}
				else
				{
					if($offset > 0)
					{
						print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($offset-1)."\"><</a>";
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
									print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
						}
						if($offset == 1)
						{
							print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=0\">1</a></td>";
							print "<td width=35px><span style=\"color:#ddd;\">2</span></td>";
							for($counter = 3; $counter < 6; $counter++)
							{
								if($counter-1 < $pages)
								{
								print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($counter-1)."\">".$counter."</a></td>";
								
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
								print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($counter-1)."\">".$counter."</a></td>";
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
									print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
							print "<td width=35px><span style=\"color:#ddd;\">".($offset+1)."</span>";
							print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($offset+1)."\">".($offset+2)."</a></td>";
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
								print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($counter-1)."\">".$counter."</a></td>";
							}
						}
					}
					if(($offset+1)*$userHistoryResultSize < $count)
					{
						print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($offset+1)."\">></a></td>";
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
		<table class="tableheader" id="tableheader">
			<tr class="headercell">
<?php
 if($offset == 'all')
 {
	//User Name
	if($sortcat == "datetime")
	{
		if($order == "asc")
		{
			print("<td width=175px><a href=\"?p=top&s=datetime&o=desc&g=".$games."&n=all\">".$interval."</a></td>");
		}
		else
		{
			print("<td width=175px><a href=\"?p=top&s=datetime&o=asc&g=".$games."&n=all\">".$interval."</a></td>");
		}
	}
	else
	{
		print("<td width=175px><a href=\"?p=top&s=datetime&o=asc&g=".$games."&n=all\">".$interval."</a></td>");
	}
	//Games
	if($sortcat == "totgames")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=totgames&o=desc&g=".$games."&n=all\">Games</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=totgames&o=asc&g=".$games."&n=all\">Games</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=totgames&o=desc&g=".$games."&n=all\">Games</a></td>");
	}
	//Wins
	if($sortcat == "wins")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=wins&o=desc&g=".$games."&n=all\">Wins</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=wins&o=asc&g=".$games."&n=all\">Wins</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=wins&o=desc&g=".$games."&n=all\">Wins</a></td>");
	}
	//Losses
	if($sortcat == "losses")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=losses&o=desc&g=".$games."&n=all\">Losses</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=losses&o=asc&g=".$games."&n=all\">Losses</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=losses&o=desc&g=".$games."&n=all\">Losses</a></td>");
	}
	//WinPercent
	if($sortcat == "winpercent")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=winpercent&o=desc&g=".$games."&n=all\">WinPercent</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=winpercent&o=asc&g=".$games."&n=all\">WinPercent</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=winpercent&o=desc&g=".$games."&n=all\">WinPercent</a></td>");
	}	//Kills
	if($sortcat == "kills")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=kills&o=desc&g=".$games."&n=all\">Kills</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=kills&o=asc&g=".$games."&n=all\">Kills</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=kills&o=desc&g=".$games."&n=all\">Kills</a></td>");
	}
	//CreepKills
	if($sortcat == "deaths")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=deaths&o=desc&g=".$games."&n=all\">Deaths</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=deaths&o=asc&g=".$games."&n=all\">Deaths</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=deaths&o=desc&g=".$games."&n=all\">Deaths</a></td>");
	}
	//Assists
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=assists&o=desc&g=".$games."&n=all\">Assists</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=assists&o=asc&g=".$games."&n=all\">Assists</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=assists&o=desc&g=".$games."&n=all\">Assists</a></td>");
	}
	//KDRatio
	if($sortcat == "killdeathratio")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=killdeathratio&o=desc&g=".$games."&n=all\">Kills/<br>Death</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=killdeathratio&o=asc&g=".$games."&n=all\">Kills/<br>Death</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=killdeathratio&o=desc&g=".$games."&n=all\">Kills/<br>Death</a></td>");
	}
	//Creep Kills
	if($sortcat == "creepkills")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=creepkills&o=desc&g=".$games."&n=all\">Creep<br>Kills</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=creepkills&o=asc&g=".$games."&n=all\">Creep<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=creepkills&o=desc&g=".$games."&n=all\">Creep<br>Kills</a></td>");
	}
	//Denies
	if($sortcat == "creepdenies")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=creepdenies&o=desc&g=".$games."&n=all\">Creep<br>Denies</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=creepdenies&o=asc&g=".$games."&n=all\">Creep<br>Denies</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=creepdenies&o=desc&g=".$games."&n=all\">Creep<br>Denies</a></td>");
	}
	//Neutral Kills
	if($sortcat == "neutralkills")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=neutralkills&o=desc&g=".$games."&n=all\">Neutral<br>Kills</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=neutralkills&o=asc&g=".$games."&n=all\">Neutral<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=neutralkills&o=desc&g=".$games."&n=all\">Neutral<br>Kills</a></td>");
	}
}
else
{
//User Name
	if($sortcat == "datetime")
	{
		if($order == "asc")
		{
			print("<td width=175px><a href=\"?p=top&s=datetime&o=desc&g=".$games."&n=0\">".$interval."</a></td>");
		}
		else
		{
			print("<td width=175px><a href=\"?p=top&s=datetime&o=asc&g=".$games."&n=0\">".$interval."</a></td>");
		}
	}
	else
	{
		print("<td width=175px><a href=\"?p=top&s=datetime&o=asc&g=".$games."&n=0\">".$interval."</a></td>");
	}
	//Games
	if($sortcat == "totgames")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=totgames&o=desc&g=".$games."&n=0\">Games</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=totgames&o=asc&g=".$games."&n=0\">Games</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=totgames&o=desc&g=".$games."&n=0\">Games</a></td>");
	}
	//Wins
	if($sortcat == "wins")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=wins&o=desc&g=".$games."&n=0\">Wins</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=wins&o=asc&g=".$games."&n=0\">Wins</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=wins&o=desc&g=".$games."&n=0\">Wins</a></td>");
	}
	//Losses
	if($sortcat == "losses")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=losses&o=desc&g=".$games."&n=0\">Losses</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=losses&o=asc&g=".$games."&n=0\">Losses</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=losses&o=desc&g=".$games."&n=0\">Losses</a></td>");
	}
	//WinPercent
	if($sortcat == "winpercent")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=winpercent&o=desc&g=".$games."&n=0\">WinPercent</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=winpercent&o=asc&g=".$games."&n=0\">WinPercent</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=winpercent&o=desc&g=".$games."&n=0\">WinPercent</a></td>");
	}
	//Kills
	if($sortcat == "kills")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=kills&o=desc&g=".$games."&n=0\">Kills</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=kills&o=asc&g=".$games."&n=0\">Kills</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=kills&o=desc&g=".$games."&n=0\">Kills</a></td>");
	}
	//CreepKills
	if($sortcat == "deaths")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=deaths&o=desc&g=".$games."&n=0\">Deaths</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=deaths&o=asc&g=".$games."&n=0\">Deaths</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=deaths&o=desc&g=".$games."&n=0\">Deaths</a></td>");
	}
	//Assists
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=assists&o=desc&g=".$games."&n=0\">Assists</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=assists&o=asc&g=".$games."&n=0\">Assists</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=assists&o=desc&g=".$games."&n=0\">Assists</a></td>");
	}
	//KDRatio
	if($sortcat == "killdeathratio")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=killdeathratio&o=desc&g=".$games."&n=0\">Kills/<br>Death</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=killdeathratio&o=asc&g=".$games."&n=0\">Kills/<br>Death</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=killdeathratio&o=desc&g=".$games."&n=0\">Kills/<br>Death</a></td>");
	}
	//Creep Kills
	if($sortcat == "creepkills")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=creepkills&o=desc&g=".$games."&n=0\">Creep<br>Kills</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=creepkills&o=asc&g=".$games."&n=0\">Creep<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=creepkills&o=desc&g=".$games."&n=0\">Creep<br>Kills</a></td>");
	}
	//Denies
	if($sortcat == "creepdenies")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=creepdenies&o=desc&g=".$games."&n=0\">Creep<br>Denies</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=creepdenies&o=asc&g=".$games."&n=0\">Creep<br>Denies</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=creepdenies&o=desc&g=".$games."&n=0\">Creep<br>Denies</a></td>");
	}
	//Neutral Kills
	if($sortcat == "neutralkills")
	{
		if($order == "asc")
		{
			print("<td width=75px><a href=\"?p=top&s=neutralkills&o=desc&g=".$games."&n=0\">Neutral<br>Kills</a></td>");
		}
		else
		{
			print("<td width=75px><a href=\"?p=top&s=neutralkills&o=asc&g=".$games."&n=0\">Neutral<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=75px><a href=\"?p=top&s=neutralkills&o=desc&g=".$games."&n=0\">Neutral<br>Kills</a></td>");
	}
}
?>
				<td width=16px></td>
			</tr>
		</table>
	</div>
	<div id="datawrapper">
		<table class="table" id="data">
<?php

$sql = "select *,(kills/deaths) as killdeathratio, (wins+0.000001)/(losses+0.000001) as winpercent from (
select ".$sqlGroupBy1." as y, ".$sqlGroupBy2." as m, ".$sqlGroupBy3." as mn, MIN(datetime) as datetime, avg(dp.courierkills) as courierkills, avg(dp.raxkills) as raxkills,
avg(dp.towerkills) as towerkills, avg(dp.assists) as assists, avg(dp.creepdenies) as creepdenies, avg(dp.creepkills) as creepkills,
avg(dp.neutralkills) as neutralkills, avg(dp.deaths) as deaths, avg(dp.kills) as kills,
count(*) as totgames, SUM(case when(((dg.winner = 1 and dp.newcolour < 6) or (dg.winner = 2 and dp.newcolour > 6)) AND gp.`left`/ga.duration >= $minPlayedRatio) then 1 else 0 end) as wins, SUM(case when(((dg.winner = 2 and dp.newcolour < 6) or (dg.winner = 1 and dp.newcolour > 6)) AND gp.`left`/ga.duration >= $minPlayedRatio) then 1 else 0 end) as losses
from gameplayers as gp LEFT JOIN dotagames as dg ON gp.gameid = dg.gameid LEFT JOIN dotaplayers as dp ON dg.gameid = dp.gameid and gp.colour = dp.colour and dp.newcolour <> 12 and dp.newcolour <> 6
LEFT JOIN games as ga ON dp.gameid = ga.id 
where dg.winner <> 0 and gp.name = '$username'";

if($ignorePubs)
{
$sql = $sql." and gamestate = '17'";
}
else if($ignorePrivs)
{
$sql = $sql." and gamestate = '16'";
}

$sql = $sql." group by ".$sqlGroupBy1.", ".$sqlGroupBy2." having totgames >= $historyMinGames) as h ORDER BY $sortcat $order";


if($offset!='all')
{
$sql = $sql." LIMIT ".$userHistoryResultSize*$offset.", $userHistoryResultSize";
}

if($dbType == 'sqlite')
{
	$rank = 1;
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$totgames=$row["totgames"];
		$kills=$row["kills"];
		$death=$row["deaths"];
		$assists=$row["assists"];
		$creepkills=$row["creepkills"];
		$creepdenies=$row["creepdenies"];
		$neutralkills=$row["neutralkills"];
		$courierkills=$row["courierkills"];
		$wins=$row["wins"];
		$losses=$row["losses"];
		if($wins == 0)
		{ 
			$winpercent = 0;
		} 
		else 
		{
			$winpercent = round($wins/($wins+$losses), 4)*100;
		}
		$killdeathratio=ROUND($row["killdeathratio"],2);

	?>
	<tr class="row">
	<td width=25px><?php print $rank; ?></td>
	<td width=175px><a <?php if($banname<>'') { print 'style="color:#e56879"'; } ?> href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $name; ?></a></td>
	<td width=100px><?php print ROUND($totalscore,2); ?></td>
	<td width=70px><?php print $totgames;?></td>
	<td width=70px><?php print $wins; ?></td>
	<td width=70px><?php print $losses; ?></td>
	<td width=70px><?php print ROUND($kills,1); ?></td>
	<td width=70px><?php print ROUND($death,1); ?></td>
	<td width=70px><?php print ROUND($assists,1); ?></td>
	<td width=70px><?php print $killdeathratio; ?></td>

	<td width=70px><?php print ROUND($creepkills,1) ?></td>
	<td width=70px><?php print ROUND($creepdenies,1); ?></td>
	<td width=70px><?php print ROUND($neutralkills,1); ?></td>


	</tr>

	<?php
	$rank = $rank + 1;
	}
}
else
{
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

		$year=$row["y"];
		$month=$row["m"];
		$monthname=$row["mn"];
		$totgames=$row["totgames"];
		$kills=$row["kills"];
		$death=$row["deaths"];
		$assists=$row["assists"];
		$creepkills=$row["creepkills"];
		$creepdenies=$row["creepdenies"];
		$neutralkills=$row["neutralkills"];
		$courierkills=$row["courierkills"];
		$wins=$row["wins"];
		$losses=$row["losses"];
		if($wins == 0)
		{ 
			$winpercent = 0;
		} 
		else 
		{
			$winpercent = round($wins/($wins+$losses), 4)*100;
		}
		$killdeathratio=ROUND($row["killdeathratio"],2);

	?>
	<tr class="row">
    <td width=175px><?php print $monthname; ?> <?php print $year; ?></td>
	<td width=75px><?php print $totgames;?></td>
	<td width=75px><?php print $wins; ?></td>
	<td width=75px><?php print $losses; ?></td>
	<td width=75px><?php print $winpercent; ?> %</td>
	<td width=75px><?php print ROUND($kills,1); ?></td>
	<td width=75px><?php print ROUND($death,1); ?></td>
	<td width=75px><?php print ROUND($assists,1); ?></td>
	<td width=75px><?php print $killdeathratio; ?></td>

	<td width=75px><?php print ROUND($creepkills,1) ?></td>
	<td width=75px><?php print ROUND($creepdenies,1); ?></td>
	<td width=75px><?php print ROUND($neutralkills,1); ?></td>


	</tr>

	<?php
		$rank = $rank + 1;
	}
	mysql_free_result($result);
}
?>
</table>
</div>
</div>
<div id="footerdata" class="footerdata">
	<table class="table" width=1016px>
		<tr>
			<td colspan=5>
				<h3>All Time Top Statistics per Game for <?php print $username; ?></h3>
			</td>
		</tr>
		<tr>


<?php
if($dbType == 'sqlite') // #################################################### SQLITE #########################################################
{
	$arrStatRow = array(
		"Top Kills" => "SELECT hero as topHero, kills as topValue, name as topUser, a.gameid as topGame
			FROM dotaplayers AS a 
			LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
			LEFT JOIN games as c on a.gameid = c.id",
		"Top Assists" => "SELECT hero as topHero, assists as topValue, name as topUser, a.gameid as topGame
			FROM dotaplayers AS a 
			LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
			LEFT JOIN games as c on a.gameid = c.id",
		"Top Deaths" => "SELECT hero as topHero, deaths as topValue, name as topUser, a.gameid as topGame
			FROM dotaplayers AS a 
			LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
			LEFT JOIN games as c on a.gameid = c.id",
		"Top Creep Kills" => "SELECT hero as topHero, creepkills as topValue, name as topUser, a.gameid as topGame
			FROM dotaplayers AS a 
			LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
			LEFT JOIN games as c on a.gameid = c.id",
		"Top Creep Denies" => "SELECT hero as topHero, creepdenies as topValue, name as topUser, a.gameid as topGame
			FROM dotaplayers AS a 
			LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
			LEFT JOIN games as c on a.gameid = c.id");
		
	foreach($arrStatRow as $title => $sql)
	{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="footerheadercell"><?php print $title; ?></td>
					</tr>

<?php

		if($ignorePubs)
		{
			$sql = $sql." WHERE gamestate = '17'";
		}
		else if($ignorePrivs)
		{
			$sql = $sql." WHERE gamestate = '16'";
		}
		$sql = $sql." ORDER BY topValue DESC, a.id ASC LIMIT ".$monthlyTopsListSize;

		foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
		{
			$topUser = $row["topUser"];
			$topHero = $row["topHero"];
			$topGame = $row["topGame"];
			$topHero = checkIfAliasSQLite($topHero, $dbType, $dbHandle);
			if(isset($row["topValueUnit"]))
			{
				$topValueUnit = $row["topValueUnit"];
			}
			else
			{
				$topValueUnit = '';
			}
			if ($topValueUnit <> '') {
				$topValue = ROUND($row["topValue"],1);
			}
			else
			{
				$topValue = ROUND($row["topValue"],2);				
			}
					
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $topHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $topHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $topGame;?>">(<?php print $topValue;?>)</a> 
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php
		}
?>
				</table>
			</td>
			
<?php
	}
}
else  // #################################################### MYSQL #########################################################
{

	$arrStatRow = array(
		"Top Kills" => "SELECT hero as topHero, kills as topValue, datetime as topDate, a.gameid as topGame
			FROM dotaplayers AS a 
			LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
			LEFT JOIN games as c on a.gameid = c.id",
		"Top Assists" => "SELECT hero as topHero, assists as topValue, datetime as topDate, a.gameid as topGame
			FROM dotaplayers AS a 
			LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
			LEFT JOIN games as c on a.gameid = c.id",
		"Top Deaths" => "SELECT hero as topHero, deaths as topValue, datetime as topDate, a.gameid as topGame
			FROM dotaplayers AS a 
			LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
			LEFT JOIN games as c on a.gameid = c.id",
			"Top Creep Kills" => "SELECT hero as topHero, creepkills as topValue, datetime as topDate, a.gameid as topGame
			FROM dotaplayers AS a 
			LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
			LEFT JOIN games as c on a.gameid = c.id",
		"Top Creep Denies" => "SELECT hero as topHero, creepdenies as topValue, datetime as topDate, a.gameid as topGame
			FROM dotaplayers AS a 
			LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
			LEFT JOIN games as c on a.gameid = c.id");
		
	foreach($arrStatRow as $title => $sql)
	{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="footerheadercell"><?php print $title; ?></td>
					</tr>

<?php
		$sql = $sql." WHERE name = '$username'";
		if($ignorePubs)
		{
			$sql = $sql." AND gamestate = '17'";
		}
		else if($ignorePrivs)
		{
			$sql = $sql." AND gamestate = '16'";
		}
		$sql = $sql." ORDER BY topValue DESC, a.id ASC LIMIT ".$monthlyTopsListSize;

		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
		{
			$topDate = substr($row["topDate"],0,10);
			$topHero = $row["topHero"];
			$topGame = $row["topGame"];
			$topHero = checkIfAliasMySQL($topHero);
			if(isset($row["topValueUnit"]))
			{
				$topValueUnit = $row["topValueUnit"];
			}
			else
			{
				$topValueUnit = '';
			}
			if ($topValueUnit <> '') {
				$topValue = ROUND($row["topValue"],1);
			}
			else
			{
				$topValue = ROUND($row["topValue"],2);				
			}				
?>
					<tr> 
						<td align=right width=15%>
							<a  href="?p=hero&hid=<?php print $topHero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $topHero; ?>.gif" width="16" height="16"></a>
						</td>
						<td align=center width=60px>
							<a href="?p=gameinfo&gid=<?php print $topGame;?>">(<?php print $topValue;?>)</a> 
						</td>
						<td align=left>
							<?php print $topDate;?>
						</td>
					</tr>
<?php
		}
		mysql_free_result($result);
?>

				</table>
			</td>
			
<?php
	}
}
?>			

		</tr>
	</table>
</div>
<div id="footer" class="footer">
		<h5>Total Players: <?php print $count; ?></h5>
</div>
