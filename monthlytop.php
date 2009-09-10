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
		
<?php
			$arrStatRow = array(
				"Top Kills" => "SELECT hero as topHero, kills as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",
				"Top Assists" => "SELECT hero as topHero, assists as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",
				"Top Deaths" => "SELECT hero as topHero, deaths as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",		
				"Top Creep Kills" => "SELECT hero as topHero, creepkills as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",		
				"Top Creep Denies" => "SELECT hero as topHero, creepdenies as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'");
				
			foreach($arrStatRow as $title => $sql)
			{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell"><?php print $title; ?></td>
					</tr>

<?php

				if($ignorePubs)
				{
					$sql = $sql." AND gamestate = '17'";
				}
				else if($ignorePrivs)
				{
					$sql = $sql." AND gamestate = '16'";
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

					<tr>
						<td colspan=3 class="contentemptyspacer"></td>
					<tr>
				</table>
			</td>
			
<?php
			}
?>			
			
		</tr>			
	
<?php
		}
		if($monthlyRow2) // ############################################ SQLITE Stats Row 2 #####################################################
		{
?>		


		<tr>
		
<?php
			$arrStatRow = array(
				"Top Gold" => "SELECT hero as topHero, gold as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",
				"Top Neutral Kills" => "SELECT hero as topHero, neutralkills as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",
				"Top Tower Kills" => "SELECT hero as topHero, towerkills as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",		
				"Top Rax Kills" => "SELECT hero as topHero, raxkills as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",		
				"Top Courier Kills" => "SELECT hero as topHero, courierkills as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'");
				
			foreach($arrStatRow as $title => $sql)
			{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell"><?php print $title; ?></td>
					</tr>

<?php

				if($ignorePubs)
				{
					$sql = $sql." AND gamestate = '17'";
				}
				else if($ignorePrivs)
				{
					$sql = $sql." AND gamestate = '16'";
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

					<tr>
						<td colspan=3 class="contentemptyspacer"></td>
					<tr>
				</table>
			</td>
			
<?php
			}
?>			
			
		</tr>	
		
<?php
		}
		if($monthlyRow3) // ############################################ SQLITE Stats Row 3 #####################################################
		{
?>		

<?php
			$arrStatRow = array(
				"Best Win Percentage" => "SELECT name as topUser, 100*wins*1.0/totgames*1.0 as topValue, ' %' as topValueUnit from (Select b.name as name, MAX(a.id) as id,
					count(*) as totgames,
					SUM(case when(((d.winner = 1 and a.newcolour < 6) or (d.winner = 2 and a.newcolour > 6)) AND b.`left`*1.0/c.duration*1.0 >= $minPlayedRatio) then 1 else 0 end) as wins 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",
				"Best K/D ratio" => "SELECT name as topUser, totKills*1.0/totDeaths*1.0 as topValue from (Select b.name as name, MAX(a.id) as id,
					SUM(kills) as totKills,
					SUM(deaths) as totDeaths 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",
				"Best A/D Ratio" => "SELECT name as topUser, totAssists*1.0/totDeaths*1.0 as topValue from (Select b.name as name, MAX(a.id) as id,
					SUM(assists) as totAssists,
					SUM(deaths) as totDeaths 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",
				"Most Games" => "SELECT name as topUser, totGames as topValue from (Select b.name as name, MAX(a.id) as id,
					COUNT(*) as totGames,
					SUM(deaths) as totDeaths 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",
				"Top Stay Percentage" => "SELECT name as topUser, 100*playedTime*1.0/gameDuration*1.0 as topValue, ' %' as topValueUnit from (Select b.name as name, MAX(a.id) as id,
					SUM(`left`) as playedTime,
					SUM(duration) as gameDuration 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'");
				
			foreach($arrStatRow as $title => $sql)
			{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell"><?php print $title; ?></td>
					</tr>
<?php

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
				foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
				{
					$rows = $rows + 1;
					$topUser = $row["topUser"];
					$topValueUnit = $row["topValueUnit"];
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
						<td align=center width=80px height=22px colspan=2>
							(<?php print $topValue.$topValueUnit;?>)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
				}
				while($rows < $monthlyTopsListSize) // fill empty rows
				{ 
					$rows = $rows + 1;			
?>
					<tr> 
						<td align=center width=80px height=22px colspan=2>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
				}
?>
					<tr>
						<td colspan=3 class="contentemptyspacer"></td>
					<tr>
				</table>
			</td>
			
<?php
			}
?>
		</tr>
		
<?php
		}
		if($monthlyRow4) // ############################################ SQLITE Stats Row 4 #####################################################
		{
?>		
		
		<tr>
		
<?php
			$arrStatRow = array(
				"Most Kills" => "SELECT name as topUser, sumKills as topValue from (Select b.name as name, MAX(a.id) as id,
					SUM(kills) as sumKills 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",
				"Most Assists" => "SELECT name as topUser, sumAssists as topValue from (Select b.name as name, MAX(a.id) as id,
					SUM(assists) as sumAssists 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",			
				"Most Deaths" => "SELECT name as topUser, sumDeaths as topValue from (Select b.name as name, MAX(a.id) as id,
					SUM(deaths) as sumDeaths 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",			
				"Most Creep Kills" => "SELECT name as topUser, sumCreepKills as topValue from (Select b.name as name, MAX(a.id) as id,
					SUM(creepkills) as sumCreepKills 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",			
				"Most Creep Denies" => "SELECT name as topUser, sumCreepDenies as topValue from (Select b.name as name, MAX(a.id) as id,
					SUM(creepdenies) as sumCreepDenies 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'");
				
			foreach($arrStatRow as $title => $sql)
			{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell"><?php print $title; ?></td>
					</tr>
<?php

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
				foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
				{
					$rows = $rows + 1;
					$topUser = $row["topUser"];
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
						<td align=center width=80px height=22px colspan=2>
							(<?php print $topValue;?>)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
				}
				while($rows < $monthlyTopsListSize) // fill empty rows
				{ 
					$rows = $rows + 1;			
?>
					<tr> 
						<td align=center width=80px height=22px colspan=2>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
				}
?>
					<tr>
						<td colspan=3 class="contentemptyspacer"></td>
					<tr>
				</table>
			</td>
			
<?php
			}
?>
		</tr>
		
		
<?php
		}
		if($monthlyRow5) // ############################################ SQLITE Stats Row 5 #####################################################
		{
?>		

		<tr>
		
<?php
			$arrStatRow = array(
				"AVG Kills" => "SELECT name as topUser, sumKills*1.0/totGames*1.0 as topValue from (Select b.name as name, MAX(a.id) as id,
					COUNT(*) as totGames,
					SUM(kills) as sumKills 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",
				"AVG Assists" => "SELECT name as topUser, sumAssists*1.0/totGames*1.0 as topValue from (Select b.name as name, MAX(a.id) as id,
					COUNT(*) as totGames,
					SUM(assists) as sumAssists 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",			
				"AVG Deaths" => "SELECT name as topUser, sumDeaths*1.0/totGames*1.0 as topValue from (Select b.name as name, MAX(a.id) as id,
					COUNT(*) as totGames,
					SUM(deaths) as sumDeaths 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",			
				"AVG Creep Kills" => "SELECT name as topUser, sumCreepKills*1.0/totGames*1.0 as topValue from (Select b.name as name, MAX(a.id) as id,
					COUNT(*) as totGames,
					SUM(creepkills) as sumCreepKills 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'",			
				"AVG Creep Denies" => "SELECT name as topUser, sumCreepDenies*1.0/totGames*1.0 as topValue from (Select b.name as name, MAX(a.id) as id,
					COUNT(*) as totGames,
					SUM(creepdenies) as sumCreepDenies 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = '$year' AND ".$sqlGroupBy2." = '$month'");
				
			foreach($arrStatRow as $title => $sql)
			{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell"><?php print $title; ?></td>
					</tr>
<?php

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
				foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
				{
					$rows = $rows + 1;
					$topUser = $row["topUser"];
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
						<td align=center width=80px height=22px colspan=2>
							(<?php print $topValue;?>)
						</td>
						<td align=left>
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php	
				}
				while($rows < $monthlyTopsListSize) // fill empty rows
				{ 
					$rows = $rows + 1;			
?>
					<tr> 
						<td align=center width=80px height=22px colspan=2>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
				}
?>
					<tr>
						<td colspan=3 class="contentemptyspacer"></td>
					<tr>
				</table>
			</td>
			
<?php
			}
?>
		</tr>		
		
		
<?php
		} // ############################################ __END__ SQLITE Stats Row 5 #####################################################
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
		
<?php
			$arrStatRow = array(
				"Top Kills" => "SELECT hero as topHero, kills as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",
				"Top Assists" => "SELECT hero as topHero, assists as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",
				"Top Deaths" => "SELECT hero as topHero, deaths as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",
				"Top Creep Kills" => "SELECT hero as topHero, creepkills as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",
				"Top Creep Denies" => "SELECT hero as topHero, creepdenies as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month");
				
			foreach($arrStatRow as $title => $sql)
			{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell"><?php print $title; ?></td>
					</tr>

<?php
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
					$topUser = $row["topUser"];
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
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php
				}
				mysql_free_result($result);
?>

					<tr>
						<td colspan=3 class="contentemptyspacer"></td>
					<tr>
				</table>
			</td>
			
<?php
			}
?>			
			
		</tr>	

<?php
		}
		if($monthlyRow2) // ############################################ MYSQL Stats Row 2 #####################################################
		{
?>
		<tr>
		
<?php
			$arrStatRow = array(
				"Top Gold" => "SELECT hero as topHero, gold as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",
				"Top Neutral Kills" => "SELECT hero as topHero, neutralkills as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",
				"Top Tower Kills" => "SELECT hero as topHero, towerkills as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",
				"Top Rax Kills" => "SELECT hero as topHero, raxkills as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",
				"Top Courier Kills" => "SELECT hero as topHero, courierkills as topValue, name as topUser, a.gameid as topGame
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month");
				
			foreach($arrStatRow as $title => $sql)
			{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell"><?php print $title; ?></td>
					</tr>

<?php
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
					$topUser = $row["topUser"];
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
							<a href="?p=user&u=<?php print $topUser;?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $topUser;?></a>
						</td>
					</tr>
<?php
				}
				mysql_free_result($result);
?>

					<tr>
						<td colspan=3 class="contentemptyspacer"></td>
					<tr>
				</table>
			</td>
			
<?php
			}
?>			
			
		</tr>	
		
<?php
		}
		if($monthlyRow3) // ############################################ MYSQL Stats Row 3 #####################################################
		{
?>

		<tr>
		
<?php
			$arrStatRow = array(
				"Best Win Percentage" => "SELECT name as topUser, 100*wins/totgames as topValue ,' %' as topValueUnit from (Select b.name, MAX(a.id) as id,
					count(*) as totgames,
					SUM(case when(((d.winner = 1 and a.newcolour < 6) or (d.winner = 2 and a.newcolour > 6)) AND b.`left`/c.duration >= $minPlayedRatio) then 1 else 0 end) as wins 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",
				"Best K/D ratio" => "SELECT name as topUser, totKills/totDeaths as topValue from (Select b.name, MAX(a.id) as id,
					SUM(kills) as totKills,
					SUM(deaths) as totDeaths 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",			
				"Best A/D Ratio" => "SELECT name as topUser, totAssists/totDeaths as topValue from (Select b.name, MAX(a.id) as id,
					SUM(assists) as totAssists,
					SUM(deaths) as totDeaths 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",			
				"Most Games" => "SELECT name as topUser, totGames as topValue from (Select b.name, MAX(a.id) as id,
					COUNT(*) as totGames,
					SUM(deaths) as totDeaths 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",			
				"Top Stay Percentage" => "SELECT name as topUser, 100*playedTime/gameDuration as topValue, ' %' as topValueUnit from (Select b.name, MAX(a.id) as id,
					SUM(`left`) as playedTime,
					SUM(duration) as gameDuration 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month");
				
			foreach($arrStatRow as $title => $sql)
			{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell"><?php print $title; ?></td>
					</tr>
<?php

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
					$topUser = $row["topUser"];
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
						<td align=center width=80px height=22px colspan=2>
							(<?php print $topValue.$topValueUnit;?>)
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
						<td align=center width=80px height=22px colspan=2>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
				}
?>
					<tr>
						<td colspan=3 class="contentemptyspacer"></td>
					<tr>
				</table>
			</td>
			
<?php
			}
?>
		</tr>

<?php
		}
		if($monthlyRow4) // ############################################ MYSQL Stats Row 4 #####################################################
		{
?>

		<tr>
		
<?php
			$arrStatRow = array(
				"Most Kills" => "SELECT name as topUser, sumKills as topValue from (Select b.name, MAX(a.id) as id,
					SUM(kills) as sumKills 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",
				"Most Assists" => "SELECT name as topUser, sumAssists as topValue from (Select b.name, MAX(a.id) as id,
					SUM(assists) as sumAssists 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",			
				"Most Deaths" => "SELECT name as topUser, sumDeaths as topValue from (Select b.name, MAX(a.id) as id,
					SUM(deaths) as sumDeaths 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",			
				"Most Creep Kills" => "SELECT name as topUser, sumCreepKills as topValue from (Select b.name, MAX(a.id) as id,
					SUM(creepkills) as sumCreepKills 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",			
				"Most Creep Denies" => "SELECT name as topUser, sumCreepDenies as topValue from (Select b.name, MAX(a.id) as id,
					SUM(creepdenies) as sumCreepDenies 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month");
				
			foreach($arrStatRow as $title => $sql)
			{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell"><?php print $title; ?></td>
					</tr>
<?php

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
					$topUser = $row["topUser"];
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
						<td align=center width=80px height=22px colspan=2>
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
						<td align=center width=80px height=22px colspan=2>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
				}
?>
					<tr>
						<td colspan=3 class="contentemptyspacer"></td>
					<tr>
				</table>
			</td>
			
<?php
			}
?>
		</tr>

<?php
		}
		if($monthlyRow5) // ############################################ MYSQL Stats Row 5 #####################################################
		{
?>

		<tr>
		
<?php
			$arrStatRow = array(
				"AVG Kills" => "SELECT name as topUser, sumKills/totGames as topValue from (Select b.name, MAX(a.id) as id,
					COUNT(*) as totGames,
					SUM(kills) as sumKills 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",
				"AVG Assists" => "SELECT name as topUser, sumAssists/totGames as topValue from (Select b.name, MAX(a.id) as id,
					COUNT(*) as totGames,
					SUM(assists) as sumAssists 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",			
				"AVG Deaths" => "SELECT name as topUser, sumDeaths/totGames as topValue from (Select b.name, MAX(a.id) as id,
					COUNT(*) as totGames,
					SUM(deaths) as sumDeaths 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",			
				"AVG Creep Kills" => "SELECT name as topUser, sumCreepKills/totGames as topValue from (Select b.name, MAX(a.id) as id,
					COUNT(*) as totGames,
					SUM(creepkills) as sumCreepKills 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month",			
				"AVG Creep Denies" => "SELECT name as topUser, sumCreepDenies/totGames as topValue from (Select b.name, MAX(a.id) as id,
					COUNT(*) as totGames,
					SUM(creepdenies) as sumCreepDenies 
					FROM dotaplayers AS a 
					LEFT JOIN gameplayers AS b ON a.gameid = b.gameid and a.colour = b.colour 
					LEFT JOIN games as c on a.gameid = c.id 
					LEFT JOIN dotagames as d on d.gameid = c.id
					where winner <> 0 AND ".$sqlGroupBy1." = $year AND ".$sqlGroupBy2." = $month");
				
			foreach($arrStatRow as $title => $sql)
			{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="contentheadercell"><?php print $title; ?></td>
					</tr>
<?php

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
					$topUser = $row["topUser"];
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
						<td align=center width=80px height=22px colspan=2>
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
						<td align=center width=80px height=22px colspan=2>
							(---)
						</td>
						<td align=left>
							N/A
						</td>
					</tr>
<?php	
				}
?>
					<tr>
						<td colspan=3 class="contentemptyspacer"></td>
					<tr>
				</table>
			</td>
			
<?php
			}
?>
		</tr>


<?php
		} // ############################################ __END__ MYSQL Stats Row 5 #####################################################

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
