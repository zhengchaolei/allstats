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
	$offset=sqlite_escape_string($_GET["n"]);
}
else
{
	$username=strtolower(mysql_real_escape_string($_GET["u"]));
	$sortcat=mysql_real_escape_string($_GET["s"]);
	$order=mysql_real_escape_string($_GET["o"]);
	$offset=mysql_real_escape_string($_GET["n"]);
}

//Determine if user exists
$count = 0;

$sql = "SELECT count(*) as count FROM gameplayers, dotaplayers where name = '$username' and dotaplayers.colour = gameplayers.colour and dotaplayers.gameid = gameplayers.gameid";
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
	$sql = "SELECT gp.name as name, bans.name as banname, count(1) as counttimes FROM gameplayers gp
		JOIN dotaplayers dp ON dp.colour = gp.colour and dp.gameid = gp.gameid
		LEFT JOIN bans ON bans.name = gp.name
		where gp.name like '%$username%' group by gp.name order by counttimes desc, gp.name asc";
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
				$headerString = "Location: ?p=user&u=".$founduser."&s=datetime&o=desc&n=";
				if($displayStyle == 'all')
				{ 
				$headerString = $headerString.'all';
				} 
				else 
				{
				$headerString = $headerString.'0';
				}
				header($headerString);
			}?>
			<div class="header" id="header">
				<table width=1016px>
				<tr>
				  <td align="center" >
					<h2> List of users matching <?php print $username; ?> on <?php print $botName;?>:</h2>
				  </td>
				</tr>
				<tr height=25px></tr>
			</table>
			</div>
			<div class="pageholder" id="pageholder">
				<div id="theader">
				</div>
				<div id="datawrapper">
					<table class="table" id="data" width=1016px>
			<?php
			    $counttimes = false;
				foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
				{
					$counttimes=$row["counttimes"];
					$founduser=$row["name"];
                                        $banname=$row["banname"];
                                        $output = "<tr class=\"row\"> <td><a ";
                                        if($banname<>'')
                                        {
                                        $output = $output.'style="color:#e56879 "';
                                        }
                                        $output = $output."href=\"?p=user&u=$founduser&s=datetime&o=desc&n=";
					if($displayStyle == 'all')
					{ 
					$output = $output.'all';
					} 
					else 
					{
					$output = $output.'0';
					}
					$output = $output."\">$founduser : $counttimes games.</a></td></tr>";
					print $output;
				}
				if($counttimes==false){ print "<tr class=\"rowuh\"> <td>Sorry no users named ".$username." have played any DotA games on ".$botName."</td></tr>";}
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
				$headerString = "Location: ?p=user&u=".$founduser."&s=datetime&o=desc&n=";
				if($displayStyle == 'all')
				{ 
				$headerString = $headerString.'all';
				} 
				else 
				{
				$headerString = $headerString.'0';
				}
				header($headerString);
			}?>
				<div class="header" id="header">
					<table width=1016px>
						<tr>
							<td align="center" >
								<h2> List of users matching <?php print $username; ?> on <?php print $botName;?>:</h2>
							</td>
						</tr>
						<tr height=25px></tr>
					</table>
				</div>
				<div class="pageholder" id="pageholder">
					<div id="theader">
					</div>
					<div id="datawrapper">
						<table class="table" id="data" width=1016px>
				<?php
				$counttimes = false;
				$result = mysql_query($sql);
				while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
				{
					$founduser=$row["name"];
					$counttimes=$row["counttimes"];
                                        $banname=$row["banname"];
                                        $output = "<tr class=\"row\"> <td><a ";
                                        if($banname<>'')
                                        {
                                        $output = $output.'style="color:#e56879 "';
                                        }
                                        $output = $output."href=\"?p=user&u=$founduser&s=datetime&o=desc&n=";
					if($displayStyle == 'all')
					{ 
					$output = $output.'all';
					} 
					else 
					{
					$output = $output.'0';
					}
					$output = $output."\">$founduser : $counttimes games.</a></td></tr>";
					print $output;
					
				}
				if($counttimes==false){ print "<tr class=\"rowuh\"> <td> Sorry no users found matching that criteria.</td></tr>";}
		
		}
		
		?>
					</table>
				</div>
			</div>
			<div id="footer" class="footer">
				<h5> Found <? print $foundCount; ?> matches</h5>
			</div>
