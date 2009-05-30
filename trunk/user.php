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
	$username=strtolower(sqlite_escape_string($_GET["u"]));
	$sortcat=sqlite_escape_string($_GET["s"]);
	$order=sqlite_escape_string($_GET["o"]);
}
else
{
	$username=strtolower(mysql_real_escape_string($_GET["u"]));
	$sortcat=mysql_real_escape_string($_GET["s"]);
	$order=mysql_real_escape_string($_GET["o"]);

}

//Determine if user exists
$count = 0;

$sql = "SELECT count(*) as count FROM gameplayers where name = '$username'";
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
}
if($count == 0)
{
	//Shows a list of usernames that contains the word searched for
	$sql = "SELECT name, count(1) as counttimes FROM gameplayers where name like '%$username%' group by name order by counttimes desc, name asc";
		$foundCount = 0;
		if($dbType == 'sqlite')
		{
			//Check if there is only one result:
			foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
			{
				$founduser=$row["name"];
				$foundCount = $foundCount+1;
			}
			if($foundCount == 1)
			{
				header("Location: ?p=user&u=$founduser&s=datetime&o=desc");
			}
			else
			{ ?>
			<table class="table" id="theader">
			  <tr class="rowuh">
				  <td align="center" >
					<h2> List of users matching <?php print $username; ?> on <?php print $botName;?>:</h2>
				  </td>
				</tr>
			</table>
			<div id="datawrapper">
			<table class="table" id="data">
			<?php
			    $counttimes = false;
				foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
				{
					$counttimes=$row["counttimes"];
					$founduser=$row["name"];
					print "<tr class=\"row\"> <td><a href=\"?p=user&u=$founduser&s=datetime&o=desc\">$founduser : $counttimes games.</a></td></tr>";
				}
				if($counttimes==false){ print "Sorry no users found matching that criteria.";}
			}
		}
		else
		{
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
			{
				$founduser=$row["name"];
				$foundCount = $foundCount+1;
			}
			if($foundCount == 1)
			{
				header("Location: ?p=user&u=$founduser&s=datetime&o=desc");
			}
			else
			{ ?>
				<table class="table" id="theader">
				  <tr class="rowuh">
					  <td align="center" >
						<h2> List of users matching <?php print $username; ?> on <?php print $botName;?>:</h2>
					  </td>
					</tr>
				</table>
				<div id="datawrapper">
				<table class="table" id="data">
				<?php
				$counttimes = false;
				$result = mysql_query($sql);
				while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
				{
					$founduser=$row["name"];
					$counttimes=$row["counttimes"];
					print "<tr class=\"row\"> <td><a href=\"?p=user&u=$founduser&s=datetime&o=desc\">$founduser : $counttimes games.</a></td></tr>";
				}
				if($counttimes==false){ print "<tr class=\"rowuh\"> <td> Sorry no users found matching that criteria.</td></tr>";}
			}
		}
		
		?>
		</table>
		</div>
<?php
}
else
{
if($dbType == 'sqlite')
{
	$mostkillscount="0";
	$mostkillshero="blank";
	$mostdeathscount="0";
	$mostdeathshero="blank";
	$mostassistscount="0";
	$mostassistshero="blank";
	$mostwinscount="0";
	$mostwinshero="blank";
	$mostlossescount="0";
	$mostlosseshero="blank";
	$mostplayedcount="0";
	$mostplayedhero="blank";
	
	
	//Find top heroes for this dude!
	//find hero with most kills
	$sql = "SELECT hero, max(kills) FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour where name= '$username' group by hero ORDER BY max(kills) DESC LIMIT 1 ";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$mostkillshero=checkIfAliasSQLite($row["hero"], $dbType, $dbHandle);
		$mostkillscount=$row["max(kills)"];
	}
	//find hero with most deaths
	$sql = "SELECT hero, max(deaths) FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour where name= '$username' group by hero ORDER BY max(kills) DESC LIMIT 1 ";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$mostdeathshero=checkIfAliasSQLite($row["hero"], $dbType, $dbHandle);
		$mostdeathscount=$row["max(deaths)"];
	}
	//find hero with most assists
	$sql = "SELECT hero, max(assists) FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour where name= '$username' group by hero ORDER BY max(kills) DESC LIMIT 1 ";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$mostassistshero=checkIfAliasSQLite($row["hero"], $dbType, $dbHandle);
		$mostassistscount=$row["max(assists)"];
	}
	//get hero with most wins
	$sql = "SELECT hero, COUNT(*) as wins FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' AND((winner=1 AND dotaplayers.newcolour>=1 AND dotaplayers.newcolour<=5) OR (winner=2 AND dotaplayers.newcolour>=7 AND dotaplayers.newcolour<=11)) group by hero order by wins desc limit 1";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$mostwinshero=checkIfAliasSQLite($row["hero"], $dbType, $dbHandle);
		$mostwinscount=$row["wins"];
		//put an blank if you ahvent won
		if(!isset($mostwinscount)){ $mostwinshero="blank"; $mostwinscount="0";}
	}
	//get hero with most losses
	$sql = "SELECT hero, COUNT(*) as losses FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' AND((winner=2 AND dotaplayers.newcolour>=1 AND dotaplayers.newcolour<=5) OR (winner=1 AND dotaplayers.newcolour>=7 AND dotaplayers.newcolour<=11)) group by hero order by losses desc limit 1";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$mostlosseshero=checkIfAliasSQLite($row["hero"], $dbType, $dbHandle);
		$mostlossescount=$row["losses"];
		//put an x if you ahvent lost
		if($mostlossescount==""){ $mostlosseshero="blank"; $mostlossescount="0";}
	}
	//get hero you have played most with
	$sql = "SELECT SUM(`left`) as timeplayed, hero, COUNT(*) as played FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' and hero <> '' group by hero order by played asc";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$mostplayedhero=checkIfAliasSQLite($row["hero"], $dbType, $dbHandle);
		$mostplayedcount=$row["played"];
		$mostplayedtime=secondsToTime($row["timeplayed"]);
	}
