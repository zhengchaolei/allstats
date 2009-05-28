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

$gid=$_GET["gid"];
require_once("functions.php");
require_once("config.php");
require("reshine.php");

	$firstline=true;
	$scourge=true;
	$sql = "SELECT winner, creatorname, duration, a.gameid, b.colour, newcolour, datetime, gamename, 
	hero, kills, deaths, assists, creepkills, creepdenies, neutralkills, towerkills, gold, 
	item1, item2, item3, item4, item5, item6, leftreason, b.left, name 
	FROM dotaplayers AS a LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
	LEFT JOIN dotagames AS c ON c.gameid = a.gameid LEFT JOIN games AS d ON d.id = a.gameid where a.gameid='$gid' order by newcolour";

if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$creatorname=$row["creatorname"];
		$duration=$row["duration"];
		$gametime=$row["datetime"];
		$kills=$row["kills"];
		$deaths=$row["deaths"];
		$assists=$row["assists"];
		$creepkills=$row["creepkills"];
		$creepdenies=$row["creepdenies"];
		$neutralkills=$row["neutralkills"];
		$towerkills=$row["towerkills"];
		$gold=$row["gold"];
		$item1=$row["item1"];
		$item2=$row["item2"];
		$item3=$row["item3"];
		$item4=$row["item4"];
		$item5=$row["item5"];
		$item6=$row["item6"];
		if(empty($item1) || $item1=="\0\0\0\0") $item1="empty";
		if(empty($item2) || $item2=="\0\0\0\0") $item2="empty";
		if(empty($item3) || $item3=="\0\0\0\0") $item3="empty";
		if(empty($item4) || $item4=="\0\0\0\0") $item4="empty";
		if(empty($item5) || $item5=="\0\0\0\0") $item5="empty";
		if(empty($item6) || $item6=="\0\0\0\0") $item6="empty";
		
		
		
		$left=$row["left"];
		$leftreason=$row["leftreason"];
		$gamename=$row["gamename"];
		$hero=$row["hero"];
		
		$hero=checkIfAliasSQLite($hero, $dbType, $dbHandle);
		$name=$row["name"];
	 
		$newcolour=$row["newcolour"];
		$win=$row["winner"];
		$gameid=$row["gameid"]; 
		
		if($firstline){
		$firstline = false;
		//only shows the header info once.!
		// where is the replay file
		$gametimenew = str_ireplace(":","-",$gametime);
		//$gametimenew = str_ireplace(" ","%20",$gametimenew);
		$gametimenew = substr($gametimenew,0,16);

		//REPLAY NAME HANDLING CODE
		$replayurl="replays/GHost++ ".$gametimenew." ".str_ireplace("\\","_", str_ireplace("/","_",$gamename))." (".replayDuration($duration).").w3g";
		if(!file_exists($replayurl))
		{
			$replayurl="replays/GHost++ ".$gametimenew." ".str_ireplace("\\","_", str_ireplace("/","_",$gamename)).".w3g";
		}
		$replayurl = str_ireplace(" ","%20",$replayurl);
		// GHost++ 2009-03-16 20-16 dota 4v4 apem eu.w3g
		?>
		
		<table class="table" id="introtable">
		<tr>
		<td colspan=6>
		<h2>Game Information for: <?php print $gamename; ?></h2>
		</td>
		</tr>
		<tr class="tableheader">
			<td>
			  Game Name:&nbsp; <?php print $gamename; ?>
			</td>
			<td>
			  Date: &nbsp; <?php print $gametime; ?> 
			<td>
			<td>
			  Creator: &nbsp;<?php print $creatorname; ?>
			</td>
			<td>
			  Duration: &nbsp;<?php print secondsToTime($duration); ?>
			</td>
			<td>
			  <? 
			  //only show the link if the replay feature is enabled in config.php
			  if($enablereplayfeature){ ?><a href=<?php print $replayurl;?>>Download replay</a>
			  <? } //end of enablefeature ?> 
			</td>
		</tr>
		</table>
		<table class="table" id="theader">
			<tr class="tableheader">
				<td>Player</td>
				<td width=5%>Hero</td>
				<td width=5%>Kills</td>
				<td width=5%>Deaths</td>
				<td width=5%>Assists</td>
				<td width=5%>Creep Kills</td>
				<td width=5%>Creep Denies</td>
				<td width=5%>Neutral Kills</td>
				<td width=5%>Towers</td> 
				<td width=5%>Gold</td>
				<td width=220px>Items</td>
				<td width=5%>Left At</td>
				<td width=20%>Reason</td>		
			</tr>
			<tr>
				<td height=10px colspan=14></td>
			</tr>
		</table>
		
	<div id="datawrapper">
		<table class="table" id="data">
			<tr class="tableheader">
				<td align="center" colspan=13>
					SENTINEL - <?php if($win==1) print "Winner!"; else print "Loser!";?>
				</td>
			</tr>
			<tr>
				<td height=10px colspan=12></td>
			</tr>
		<?php
		}
		if($scourge&&$newcolour>5){
		$scourge=false;
		?>
			<tr>
				<td height=10px colspan=12></td>
			</tr>
			<tr class="tableheader">
				<td align="center" colspan=13>
				SCOURGE - <?php if($win==2) print "Winner!"; else print "Loser!";?> 
				</td>
			</tr>
			<tr>
				<td height=10px colspan=12></td>
			</tr>
		<?php
		}
		 if($name!=""){
		?>		
				
				
		<tr class="row">
			<td>
			<a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc" target="_self"><b><?php print $name; ?></b></a>
			</td>
			<td width=5%>
			<?php
			if(empty($hero))
			print "<img width=\"32px\" height=\"32px\" src=./img/heroes/blank.gif>";
			else
			print "<a  href=\"?p=hero&hid=".$hero."&s=kdratio&o=desc\"><img width=\"32px\" height=\"32px\" src=./img/heroes/".$hero.".gif></a>";
			?>
			</td>
			<td width=5%><?php print $kills; ?></td>
			<td width=5%><?php print $deaths; ?></td>
			<td width=5%><?php print $assists; ?></td>
			<td width=5%><?php print $creepkills; ?></td>
			<td width=5%><?php print $creepdenies; ?></td>	
			<td width=5%><?php print $neutralkills; ?></td>
			<td width=5%><?php print $towerkills; ?></td>
			<td width=5%><?php print $gold; ?></td>
					
			<td align="center" width="220"> 
				<img width="32px" height="32px" src=./img/items/<?php print $item1; ?>.gif>
				<img width="32px" height="32px" src=./img/items/<?php print $item2; ?>.gif>
				<img width="32px" height="32px" src=./img/items/<?php print $item3; ?>.gif>
				<img width="32px" height="32px" src=./img/items/<?php print $item4; ?>.gif>
				<img width="32px" height="32px" src=./img/items/<?php print $item5; ?>.gif>
				<img width="32px" height="32px" src=./img/items/<?php print $item6; ?>.gif>
			</td>
			<td width=5%><?php print secondsToTime($left); ?></td>
			<td width=20%><?php print $leftreason; ?></td>
		</tr>
	<?php
		}
	}
}
else
{
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

		$creatorname=$row["creatorname"];
		$duration=$row["duration"];
		$gametime=$row["datetime"];
		$kills=$row["kills"];
		$deaths=$row["deaths"];
		$assists=$row["assists"];
		$creepkills=$row["creepkills"];
		$creepdenies=$row["creepdenies"];
		$neutralkills=$row["neutralkills"];
		$towerkills=$row["towerkills"];
		$gold=$row["gold"];
		$item1=$row["item1"];
		$item2=$row["item2"];
		$item3=$row["item3"];
		$item4=$row["item4"];
		$item5=$row["item5"];
		$item6=$row["item6"];
		if(empty($item1) || $item1=="\0\0\0\0") $item1="empty";
		if(empty($item2) || $item2=="\0\0\0\0") $item2="empty";
		if(empty($item3) || $item3=="\0\0\0\0") $item3="empty";
		if(empty($item4) || $item4=="\0\0\0\0") $item4="empty";
		if(empty($item5) || $item5=="\0\0\0\0") $item5="empty";
		if(empty($item6) || $item6=="\0\0\0\0") $item6="empty";
		
		
		
		$left=$row["left"];
		$leftreason=$row["leftreason"];
		$gamename=$row["gamename"];
		$hero=$row["hero"];
		
		$hero=checkIfAliasMySQL($hero);
		$name=$row["name"];
	 
		$newcolour=$row["newcolour"];
		$win=$row["winner"];
		$gameid=$row["gameid"]; 
		
		if($firstline){
		$firstline = false;
		//only shows the header info once.!
		// where is the replay file
		$gametimenew = str_ireplace(":","-",$gametime);
		//$gametimenew = str_ireplace(" ","%20",$gametimenew);
		$gametimenew = substr($gametimenew,0,16);

		//REPLAY NAME HANDLING CODE
		$replayurl="replays/GHost++ ".$gametimenew." ".str_ireplace("\\","_", str_ireplace("/","_",$gamename))." (".replayDuration($duration).").w3g";
		if(!file_exists($replayurl))
		{
			$replayurl="replays/GHost++ ".$gametimenew." ".str_ireplace("\\","_", str_ireplace("/","_",$gamename)).".w3g";
		}
		$replayurl = str_ireplace(" ","%20",$replayurl);
		
		// GHost++ 2009-03-16 20-16 dota 4v4 apem eu.w3g
		?>
		
		<table class="table" id="introtable">
		<tr>
		<td colspan=6>
		<h2>Game Information for: <?php print $gamename; ?></h2>
		</td>
		</tr>
		<tr class="tableheader">
			<td>
			  Game Name:&nbsp; <?php print $gamename; ?>
			</td>
			<td>
			  Date: &nbsp; <?php print $gametime; ?> 
			<td>
			<td>
			  Creator: &nbsp;<?php print $creatorname; ?>
			</td>
			<td>
			  Duration: &nbsp;<?php print secondsToTime($duration); ?>
			</td>
			<td>
			  <? 
			  //only show the link if the replay feature is enabled in config.php
			  if($enablereplayfeature){ ?><a href=<?php print $replayurl;?>>Download replay</a>
			  <? } //end of enablefeature ?> 
			</td>
		</tr>
		</table>
		<table class="table" id="theader">
			<tr class="tableheader">
				<td>Player</td>
				<td width=5%>Hero</td>
				<td width=5%>Kills</td>
				<td width=5%>Deaths</td>
				<td width=5%>Assists</td>
				<td width=5%>Creep Kills</td>
				<td width=5%>Creep Denies</td>
				<td width=5%>Neutral Kills</td>
				<td width=5%>Towers</td> 
				<td width=5%>Gold</td>
				<td width=220px>Items</td>
				<td width=5%>Left At</td>
				<td width=20%>Reason</td>		
			</tr>
			<tr>
				<td height=10px colspan=14></td>
			</tr>
		</table>
		
	<div id="datawrapper">
		<table class="table" id="data">
			<tr class="tableheader">
				<td align="center" colspan=13>
					SENTINEL - <?php if($win==1) print "Winner!"; else print "Loser!";?>
				</td>
			</tr>
			<tr>
				<td height=10px colspan=12></td>
			</tr>
		<?php
		}
		if($scourge&&$newcolour>5){
		$scourge=false;
		?>
			<tr>
				<td height=10px colspan=12></td>
			</tr>
			<tr class="tableheader">
				<td align="center" colspan=13>
				SCOURGE - <?php if($win==2) print "Winner!"; else print "Loser!";?> 
				</td>
			</tr>
			<tr>
				<td height=10px colspan=12></td>
			</tr>
		<?php
		}
		 if($name!=""){
		?>		
				
				
		<tr class="row">
			<td>
			<a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc" target="_self"><b><?php print $name; ?></b></a>
			</td>
			<td width=5%>
			<?php
			if(empty($hero))
			print "<img width=\"32px\" height=\"32px\" src=./img/heroes/blank.gif>";
			else
			print "<a  href=\"?p=hero&hid=".$hero."&s=kdratio&o=desc\"><img width=\"32px\" height=\"32px\" src=./img/heroes/".$hero.".gif></a>";
			?>
			</td>
			<td width=5%><?php print $kills; ?></td>
			<td width=5%><?php print $deaths; ?></td>
			<td width=5%><?php print $assists; ?></td>
			<td width=5%><?php print $creepkills; ?></td>
			<td width=5%><?php print $creepdenies; ?></td>	
			<td width=5%><?php print $neutralkills; ?></td>
			<td width=5%><?php print $towerkills; ?></td>
			<td width=5%><?php print $gold; ?></td>
					
			<td align="center" width="220"> 
				<img width="32px" height="32px" src=./img/items/<?php print $item1; ?>.gif>
				<img width="32px" height="32px" src=./img/items/<?php print $item2; ?>.gif>
				<img width="32px" height="32px" src=./img/items/<?php print $item3; ?>.gif>
				<img width="32px" height="32px" src=./img/items/<?php print $item4; ?>.gif>
				<img width="32px" height="32px" src=./img/items/<?php print $item5; ?>.gif>
				<img width="32px" height="32px" src=./img/items/<?php print $item6; ?>.gif>
			</td>
			<td width=5%><?php print secondsToTime($left); ?></td>
			<td width=20%><?php print $leftreason; ?></td>
		</tr>
	<?php
		}
	}
}
?>
</table>
</div>