<?php
}
else
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
	$kills="0";
	$death="0";
	$assists="0";
	$creepkills="0";
	$creepdenies="0";
	$neutralkills="0";
	$towerkills="0";
	$raxkills="0";
	$courierkills="0";
	$name="0";
	$totgames="0";
if($dbType == 'sqlite')
{
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
	$sql = "SELECT MIN(datetime), MIN(loadingtime), MAX(loadingtime), AVG(loadingtime), MIN(`left`), MAX(`left`), AVG(`left`), SUM(`left`) FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username' AND winner!=0";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$firstgame=$row["datetime"];
		$minLoading=millisecondsToTime($row["MIN(loadingtime)"]);
		$maxLoading=millisecondsToTime($row["MAX(loadingtime)"]);
		$avgLoading=millisecondsToTime($row["AVG(loadingtime)"]);
	        $minDuration=secondsToTime($row["MIN(`left`)"]);
        	$maxDuration=secondsToTime($row["MAX(`left`)"]);
	        $avgDuration=secondsToTime($row["AVG(`left`)"]);
	        $totalDuration=secondsToTime($row["SUM(`left`)"]);
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
		$sql = "select ($scoreFormula) as score from(select *, (kills/deaths) as killdeathratio from (select gp.name as name,gp.gameid as gameid, gp.colour as colour, avg(dp.courierkills) as courierkills, avg(dp.raxkills) as raxkills,
		avg(dp.towerkills) as towerkills, avg(dp.assists) as assists, avg(dp.creepdenies) as creepdenies, avg(dp.creepkills) as creepkills,
		avg(dp.neutralkills) as neutralkills, avg(dp.deaths) as deaths, avg(dp.kills) as kills,
		count(*) as totgames, SUM(case when(((dg.winner = 1 and dp.newcolour < 6) or (dg.winner = 2 and dp.newcolour > 6)) AND gp.`left`/ga.duration >= 0.8) then 1 else 0 end) as wins, SUM(case when(((dg.winner = 2 and dp.newcolour < 6) or (dg.winner = 1 and dp.newcolour > 6)) AND gp.`left`/ga.duration >= 0.8) then 1 else 0 end) as losses
		from gameplayers as gp LEFT JOIN dotagames as dg ON gp.gameid = dg.gameid LEFT JOIN games as ga ON ga.id = dg.gameid LEFT JOIN 
		dotaplayers as dp on dp.gameid = dg.gameid and gp.colour = dp.colour where dg.winner <> 0 and gp.name = '$username') as h) as i";
	}
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$score=$row["score"];
	}
		
	$sql = "SELECT COUNT(a.id), SUM(kills), SUM(deaths), SUM(creepkills), SUM(creepdenies), SUM(assists), SUM(neutralkills), SUM(towerkills), SUM(raxkills), SUM(courierkills), name FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.newcolour = b.colour where name= '$username' group by name ORDER BY sum(kills)";
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
$sql = "SELECT MIN(datetime), MIN(loadingtime), MAX(loadingtime), AVG(loadingtime), MIN(`left`), MAX(`left`), AVG(`left`), SUM(`left`) FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour LEFT JOIN dotagames ON games.id=dotagames.gameid WHERE name='$username'";
$result = mysql_query($sql);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$firstgame=$row["datetime"];
	$minLoading=millisecondsToTime($row["MIN(loadingtime)"]);
	$maxLoading=millisecondsToTime($row["MAX(loadingtime)"]);
	$avgLoading=millisecondsToTime($row["AVG(loadingtime)"]);
	$minDuration=secondsToTime($row["MIN(`left`)"]);
	$maxDuration=secondsToTime($row["MAX(`left`)"]);
	$avgDuration=secondsToTime($row["AVG(`left`)"]);
	$totalDuration=secondsToTime($row["SUM(`left`)"]);
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
	$sql = "select ($scoreFormula) as score from(select *, (kills/deaths) as killdeathratio from (select gp.name as name,gp.gameid as gameid, gp.colour as colour, avg(dp.courierkills) as courierkills, avg(dp.raxkills) as raxkills,
		avg(dp.towerkills) as towerkills, avg(dp.assists) as assists, avg(dp.creepdenies) as creepdenies, avg(dp.creepkills) as creepkills,
		avg(dp.neutralkills) as neutralkills, avg(dp.deaths) as deaths, avg(dp.kills) as kills,
		count(*) as totgames SUM(case when(((dg.winner = 1 and dp.newcolour < 6) or (dg.winner = 2 and dp.newcolour > 6)) AND gp.`left`/ga.duration >= 0.8) then 1 else 0 end) as wins, SUM(case when(((dg.winner = 2 and dp.newcolour < 6) or (dg.winner = 1 and dp.newcolour > 6)) AND gp.`left`/ga.duration >= 0.8) then 1 else 0 end) as losses
		from gameplayers as gp LEFT JOIN dotagames as dg ON gp.gameid = dg.gameid LEFT JOIN games as ga ON ga.id = dg.gameid LEFT JOIN 
		dotaplayers as dp on dp.gameid = dg.gameid and gp.colour = dp.colour where dg.winner <> 0 and gp.name = '$username') as h) as i";
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
}
?>