//get avg loadingtimes
	$sql = "SELECT datetime, MIN(loadingtime), MAX(loadingtime), AVG(loadingtime) FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' AND winner!=0";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$firstgame=$row["datetime"];
		$minLoading=millisecondsToTime($row["MIN(loadingtime)"]);
		$maxLoading=millisecondsToTime($row["MAX(loadingtime)"]);
		$avgLoading=millisecondsToTime($row["AVG(loadingtime)"]);
	}

//get lastgame played
	$sql = "SELECT dotagames.gameid, datetime FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' AND winner!=0 order by dotagames.gameid desc";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$lastgame=$row["datetime"];
	}
	
	$score = "";
	//get score
	if($scoreFromDB)	//Using score table
	{
		$sql = "SELECT scores.score from scores where name = '$username'";
	}
	else
	{
		$sql = "select ($scoreFormula) as score from(select *, (kills/deaths) as killdeathratio, (totgames-wins) as losses from (select gp.name as name,gp.gameid as gameid, gp.colour as colour, avg(dp.courierkills) as courierkills, avg(dp.raxkills) as raxkills,
		avg(dp.towerkills) as towerkills, avg(dp.assists) as assists, avg(dp.creepdenies) as creepdenies, avg(dp.creepkills) as creepkills,
		avg(dp.neutralkills) as neutralkills, avg(dp.deaths) as deaths, avg(dp.kills) as kills,
		count(*) as totgames, SUM(case when((dg.winner = 1 and dp.newcolour < 6) or (dg.winner = 2 and dp.newcolour > 6)) then 1 else 0 end) as wins
		from gameplayers as gp LEFT JOIN dotagames as dg ON gp.gameid = dg.gameid LEFT JOIN games as ga ON ga.id = dg.gameid LEFT JOIN 
		dotaplayers as dp on dp.gameid = dg.gameid and gp.colour = dp.colour where dg.winner <> 0 and gp.name = '$username') as h) as i";
	}
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$score=$row["score"];
	}
		
	$sql = "SELECT COUNT(a.id), SUM(kills), SUM(deaths), SUM(creepkills), SUM(creepdenies), SUM(assists), SUM(neutralkills), SUM(towerkills), SUM(raxkills), SUM(courierkills), name FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour where name= '$username' group by name ORDER BY sum(kills)";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$kills=$row["SUM(kills)"];
		$death=$row["SUM(deaths)"];
		$assists=$row["SUM(assists)"];
		$creepkills=$row["SUM(creepkills)"];
		$creepdenies=$row["SUM(creepdenies)"];
		$neutralkills=$row["SUM(neutralkills)"];
		$towerkills=$row["SUM(towerkills)"];
		$raxkills=$row["SUM(raxkills)"];
		$courierkills=$row["SUM(courierkills)"];
		$name=$row["name"];
		$totgames=$row["COUNT(a.id)"];
		}
		//calculate wins
		$wins=getWinsSQLite($username, $dbType, $dbHandle);
		//calculate losses
		$losses=getLossesSQLite($username, $dbType, $dbHandle);
