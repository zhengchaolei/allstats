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

$sql = "Select count(*) as count FROM
    (SELECT count(*) as totgames FROM dotaplayers AS a LEFT JOIN originals as b ON hero = heroid LEFT JOIN dotagames as c ON c.gameid = a.gameid 
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
	$sql= $sql." group by description) as z where z.totgames > 0";
	
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

	$pages = ceil($count/$allHeroResultSize);
?>

<div class="header" id="header">
	<table width=1016px>
		<tr>
			<td width=25%>
				<table class="rowuh" width = 235px style="float:left">
					<tr>
						<td>
						<?php
						if($offset == 'all')
						{
							print "Showing All Heroes";
						}
						else
						{
							print "<a href=\"?p=heroall&s=".$sortcat."&o=".$order."&n=all\"><strong>Show All Heroes</strong></a>";
						}
						?>
						</td>
					</tr>
				</table>
			</td>
			<td width=50%>
				<h2>Hero Statistics <?php if($ignorePubs){ print "for Private Games";} else if($ignorePrivs){ print "for Public Games";} else { print "for All Games";} ?>:</h2>
			</td>
			<td width=25% class="rowuh">
				<table class="rowuh" width = 235px style="float:right">
				<h4>
				<tr>
					<td colspan=7>
					<?php
					if($offset == 'all')
					{
						print "Show Heroes Page:";
					}
					else
					{
						$min = $offset*$allHeroResultSize+1;
						$max = $offset*$allHeroResultSize+$allHeroResultSize;
						if($max > $count)
						{
							$max = $count;
						}
						print "Showing Heroes: ".$min." - ".$max;
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
						print "<td width=35px><a href=\"?p=heroall&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
						}
					}
					print "<td width=35px><span style=\"color:#ddd;\">></span></td>";
				}
				else
				{
					if($offset > 0)
					{
						print "<td width=35px><a href=\"?p=heroall&s=".$sortcat."&o=".$order."&n=".($offset-1)."\"><strong><</strong></a>";
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
									print "<td width=35px><a href=\"?p=heroall&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
						}
						if($offset == 1)
						{
							print "<td width=35px><a href=\"?p=heroall&s=".$sortcat."&o=".$order."&n=0\">1</a></td>";
							print "<td width=35px><span style=\"color:#ddd;\">2</span></td>";
							for($counter = 3; $counter < 6; $counter++)
							{
								if($counter-1 < $pages)
								{
								print "<td width=35px><a href=\"?p=heroall&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
								
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
								print "<td width=35px><a href=\"?p=heroall&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
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
									print "<td width=35px><a href=\"?p=heroall&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
							print "<td width=35px><span style=\"color:#ddd;\">".($offset+1)."</span>";
							print "<td width=35px><a href=\"?p=heroall&s=".$sortcat."&o=".$order."&n=".($offset+1)."\">".($offset+2)."</a></td>";
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
								print "<td width=35px><a href=\"?p=heroall&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
							}
						}
					}
					if(($offset+1)*$allHeroResultSize < $count)
					{
						print "<td width=35px><a href=\"?p=heroall&s=".$sortcat."&o=".$order."&n=".($offset+1)."\"><strong>></strong></a></td>";
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
	  <tr>
		<td class="headercell" width=30px></td>
		  <?php
		  
		 if($offset == 'all')
		 {
			//Hero name
			if($sortcat == "description")
			{
				if($order == "asc")
				{
					print("<td width=145px class=\"headercell\" colspan=2><a href=\"?p=heroall&s=description&o=desc&n=all\">Hero</a></td>");
				}
				else
				{
					print("<td width=145px class=\"headercell\" colspan=2><a href=\"?p=heroall&s=description&o=asc&n=all\">Hero</a></td>");
				}
			}
			else
			{
				print("<td width=145px class=\"headercell\" colspan=2><a href=\"?p=heroall&s=description&o=asc&n=all\">Hero</a></td>");
			}
			
			//Times played
			if($sortcat == "totgames")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=totgames&o=desc&n=all\">Times<br>Played</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=totgames&o=asc&n=all\">Times<br>Played</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=totgames&o=desc&n=all\">Times<br>Played</a></td>");
			}
			
			//Wins
			if($sortcat == "wins")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=wins&o=desc&n=all\">Wins</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=wins&o=asc&n=all\">Wins</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=wins&o=desc&n=all\">Wins</a></td>");
			}
			
			//Losses
			if($sortcat == "losses")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=losses&o=desc&n=all\">Losses</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=losses&o=asc&n=all\">Losses</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=losses&o=desc&n=all\">Losses</a></td>");
			}
			
			//Win/Game ratio
			if($sortcat == "winratio")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=winratio&o=desc&n=all\">Win/Game<br>Ratio</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=winratio&o=asc&n=all\">Win/Game<br>Ratio</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=winratio&o=desc&n=all\">Win/Game<br>Ratio</a></td>");
			}
			
			//Kills
			if($sortcat == "kills")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=kills&o=desc&n=all\">Kills</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=kills&o=asc&n=all\">Kills</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=kills&o=desc&n=all\">Kills</a></td>");
			}
			
			//Deaths
			if($sortcat == "deaths")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=deaths&o=desc&n=all\">Deaths</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=deaths&o=asc&n=all\">Deaths</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=deaths&o=desc&n=all\">Deaths</a></td>");
			}
			
			//Assists
			if($sortcat == "assists")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=assists&o=desc&n=all\">Assists</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=assists&o=asc&n=all\">Assists</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=assists&o=desc&n=all\">Assists</a></td>");
			}
			
			//KDRatio
			if($sortcat == "kdratio")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=kdratio&o=desc&n=all\">K/D<br>Ratio</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=kdratio&o=asc&n=all\">K/D<br>Ratio</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=kdratio&o=desc&n=all\">K/D<br>Ratio</a></td>");
			}
			
			//Creep Kills
			if($sortcat == "assists")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=creepkills&o=desc&n=all\">Creep<br>Kills</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=creepkills&o=asc&n=all\">Creep<br>Kills</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=creepkills&o=desc&n=all\">Creep<br>Kills</a></td>");
			}
			
			//Creep Denies
			if($sortcat == "creepdenies")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=creepdenies&o=desc&n=all\">Creep<br>Denies</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=creepdenies&o=asc&n=all\">Creep<br>Denies</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=creepdenies&o=desc&n=all\">Creep<br>Denies</a></td>");
			}
			
			//Neutral Kills
			if($sortcat == "neutralkills")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=neutralkills&o=desc&n=all\">Neutral<br>Kills</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=neutralkills&o=asc&n=all\">Neutral<br>Kills</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=neutralkills&o=desc&n=all\">Neutral<br>Kills</a></td>");
			}
		}
		else
		{
			//Hero name
			if($sortcat == "description")
			{
				if($order == "asc")
				{
					print("<td width=145px class=\"headercell\" colspan=2><a href=\"?p=heroall&s=description&o=desc&n=0\">Hero</a></td>");
				}
				else
				{
					print("<td width=145px class=\"headercell\" colspan=2><a href=\"?p=heroall&s=description&o=asc&n=0\">Hero</a></td>");
				}
			}
			else
			{
				print("<td width=145px class=\"headercell\" colspan=2><a href=\"?p=heroall&s=description&o=asc&n=0\">Hero</a></td>");
			}
			
			//Times played
			if($sortcat == "totgames")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=totgames&o=desc&n=0\">Times<br>Played</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=totgames&o=asc&n=0\">Times<br>Played</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=totgames&o=desc&n=0\">Times<br>Played</a></td>");
			}
			
			//Wins
			if($sortcat == "wins")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=wins&o=desc&n=0\">Wins</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=wins&o=asc&n=0\">Wins</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=wins&o=desc&n=0\">Wins</a></td>");
			}
			
			//Losses
			if($sortcat == "losses")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=losses&o=desc&n=0\">Losses</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=losses&o=asc&n=0\">Losses</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=losses&o=desc&n=0\">Losses</a></td>");
			}
			
			//Win/Game ratio
			if($sortcat == "winratio")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=winratio&o=desc&n=0\">Win/Game<br>Ratio</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=winratio&o=asc&n=0\">Win/Game<br>Ratio</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=winratio&o=desc&n=0\">Win/Game<br>Ratio</a></td>");
			}
			
			//Kills
			if($sortcat == "kills")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=kills&o=desc&n=0\">Kills</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=kills&o=asc&n=0\">Kills</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=kills&o=desc&n=0\">Kills</a></td>");
			}
			
			//Deaths
			if($sortcat == "deaths")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=deaths&o=desc&n=0\">Deaths</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=deaths&o=asc&n=0\">Deaths</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=deaths&o=desc&n=0\">Deaths</a></td>");
			}
			
			//Assists
			if($sortcat == "assists")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=assists&o=desc&n=0\">Assists</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=assists&o=asc&n=0\">Assists</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=assists&o=desc&n=0\">Assists</a></td>");
			}
			
			//KDRatio
			if($sortcat == "kdratio")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=kdratio&o=desc&n=0\">K/D<br>Ratio</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=kdratio&o=asc&n=0\">K/D<br>Ratio</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=kdratio&o=desc&n=0\">K/D<br>Ratio</a></td>");
			}
			
			//Creep Kills
			if($sortcat == "assists")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=creepkills&o=desc&n=0\">Creep<br>Kills</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=creepkills&o=asc&n=0\">Creep<br>Kills</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=creepkills&o=desc&n=0\">Creep<br>Kills</a></td>");
			}
			
			//Creep Denies
			if($sortcat == "creepdenies")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=creepdenies&o=desc&n=0\">Creep<br>Denies</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=creepdenies&o=asc&n=0\">Creep<br>Denies</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=creepdenies&o=desc&n=0\">Creep<br>Denies</a></td>");
			}
			
			//Neutral Kills
			if($sortcat == "neutralkills")
			{
				if($order == "asc")
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=neutralkills&o=desc&n=0\">Neutral<br>Kills</a></td>");
				}
				else
				{
					print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=neutralkills&o=asc&n=0\">Neutral<br>Kills</a></td>");
				}
			}
			else
			{
				print("<td class=\"headercell\" width=75px><a href=\"?p=heroall&s=neutralkills&o=desc&n=0\">Neutral<br>Kills</a></td>");
			}
		}
		?>
	<td class="headercell" width=16px></td>
  </tr>