<div class="header" id="header">
<table width=1016px>
  <tr class="rowuh" style="border-bottom: 1px solid #EBEBEB;">
	<td colspan=2>
	  <h2>User Statistics for: <?php print $username;?></h2>
	</td>
  </tr>
  <tr class="rowuh"> 
    <td colspan=2 align="left"><b>Loading Times(sec):</b>
      MAX: <?php print $maxLoading;?> | MIN: <?php print $minLoading;?> | AVG: <?php print $avgLoading;?>
    </td>
  </tr>
  <tr class="footerheadercell">
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
          <td><?php if($death == 0 and $kills == 0){ print '0';} else if($death == 0){print '1000';} else {print round(($kills/$death),2);}?></td>
		  
        </tr>
		<tr height=10px>
		</tr>
        <tr>
			<td>Games:</td>
          <td><?php print $totgames;?></td>		
		  <td>Wins/Losses:</td>
          <td><?php print $wins;?>/<?php print $losses;?></td>
		  
        </tr>
		<tr>
		  <td>Score:</td>
          <td><?php print ROUND($score, 2); ?></td>
		  <td>Win Percent:</td>
          <td><?php if($wins == 0 and $wins+$losses == 0){ print '0';} else if($wins+$losses == 0){print '1000';} else {print round($wins/($wins+$losses), 4)*100;}?></td>
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
          <td align=center>Kills</td>
          <td align=center >Deaths</td>
          <td align=center >Assists</td>
          <td align=center>Wins</td>
          <td align=center>Losses</td>
          <td align=center>Times Played</td>
        </tr>
        <tr> 
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostkillshero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostkillshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostdeathshero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostdeathshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostassistshero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostassistshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostwinshero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostwinshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostlosseshero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostlosseshero; ?>.gif" width="64" height="64"></a></td>
          <td align='center' width=10% scope=col ><a  href="?p=hero&hid=<?php print $mostplayedhero;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img src="img/heroes/<?php print $mostplayedhero; ?>.gif" width="64" height="64"></a></td>
        </tr>
        <tr> 
          <td align=center >(<?php print $mostkillscount;?>)</td>
          <td align=center >(<?php print $mostdeathscount;?>)</td>
          <td align=center >(<?php print $mostassistscount;?>)</td>
          <td align=center >(<?php print $mostwinscount;?>)</td>
          <td align=center >(<?php print $mostlossescount;?>)</td>
          <td align=center >(<?php print $mostplayedcount;?>)</td>
        </tr>
      </table></td>
  </tr>
</table>


<?php
 $sql = "SELECT count(*) as count FROM( select a.hero
 FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN dotagames AS c ON c.gameid = a.gameid 
 LEFT JOIN games AS d ON d.id = a.gameid LEFT JOIN originals as e ON a.hero = heroid where name= '$username' and description <> 'NULL') as t";

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