?>

<table class="table" id="introtable">
	<tr>
	<td colspan=2>
	<h2>User Statistics for: <?php print $username;?></h2>
	</td>
	</tr>
  <tr class="rowuh"> 
    <td colspan=2 align="left"><b>Loading Times(sec): 
      MAX: <?php print $maxLoading;?> | MIN: <?php print $minLoading;?> | AVG: <?php print $avgLoading;?> 
    </td>
  </tr>
  <tr class="tableheader">
	<td align="center" width=34%>All Time Stats:</td>
	<td  align="center" width = 66%>Highest Hero Stats:</td>
  </tr>
  <tr> 
    <td> <table class="rowuh" width="100%">
        <tr> 
          <td>Kills:</td>
          <td><?php print $kills;?></td>
		  <td>Deaths: </td>
          <td><?php print $death;?></td>
		  
        </tr>
        <tr> 
          <td>Assists:</td>
          <td><?php print $assists;?></td>
		  <td>Kills/Death:</td>
          <td><?php print round(($kills/$death),2);?></td>
		  
        </tr>
		<tr height=10px>
		</tr>
        <tr>
			<td>Games:</td>
          <td><?php print $wins+$losses;?></td>		
		  <td>Wins/Losses:</td>
          <td><?php print $wins;?>/<?php print $losses;?></td>
		  
        </tr>
		<tr>
		  <td>Score:</td>
          <td><?php print ROUND($score, 2); ?></td>
		  <td>Wins per Game:</td>
          <td><?php print round($wins/($wins+$losses), 2);?></td>
		</tr>
		<tr height=10px>
		</tr>
        <tr> 
          <td>Creep Kills:</td>
          <td><?php print $creepkills;?></td>
		  <td>Creep Denies:</td>
          <td><?php print $creepdenies;?></td>
        </tr>
		<tr> 
          <td>Tower Kills:</td>
          <td><?php print $towerkills;?></td>
		  <td>Rax Kills:</td>
          <td><?php print $raxkills;?></td>
        </tr>
        <tr> 
		  <td>Courier Kills:</td>
          <td><?php print $courierkills;?></td>
        </tr>
      </table></td>
    <td align='center'  scope=col rowspan="2"> 
	<table class="rowuh">
        <tr> 
          <td align=center><strong>Kills</strong></td>
          <td align=center ><strong>Deaths</strong></td>
          <td align=center ><strong>Assists</strong></td>
          <td align=center><strong>Wins</strong></td>
          <td align=center><strong>Losses</strong></td>
          <td align=center><strong>Times Played</strong></td>
        </tr>
        <tr> 
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostkillshero;?>&s=kdratio&o=desc"><img src="img/heroes/<?php print $mostkillshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostdeathshero;?>&s=kdratio&o=desc"><img src="img/heroes/<?php print $mostdeathshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostassistshero;?>&s=kdratio&o=desc"><img src="img/heroes/<?php print $mostassistshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostwinshero;?>&s=kdratio&o=desc"><img src="img/heroes/<?php print $mostwinshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostlosseshero;?>&s=kdratio&o=desc"><img src="img/heroes/<?php print $mostlosseshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostplayedhero;?>&s=kdratio&o=desc"><img src="img/heroes/<?php print $mostplayedhero; ?>.gif" width="64" height="64"></a></td>
        </tr>
        <tr> 
          <td align=center ><strong>(<?php print $mostkillscount;?>)</strong></td>
          <td align=center ><strong>(<?php print $mostdeathscount;?>)</strong></td>
          <td align=center ><strong>(<?php print $mostassistscount;?>)</strong></td>
          <td align=center ><strong>(<?php print $mostwinscount;?>)</strong></td>
          <td align=center ><strong>(<?php print $mostlossescount;?>)</strong></td>
          <td align=center ><strong>(<?php print $mostplayedcount;?>)</strong></td>
        </tr>
      </table></td>
  </tr>