</table>
</div>
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
	if($offset!='all')
	{
	$sql = $sql." LIMIT ".$allHeroResultSize*$offset.", $allHeroResultSize";
	}
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
	<td width=30px><a href="?p=hero&hid=<?php print $hid;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img width="28px" height="28px" src=./img/heroes/<?php print $hid ?>.gif></a></td>
	<td width=145px><a href="?p=hero&hid=<?php print $hid; ?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $hero; ?></a></td>
	<td width=75px><?php print $totgames; ?></td>
	<td width=75px><?php print $wins; ?></td>
	<td width=75px><?php print $losses; ?></td>
	<td width=75px><?php print ROUND($winratio,2); ?></td>
	<td width=75px><?php print ROUND($kills,2); ?></td>
	<td width=75px><?php print ROUND($deaths,2); ?></td>
	<td width=75px><?php print ROUND($assists,2); ?></td>
	<td width=75px><?php print ROUND($kdratio,2); ?></td>
	<td width=75px><?php print ROUND($creepkills,2); ?></td>
	<td width=75px><?php print ROUND($creepdenies,2); ?></td>
	<td width=75px><?php print ROUND($neutralkills,2); ?></td>

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
	<td width=30px><a href="?p=hero&hid=<?php print $hid;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img width="28px" height="28px" src=./img/heroes/<?php print $hid ?>.gif></a></td>
	<td width=145px><a href="?p=hero&hid=<?php print $hid; ?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $hero; ?></a></td>
	<td width=75px><?php print $totgames; ?></td>
	<td width=75px><?php print $wins; ?></td>
	<td width=75px><?php print $losses; ?></td>
	<td width=75px><?php print ROUND($winratio,2); ?></td>
	<td width=75px><?php print ROUND($kills,2); ?></td>
	<td width=75px><?php print ROUND($deaths,2); ?></td>
	<td width=75px><?php print ROUND($assists,2); ?></td>
	<td width=75px><?php print ROUND($kdratio,2); ?></td>
	<td width=75px><?php print ROUND($creepkills,2); ?></td>
	<td width=75px><?php print ROUND($creepdenies,2); ?></td>
	<td width=75px><?php print ROUND($neutralkills,2); ?></td>

	</tr>

	<?php

	}
	mysql_free_result($result);
}
	?>
</table>
</div>
</div>
<div id="footer" class="footer">
</div>