$pages = ceil($count/$userResultSize);
?>

	<table width=1016px>
		<tr class="rowuh" style="border-top: 1px solid #EBEBEB;">
			<td width=25%>
				<table class="rowuh" width = 235px style="float:left">
					<tr>
						<td>
						<?php
						if($offset == 'all')
						{
							print "Showing All Games";
						}
						else
						{
							print "<a href=\"?p=user&u=".$username."&s=".$sortcat."&o=".$order."&n=all\">Show All Games</a>";
						}
						?>
						</td>
					</tr>
				</table>
			</td>
			<td width=50%>
				<h3>Game History:</h3>
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
						$min = $offset*$userResultSize+1;
						$max = $offset*$userResultSize+$userResultSize;
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
						print "<td width=35px><a href=\"?p=user&u=".$username."&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
						}
					}
					print "<td width=35px><span style=\"color:#ddd;\">></span></td>";
				}
				else
				{
					if($offset > 0)
					{
						print "<td width=35px><a href=\"?p=user&u=".$username."&s=".$sortcat."&o=".$order."&n=".($offset-1)."\"><</a>";
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
									print "<td width=35px><a href=\"?p=user&u=".$username."&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
						}
						if($offset == 1)
						{
							print "<td width=35px><a href=\"?p=user&u=".$username."&s=".$sortcat."&o=".$order."&n=0\">1</a></td>";
							print "<td width=35px><span style=\"color:#ddd;\">2</span></td>";
							for($counter = 3; $counter < 6; $counter++)
							{
								if($counter-1 < $pages)
								{
								print "<td width=35px><a href=\"?p=user&u=".$username."&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
								
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
								print "<td width=35px><a href=\"?p=user&u=".$username."&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
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
									print "<td width=35px><a href=\"?p=user&u=".$username."&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
							print "<td width=35px><span style=\"color:#ddd;\">".($offset+1)."</span>";
							print "<td width=35px><a href=\"?p=user&u=".$username."&s=".$sortcat."&o=".$order."&n=".($offset+1)."\">".($offset+2)."</a></td>";
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
								print "<td width=35px><a href=\"?p=user&u=".$username."&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
							}
						}
					}
					if(($offset+1)*$userResultSize < $count)
					{
						print "<td width=35px><a href=\"?p=user&u=".$username."&s=".$sortcat."&o=".$order."&n=".($offset+1)."\">></a></td>";
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
  //Date and Time
	if($sortcat == "datetime")
	{
		if($order == "asc")
		{
			print("<td width=150px><a href=\"?p=user&u=$username&s=datetime&o=desc&n=all\">Date and Time</a></td>");
		}
		else
		{
			print("<td width=150px><a href=\"?p=user&u=$username&s=datetime&o=asc&n=all\">Date and Time</a></td>");
		}
	}
	else
	{
		print("<td width=150px><a href=\"?p=user&u=$username&s=datetime&o=desc&n=all\">Date and Time</a></td>");
	}
	//Game Name
	if($sortcat == "gamename")
	{
		if($order == "asc")
		{
			print("<td width=175px><a href=\"?p=user&u=$username&s=gamename&o=desc&n=all\">Game Name</a></td>");
		}
		else
		{
			print("<td width=175px><a href=\"?p=user&u=$username&s=gamename&o=asc&n=all\">Game Name</a></td>");
		}
	}
	else
	{
		print("<td width=175px><a href=\"?p=user&u=$username&s=gamename&o=asc&n=all\">Game Name</a></td>");
	}
	//Game Type
	if($sortcat == "type")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=type&o=desc&n=all\">Type</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=type&o=asc&n=all\">Type</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=type&o=desc&n=all\">Type</a></td>");
	}
	//Hero
	if($sortcat == "description")
	{
		if($order == "asc")
		{
			print("<td width=180px><a href=\"?p=user&u=$username&s=description&o=desc&n=all\">Hero Played</a></td>");
		}
		else
		{
			print("<td width=180px><a href=\"?p=user&u=$username&s=description&o=asc&n=all\">Hero Played</a></td>");
		}
	}
	else
	{
		print("<td width=180px><a href=\"?p=user&u=$username&s=description&o=asc&n=all\">Hero Played</a></td>");
	}
	//Kills
	if($sortcat == "kills")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=kills&o=desc&n=all\">Kills</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=kills&o=asc&n=all\">Kills</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=kills&o=desc&n=all\">Kills</a></td>");
	}
	
	//Deaths
	if($sortcat == "deaths")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=deaths&o=desc&n=all\">Deaths</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=deaths&o=asc&n=all\">Deaths</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=deaths&o=desc&n=all\">Deaths</a></td>");
	}
	
	//Assists
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=assists&o=desc&n=all\">Assists</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=assists&o=asc&n=all\">Assists</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=assists&o=desc&n=all\">Assists</a></td>");
	}
	//KDRatio
	if($sortcat == "kdratio")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=kdratio&o=desc&n=all\">K/D Ratio</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=kdratio&o=asc&n=all\">K/D Ratio</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=kdratio&o=desc&n=all\">K/D Ratio</a></td>");
	}
	//Creep Kills
	if($sortcat == "creepkills")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=creepkills&o=desc&n=all\">Creep<br>Kills</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=creepkills&o=asc&n=all\">Creep<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=creepkills&o=desc&n=all\">Creep<br>Kills</a></td>");
	}
	//Creep Denies
	if($sortcat == "creepdenies")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=creepdenies&o=desc&n=all\">Creep<br>Denies</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=creepdenies&o=asc&n=all\">Creep<br>Denies</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=creepdenies&o=desc&n=all\">Creep<br>Denies</a></td>");
	}
	//Neutral Kills
	if($sortcat == "neutralkills")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=neutralkills&o=desc&n=all\">Neutral<br>Kills</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=neutralkills&o=asc&n=all\">Neutral<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=neutralkills&o=desc&n=all\">Neutral<br>Kills</a></td>");
	}
	//Outcome
	if($sortcat == "outcome")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=outcome&o=desc&n=all\">Result</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=outcome&o=asc&n=all\">Result</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=outcome&o=desc&n=all\">Result</a></td>");
	}
 }
 else
 {
	 //Date and Time
	if($sortcat == "datetime")
	{
		if($order == "asc")
		{
			print("<td width=150px><a href=\"?p=user&u=$username&s=datetime&o=desc&n=0\">Date and Time</a></td>");
		}
		else
		{
			print("<td width=150px><a href=\"?p=user&u=$username&s=datetime&o=asc&n=0\">Date and Time</a></td>");
		}
	}
	else
	{
		print("<td width=150px><a href=\"?p=user&u=$username&s=datetime&o=desc&n=0\">Date and Time</a></td>");
	}
	//Game Name
	if($sortcat == "gamename")
	{
		if($order == "asc")
		{
			print("<td width=175px><a href=\"?p=user&u=$username&s=gamename&o=desc&n=0\">Game Name</a></td>");
		}
		else
		{
			print("<td width=175px><a href=\"?p=user&u=$username&s=gamename&o=asc&n=0\">Game Name</a></td>");
		}
	}
	else
	{
		print("<td width=175px><a href=\"?p=user&u=$username&s=gamename&o=asc&n=0\">Game Name</a></td>");
	}
	//Game Type
	if($sortcat == "type")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=type&o=desc&n=0\">Type</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=type&o=asc&n=0\">Type</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=type&o=desc&n=0\">Type</a></td>");
	}
	//Hero
	if($sortcat == "description")
	{
		if($order == "asc")
		{
			print("<td width=180px><a href=\"?p=user&u=$username&s=description&o=desc&n=0\">Hero Played</a></td>");
		}
		else
		{
			print("<td width=180px><a href=\"?p=user&u=$username&s=description&o=asc&n=0\">Hero Played</a></td>");
		}
	}
	else
	{
		print("<td width=180px><a href=\"?p=user&u=$username&s=description&o=asc&n=0\">Hero Played</a></td>");
	}
	//Kills
	if($sortcat == "kills")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=kills&o=desc&n=0\">Kills</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=kills&o=asc&n=0\">Kills</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=kills&o=desc&n=0\">Kills</a></td>");
	}
	
	//Deaths
	if($sortcat == "deaths")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=deaths&o=desc&n=0\">Deaths</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=deaths&o=asc&n=0\">Deaths</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=deaths&o=desc&n=0\">Deaths</a></td>");
	}
	
	//Assists
	if($sortcat == "assists")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=assists&o=desc&n=0\">Assists</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=assists&o=asc&n=0\">Assists</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=assists&o=desc&n=0\">Assists</a></td>");
	}
	//KDRatio
	if($sortcat == "kdratio")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=kdratio&o=desc&n=0\">K/D Ratio</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=kdratio&o=asc&n=0\">K/D Ratio</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=kdratio&o=desc&n=0\">K/D Ratio</a></td>");
	}
	//Creep Kills
	if($sortcat == "creepkills")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=creepkills&o=desc&n=0\">Creep<br>Kills</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=creepkills&o=asc&n=0\">Creep<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=creepkills&o=desc&n=0\">Creep<br>Kills</a></td>");
	}
	//Creep Denies
	if($sortcat == "creepdenies")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=creepdenies&o=desc&n=0\">Creep<br>Denies</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=creepdenies&o=asc&n=0\">Creep<br>Denies</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=creepdenies&o=desc&n=0\">Creep<br>Denies</a></td>");
	}
	//Neutral Kills
	if($sortcat == "neutralkills")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=neutralkills&o=desc&n=0\">Neutral<br>Kills</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=neutralkills&o=asc&n=0\">Neutral<br>Kills</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=neutralkills&o=desc&n=0\">Neutral<br>Kills</a></td>");
	}
	//Outcome
	if($sortcat == "outcome")
	{
		if($order == "asc")
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=outcome&o=desc&n=0\">Result</a></td>");
		}
		else
		{
			print("<td width=55px><a href=\"?p=user&u=$username&s=outcome&o=asc&n=0\">Result</a></td>");
		}
	}
	else
	{
		print("<td width=55px><a href=\"?p=user&u=$username&s=outcome&o=desc&n=0\">Result</a></td>");
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

 $sql = "SELECT winner, a.gameid as id, newcolour, datetime, gamename, description, hero, kills, deaths, assists, creepkills, creepdenies, neutralkills, name, CASE when(gamestate = '17') then 'PRIV' else 'PUB' end as type,
 CASE WHEN (deaths = 0 and kills = 0) THEN 0 WHEN (deaths = 0) then 1000 ELSE (kills*1.0/deaths) end as kdratio,
 CASE when ((winner=1 and newcolour < 6) or (winner=2 and newcolour > 5)) AND b.`left`/d.duration >= 0.8  then 'WON' when ((winner=2 and newcolour < 6) or (winner=1 and newcolour > 5)) AND b.`left`/d.duration >= 0.8  then 'LOST' when  winner=0 then 'DRAW' else '$notCompleted' end as outcome 
 FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour LEFT JOIN dotagames AS c ON c.gameid = a.gameid 
 LEFT JOIN games AS d ON d.id = a.gameid LEFT JOIN originals as e ON a.hero = heroid where name= '$username' and description <> 'NULL' ORDER BY $sortcat $order, d.id DESC";

	if($offset!='all')
	{
	$sql = $sql." LIMIT ".$userResultSize*$offset.", $userResultSize";
	}
 if($dbType == 'sqlite')
 {
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
	<td width=150px><?php print $gametime;?></td>
    <td width=175px><a href="?p=gameinfo&gid=<?php print $gameid; ?>" target="_self"><?php print $gamename;?></a></td>
    <td width=55px><?php print $type;?></td>
	<td width=30px><a  href="?p=hero&hid=<?php print$hid;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img width="28px" height="28px" src=./img/heroes/<?php print $hid; ?>.gif></a></td>
	<td width=150px><a  href="?p=hero&hid=<?php print $hid;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $hero;?></a></td>
    <td width=55px><?php print $kills;?></td>
    <td width=55px><?php print $death;?></td>
    <td width=55px><?php print $assists;?></td>
    <td width=55px><?php print round($kdratio, 2);?></td>
	<td width=55px><?php print $creepkills;?></td>
    <td width=55px><?php print $creepdenies;?></td>
	<td width=55px><?php print $neutralkills;?></td>
	<td width=55px> <span <?php if($outcome == 'LOST'){print 'style="color:#e56879"';}elseif($outcome == 'WON'){print 'style="color:#86E573"';} else{print 'style="color:#ebeb7d"';} ?>><?php print $outcome;?></span></td>
</tr>	
	
 <?php 
	}
}
else
{
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
	<td width=150px><?php print $gametime;?></td>
    <td width=175px><a href="?p=gameinfo&gid=<?php print $gameid; ?>" target="_self"><?php print $gamename;?></a></td>
    <td width=55px><?php print $type;?></td>
	<td width=30px><a  href="?p=hero&hid=<?php print$hid;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><img width="28px" height="28px" src=./img/heroes/<?php print $hid; ?>.gif></a></td>
	<td width=150px><a  href="?p=hero&hid=<?php print $hid;?>&s=kdratio&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $hero;?></a></td>
    <td width=55px><?php print $kills;?></td>
    <td width=55px><?php print $death;?></td>
    <td width=55px><?php print $assists;?></td>
    <td width=55px><?php print round($kdratio, 2);?></td>
	<td width=55px><?php print $creepkills;?></td>
    <td width=55px><?php print $creepdenies;?></td>
	<td width=55px><?php print $neutralkills;?></td>
	<td width=55px> <span <?php if($outcome == 'LOST'){print 'style="color:#e56879"';}elseif($outcome == 'WON'){print 'style="color:#86E573"';} else{print 'style="color:#ebeb7d"';} ?>><?php print $outcome;?></span></td>
</tr>	
	
	<?php
	}
}
}
	?>
</table>
</div>
</div>
<div id="footer" class="footer">
  <h5> Game Duration(hh:mm:ss):
    MAX: <?php print $maxDuration;?> | MIN: <?php print $minDuration;?> | AVG: <?php print $avgDuration;?> | TOTAL: <?php print $totalDuration;?>
  </h5>
</div>