</table>
<table class="table" id="theader">
  <tr class="rowuh">
  <td colspan=12 align="center" >
	<h3><strong>Game History:</strong></h3>
  </td>
  <tr class="tableheader">
  <?php
  //Date and Time
	if($sortcat == "datetime")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=user&u=$username&s=datetime&o=desc\">Date and Time</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=user&u=$username&s=datetime&o=asc\">Date and Time</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=user&u=$username&s=datetime&o=desc\">Date and Time</a></td>");
	}
	//Game Name
	if($sortcat == "gamename")
	{
		if($order == "asc")
		{
			print("<td><a href=\"?p=user&u=$username&s=gamename&o=desc\">Game Name</a></td>");
		}
		else
		{
			print("<td><a href=\"?p=user&u=$username&s=gamename&o=asc\">Game Name</a></td>");
		}
	}
	else
	{
		print("<td><a href=\"?p=user&u=$username&s=gamename&o=asc\">Game Name</a></td>");
	}
	//Game Type
	if($sortcat == "type")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=type&o=desc\">Type</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=type&o=asc\">Type</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=type&o=desc\">Type</a></td>");
	}
	//Hero
	if($sortcat == "description")
	{
		if($order == "asc")
		{
			print("<td width=186px><a href=\"?p=user&u=$username&s=description&o=desc\">Hero Played</a></td>");
		}
		else
		{
			print("<td width=186px><a href=\"?p=user&u=$username&s=description&o=asc\">Hero Played</a></td>");
		}
	}
	else
	{
		print("<td width=186px><a href=\"?p=user&u=$username&s=description&o=asc\">Hero Played</a></td>");
	}
	//Kills
	if($sortcat == "kills")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=kills&o=desc\">Kills</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=kills&o=asc\">Kills</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=kills&o=desc\">Kills</a></td>");
	}
	
	//Deaths
	if($sortcat == "deaths")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=deaths&o=desc\">Deaths</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=deaths&o=asc\">Deaths</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=deaths&o=desc\">Deaths</a></td>");
	}
	
	//Assists
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=assists&o=desc\">Assists</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=assists&o=asc\">Assists</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=assists&o=desc\">Assists</a></td>");
	}
	//KDRatio
	if($sortcat == "kdratio")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=kdratio&o=desc\">K/D Ratio</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=kdratio&o=asc\">K/D Ratio</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=kdratio&o=desc\">K/D Ratio</a></td>");
	}
	//Creep Kills
	if($sortcat == "creepkills")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=creepkills&o=desc\">Creep<br>Kills</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=creepkills&o=asc\">Creep<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=creepkills&o=desc\">Creep<br>Kills</a></td>");
	}
	//Creep Denies
	if($sortcat == "creepdenies")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=creepdenies&o=desc\">Creep<br>Denies</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=creepdenies&o=asc\">Creep<br>Denies</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=creepdenies&o=desc\">Creep<br>Denies</a></td>");
	}
	//Neutral Kills
	if($sortcat == "neutralkills")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=neutralkills&o=desc\">Neutral<br>Kills</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=neutralkills&o=asc\">Neutral<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=neutralkills&o=desc\">Neutral<br>Kills</a></td>");
	}
	//Outcome
	if($sortcat == "outcome")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=outcome&o=desc\">Result</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=outcome&o=asc\">Result</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=outcome&o=desc\">Result</a></td>");
	}
  ?>
  </tr>
  </table>
<div id="datawrapper">
	<table class="table" id="data">
 <?php 

 $sql = "SELECT winner, a.gameid as id, newcolour, datetime, gamename, description, hero, kills, deaths, assists, creepkills, creepdenies, neutralkills, name, CASE when(gamestate = '17') then 'PRIV' else 'PUB' end as type,
 CASE WHEN (deaths = 0 and kills = 0) THEN 0 WHEN (deaths = 0) then 1000 ELSE (kills*1.0/deaths) end as kdratio,
 CASE when (winner=1 and newcolour < 6) or (winner=2 and newcolour > 5) then 'WON' when  winner=0 then 'DRAW' else 'LOST' end as outcome 
 FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN dotagames AS c ON c.gameid = a.gameid 
 LEFT JOIN games AS d ON d.id = a.gameid LEFT JOIN originals as e ON a.hero = heroid where name= '$username' and description <> 'NULL' ORDER BY $sortcat $order, d.id DESC";
foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
    $gametime=$row["datetime"];
	$kills=$row["kills"];
	$death=$row["deaths"];
    $assists=$row["assists"];
	$gamename=$row["gamename"];
	$hid=$row["hero"];
	$hid=checkIfAliasSQlite($hid, $dbType, $dbHandle);
	$hero=$row["description"];
	$name=$row["name"];
	$colour=$row["newcolour"];
	$winner=$row["winner"];
	$gameid=$row["id"]; 
	$type=$row["type"];
	$outcome=$row["outcome"];
	$kdratio = $row["kdratio"];
	$creepkills=$row["creepkills"];
	$creepdenies=$row["creepdenies"];
	$neutralkills=$row["neutralkills"];
 ?> 
 <tr class="row">
	<td width=15%><?php print $gametime;?></td>
    <td><a href="?p=gameinfo&gid=<?php print $gameid; ?>" target="_self"><Strong><?php print $gamename;?></strong></a></td>
    <td width=70px><?php print $type;?></td>
	<td width=34px><a  href="?p=hero&hid=<?php print$hid;?>&s=kdratio&o=desc"><img width="32px" height="32px" src=./img/heroes/<?php print $hid; ?>.gif></a></td>
	<td width=150px><a  href="?p=hero&hid=<?php print $hid;?>&s=kdratio&o=desc"><?php print $hero;?></a></td>
    <td width=70px><?php print $kills;?></td>
    <td width=70px><?php print $death;?></td>
    <td width=70px><?php print $assists;?></td>
    <td width=70px><?php print round($kdratio, 2);?></td>
	<td width=70px><?php print $creepkills;?></td>
    <td width=70px><?php print $creepdenies;?></td>
	<td width=70px><?php print $neutralkills;?></td>
	<td width=70px> <span <?php if($outcome == 'LOST'){print 'style="color:#cc6666"';}elseif($outcome == 'WON'){print 'style="color:#66cc66"';} else{print 'style="color:#daa701"';} ?>><?php print $outcome;?></span></td>
</tr>
	
	<?php
	}
}
else
{
//Find top heroes for this dude!
//find hero with most kills
$result = mysql_query("SELECT hero, max(kills) FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour where name= '$username' group by hero ORDER BY max(kills) DESC LIMIT 1 ");
$row = mysql_fetch_array($result, MYSQL_ASSOC);
$mostkillshero=checkIfAliasMySQL($row["hero"]);
$mostkillscount=$row["max(kills)"];
//find hero with most deaths
$result = mysql_query("SELECT hero, max(deaths) FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour where name= '$username' group by hero ORDER BY max(deaths) DESC LIMIT 1 ");
$row = mysql_fetch_array($result, MYSQL_ASSOC);
$mostdeathshero=checkIfAliasMySQL($row["hero"]);
$mostdeathscount=$row["max(deaths)"];
//find hero with most assists
$result = mysql_query("SELECT hero, max(assists) FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour where name= '$username' group by hero ORDER BY max(assists) DESC LIMIT 1 ");
$row = mysql_fetch_array($result, MYSQL_ASSOC);
$mostassistshero=checkIfAliasMySQL($row["hero"]);
$mostassistscount=$row["max(assists)"];
//get hero with most wins
$result = mysql_query("SELECT hero, COUNT(*) as wins FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' AND((winner=1 AND dotaplayers.newcolour>=1 AND dotaplayers.newcolour<=5) OR (winner=2 AND dotaplayers.newcolour>=7 AND dotaplayers.newcolour<=11)) group by hero order by wins desc limit 1");
$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$mostwinshero=checkIfAliasMySQL($row["hero"]);
	$mostwinscount=$row["wins"];
	//put an blank if you ahvent won
	if($mostwinscount==""){ $mostwinshero="blank"; $mostwinscount="0";}
//get hero with most losses
$result = mysql_query("SELECT hero, COUNT(*) as losses FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' AND((winner=2 AND dotaplayers.newcolour>=1 AND dotaplayers.newcolour<=5) OR (winner=1 AND dotaplayers.newcolour>=7 AND dotaplayers.newcolour<=11)) group by hero order by losses desc limit 1");
$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$mostlosseshero=checkIfAliasMySQL($row["hero"]);
	$mostlossescount=$row["losses"];
	//put an x if you ahvent lost
	if($mostlossescount==""){ $mostlosseshero="blank"; $mostlossescount="0";}
//get hero you have played most with
$result = mysql_query("SELECT SUM(`left`) as timeplayed, hero, COUNT(*) as played FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' and hero<>'' group by hero order by played desc");	
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$mostplayedhero=checkIfAliasMySQL($row["hero"]);
	$mostplayedcount=$row["played"];
	$mostplayedtime=secondsToTime($row["timeplayed"]);
//get avg loadingtimes
$sql = "SELECT datetime, MIN(loadingtime), MAX(loadingtime), AVG(loadingtime) FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username'";
$result = mysql_query($sql);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$firstgame=$row["datetime"];
	$minLoading=millisecondsToTime($row["MIN(loadingtime)"]);
	$maxLoading=millisecondsToTime($row["MAX(loadingtime)"]);
	$avgLoading=millisecondsToTime($row["AVG(loadingtime)"]);
//get lastgame played
$result = mysql_query("SELECT dotagames.gameid, datetime FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' AND winner!=0 order by dotagames.gameid desc");
$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$lastgame=$row["datetime"];
	
if($scoreFromDB)	//Using score table
{
	$sql = "SELECT scores.score from scores where name = '$username'";
}
else
{
	$sql = "select ($scoreFormula) as score from(select *, (kills/deaths) as killdeathratio, (totgames-wins) as losses from (select gp.name as name,gp.gameid as gameid, gp.colour as colour, avg(dp.courierkills) as courierkills, avg(dp.raxkills) as raxkills,
		avg(dp.towerkills) as towerkills, avg(dp.assists) as assists, avg(dp.creepdenies) as creepdenies, avg(dp.creepkills) as creepkills,
		avg(dp.neutralkills) as neutralkills, avg(dp.deaths) as deaths, avg(dp.kills) as kills,
		count(*) as totgames, SUM(case when((dg.winner = 1 and dp.newcolour < 6) or (dg.winner = 2 and dp.newcolour > 6)) then 1 else 0 end) as wins
		from gameplayers as gp LEFT JOIN dotagames as dg ON gp.gameid = dg.gameid LEFT JOIN games as ga ON ga.id = dg.gameid LEFT JOIN 
		dotaplayers as dp on dp.gameid = dg.gameid and gp.colour = dp.colour where dg.winner <> 0 and gp.name = '$username' and gp.colour <= 10) as h) as i";
}
$result = mysql_query($sql);
$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$score=$row["score"];
	
$result = mysql_query("SELECT COUNT(a.id), SUM(kills), SUM(deaths), SUM(creepkills), SUM(creepdenies), SUM(assists), SUM(neutralkills), SUM(towerkills), SUM(raxkills), SUM(courierkills), name FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour where name= '$username' group by name ORDER BY sum(kills) desc ");
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$kills=$row["SUM(kills)"];
	$death=$row["SUM(deaths)"];
    $assists=$row["SUM(assists)"];
	$creepkills=$row["SUM(creepkills)"];
	$creepdenies=$row["SUM(creepdenies)"];
	$neutralkills=$row["SUM(neutralkills)"];
	$towerkills=$row["SUM(towerkills)"];
	$raxkills=$row["SUM(raxkills)"];
	$courierkills=$row["SUM(courierkills)"];
	$name=$row["name"];
	$totgames=$row["COUNT(a.id)"];
	}
	//calculate wins
	$wins=getWinsMySQL($username);
	//calculate losses
	$losses=getLossesMySQL($username);
?>

<table class="table" id="introtable">
	<tr>
	<td colspan=2>
	<h2>User Statistics for: <?php print $username;?></h2>
	</td>
	</tr>
  <tr class="rowuh"> 
    <td colspan=2 align="left"><b>Loading Times(sec): 
      MAX: <?php print $maxLoading;?> | MIN: <?php print $minLoading;?> | AVG: <?php print $avgLoading;?> 
    </td>
  </tr>
  <tr class="tableheader">
	<td align="center" width=34%>All Time Stats:</td>
	<td  align="center" width = 66%>Highest Hero Stats:</td>
  </tr>
  <tr> 
    <td> <table class="rowuh" width="100%">
        <tr> 
          <td>Kills:</td>
          <td><?php print $kills;?></td>
		  <td>Deaths: </td>
          <td><?php print $death;?></td>
		  
        </tr>
        <tr> 
          <td>Assists:</td>
          <td><?php print $assists;?></td>
		  <td>Kills/Death:</td>
          <td><?php if($death != 0) {print round(($kills/$death),2); } else { print 1000; }?></td>
		  
        </tr>
		<tr height=10px>
		</tr>
        <tr>
			<td>Games:</td>
          <td><?php print $wins+$losses;?></td>		
		  <td>Wins/Losses:</td>
          <td><?php print $wins;?>/<?php print $losses;?></td>
		  
        </tr>
		<tr>
		  <td>Score:</td>
          <td><?php print ROUND($score, 2); ?></td>
		  <td>Wins per Game:</td>
          <td><?php print round($wins/($wins+$losses), 2);?></td>
		</tr>
		<tr height=10px>
		</tr>
        <tr> 
          <td>Creep Kills:</td>
          <td><?php print $creepkills;?></td>
		  <td>Creep Denies:</td>
          <td><?php print $creepdenies;?></td>
        </tr>
		<tr> 
          <td>Tower Kills:</td>
          <td><?php print $towerkills;?></td>
		  <td>Rax Kills:</td>
          <td><?php print $raxkills;?></td>
        </tr>
        <tr> 
		  <td>Courier Kills:</td>
          <td><?php print $courierkills;?></td>
        </tr>
      </table></td>
    <td align='center'  scope=col rowspan="2"> 
	<table class="rowuh">
        <tr> 
          <td align=center><strong>Kills</strong></td>
          <td align=center ><strong>Deaths</strong></td>
          <td align=center ><strong>Assists</strong></td>
          <td align=center><strong>Wins</strong></td>
          <td align=center><strong>Losses</strong></td>
          <td align=center><strong>Times Played</strong></td>
        </tr>
        <tr> 
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostkillshero;?>&s=kdratio&o=desc"><img src="img/heroes/<?php print $mostkillshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostdeathshero;?>&s=kdratio&o=desc"><img src="img/heroes/<?php print $mostdeathshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostassistshero;?>&s=kdratio&o=desc"><img src="img/heroes/<?php print $mostassistshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostwinshero;?>&s=kdratio&o=desc"><img src="img/heroes/<?php print $mostwinshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostlosseshero;?>&s=kdratio&o=desc"><img src="img/heroes/<?php print $mostlosseshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostplayedhero;?>&s=kdratio&o=desc"><img src="img/heroes/<?php print $mostplayedhero; ?>.gif" width="64" height="64"></a></td>
        </tr>
        <tr> 
          <td align=center ><strong>(<?php print $mostkillscount;?>)</strong></td>
          <td align=center ><strong>(<?php print $mostdeathscount;?>)</strong></td>
          <td align=center ><strong>(<?php print $mostassistscount;?>)</strong></td>
          <td align=center ><strong>(<?php print $mostwinscount;?>)</strong></td>
          <td align=center ><strong>(<?php print $mostlossescount;?>)</strong></td>
          <td align=center ><strong>(<?php print $mostplayedcount;?>)</strong></td>
        </tr>
      </table></td>
  </tr>
</table>
<table class="table" id="theader">
  <tr class="rowuh">
  <td colspan=12 align="center" >
	<h3><strong>Game History:</strong></h3>
  </td>
  <tr class="tableheader">
  <?php
  //Date and Time
	if($sortcat == "datetime")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=user&u=$username&s=datetime&o=desc\">Date and Time</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=user&u=$username&s=datetime&o=asc\">Date and Time</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=user&u=$username&s=datetime&o=desc\">Date and Time</a></td>");
	}
	//Game Name
	if($sortcat == "gamename")
	{
		if($order == "asc")
		{
			print("<td><a href=\"?p=user&u=$username&s=gamename&o=desc\">Game Name</a></td>");
		}
		else
		{
			print("<td><a href=\"?p=user&u=$username&s=gamename&o=asc\">Game Name</a></td>");
		}
	}
	else
	{
		print("<td><a href=\"?p=user&u=$username&s=gamename&o=asc\">Game Name</a></td>");
	}
	//Game Type
	if($sortcat == "type")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=type&o=desc\">Type</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=type&o=asc\">Type</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=type&o=desc\">Type</a></td>");
	}
	//Hero
	if($sortcat == "description")
	{
		if($order == "asc")
		{
			print("<td width=186px><a href=\"?p=user&u=$username&s=description&o=desc\">Hero Played</a></td>");
		}
		else
		{
			print("<td width=186px><a href=\"?p=user&u=$username&s=description&o=asc\">Hero Played</a></td>");
		}
	}
	else
	{
		print("<td width=186px><a href=\"?p=user&u=$username&s=description&o=asc\">Hero Played</a></td>");
	}
	//Kills
	if($sortcat == "kills")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=kills&o=desc\">Kills</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=kills&o=asc\">Kills</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=kills&o=desc\">Kills</a></td>");
	}
	
	//Deaths
	if($sortcat == "deaths")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=deaths&o=desc\">Deaths</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=deaths&o=asc\">Deaths</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=deaths&o=desc\">Deaths</a></td>");
	}
	
	//Assists
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=assists&o=desc\">Assists</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=assists&o=asc\">Assists</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=assists&o=desc\">Assists</a></td>");
	}
	//KDRatio
	if($sortcat == "kdratio")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=kdratio&o=desc\">K/D Ratio</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=kdratio&o=asc\">K/D Ratio</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=kdratio&o=desc\">K/D Ratio</a></td>");
	}
	//Creep Kills
	if($sortcat == "creepkills")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=creepkills&o=desc\">Creep<br>Kills</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=creepkills&o=asc\">Creep<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=creepkills&o=desc\">Creep<br>Kills</a></td>");
	}
	//Creep Denies
	if($sortcat == "creepdenies")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=creepdenies&o=desc\">Creep<br>Denies</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=creepdenies&o=asc\">Creep<br>Denies</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=creepdenies&o=desc\">Creep<br>Denies</a></td>");
	}
	//Neutral Kills
	if($sortcat == "neutralkills")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=neutralkills&o=desc\">Neutral<br>Kills</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=neutralkills&o=asc\">Neutral<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=neutralkills&o=desc\">Neutral<br>Kills</a></td>");
	}
	//Outcome
	if($sortcat == "outcome")
	{
		if($order == "asc")
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=outcome&o=desc\">Result</a></td>");
		}
		else
		{
			print("<td width=70px><a href=\"?p=user&u=$username&s=outcome&o=asc\">Result</a></td>");
		}
	}
	else
	{
		print("<td width=70px><a href=\"?p=user&u=$username&s=outcome&o=desc\">Result</a></td>");
	}
  ?>
  </tr>
  </table>
<div id="datawrapper">
	<table class="table" id="data">
 <?php 

 $sql = "SELECT winner, a.gameid as id, newcolour, datetime, gamename, description, hero, kills, deaths, assists, creepkills, creepdenies, neutralkills, name, CASE when(gamestate = '17') then 'PRIV' else 'PUB' end as type,
 CASE WHEN (deaths = 0 and kills = 0) THEN 0 WHEN (deaths = 0) then 1000 ELSE (kills*1.0/deaths) end as kdratio,
 CASE when (winner=1 and newcolour < 6) or (winner=2 and newcolour > 5) then 'WON' when  winner=0 then 'DRAW' else 'LOST' end as outcome 
 FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN dotagames AS c ON c.gameid = a.gameid 
 LEFT JOIN games AS d ON d.id = a.gameid LEFT JOIN originals as e ON a.hero = heroid where name= '$username' and description <> 'NULL' ORDER BY $sortcat $order, d.id DESC";
 $result = mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $gametime=$row["datetime"];
	$kills=$row["kills"];
	$death=$row["deaths"];
    $assists=$row["assists"];
	$gamename=$row["gamename"];
	$hid=$row["hero"];
	$hid=checkIfAliasMySQL($hid);
	$hero=$row["description"];
	$name=$row["name"];
	$colour=$row["newcolour"];
	$winner=$row["winner"];
	$gameid=$row["id"]; 
	$type=$row["type"];
	$outcome=$row["outcome"];
	$kdratio = $row["kdratio"];
	$creepkills=$row["creepkills"];
	$creepdenies=$row["creepdenies"];
	$neutralkills=$row["neutralkills"];
 ?> 
 <tr class="row">
 <td width=15%><?php print $gametime;?></td>
    <td><a href="?p=gameinfo&gid=<?php print $gameid; ?>" target="_self"><Strong><?php print $gamename;?></strong></a></td>
    <td width=70px><?php print $type;?></td>
	<td width=34px><a  href="?p=hero&hid=<?php print$hid;?>&s=kdratio&o=desc"><img width="32px" height="32px" src=./img/heroes/<?php print $hid; ?>.gif></a></td>
	<td width=150px><a  href="?p=hero&hid=<?php print $hid;?>&s=kdratio&o=desc"><?php print $hero;?></a></td>
    <td width=70px><?php print $kills;?></td>
    <td width=70px><?php print $death;?></td>
    <td width=70px><?php print $assists;?></td>
    <td width=70px><?php print round($kdratio, 2);?></td>
	<td width=70px><?php print $creepkills;?></td>
    <td width=70px><?php print $creepdenies;?></td>
	<td width=70px><?php print $neutralkills;?></td>
	<td width=70px> <span <?php if($outcome == 'LOST'){print 'style="color:#cc6666"';}elseif($outcome == 'WON'){print 'style="color:#66cc66"';} else{print 'style="color:#daa701"';} ?>><?php print $outcome;?></span></td>
</tr>	
	
	<?php
	}
}
}
	?>
</table>
</div>
</body>
</html>
