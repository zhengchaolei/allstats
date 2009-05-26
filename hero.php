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


$heroid=$_GET["hid"];
require_once("functions.php");
require_once("config.php");
//find if this is an alias
$aliasheroes="";
if($dbType == 'sqlite')
{
	$heroid=checkIfAliasSQLite($heroid, $dbType, $dbHandle);
	$sql = "select heroid from originals where original='$heroid'";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$aliasheroes="$aliasheroes or hero='".$row["heroid"]."'";
	}
}
else
{
	$heroid=checkIfAliasMySQL($heroid);
	$result = mysql_query("select heroid from originals where original='$heroid'");
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
	{
		$aliasheroes="$aliasheroes or hero='".$row["heroid"]."'";
	}
}

?>

<table class="table" id="theader">
	<tr class="rowuh"> 
	<td colspan=12 align="center" ><h2><?php print heroname($heroid); ?> Statistics:</h2> 
    </td>
  </tr>
  <tr>
	<td class="rowuh"colspan=1></td>
	<td class="tableheader" colspan=2>Total:</td>
	<td class="tableheader" colspan=9>Average Per Game:</td>
  </tr>
  <tr class="tableheader"> 
    <td>Player Name</td>
	<td width=7%>Games:</td>
    <td width=7%>K/D Ratio</td>
    <td width=7%>Kills</td>
    <td width=7%>Deaths</td>
    <td width=7%>Assists</td>
	<td width=7%>Creep Kills</td>
	<td width=7%>Creep Denies</td>
	<td width=7%>Neutral Kills</td>
	<td width=7%>Tower Kills</td>
	<td width=7%>Rax Kills</td>
	<td width=7%>Courier Kills</td>
  </tr>
</table>
<div id="datawrapper">
	<table class="table" id="data">

<?php
$sql = "select *,(sumkills/sumdeaths) as killdeathratio from 
(select hero, count(*) as totgames, sum(kills) as sumkills, sum(deaths) as sumdeaths, sum(assists), 
sum(creepkills), sum(creepdenies), sum(neutralkills), sum(towerkills), sum(raxkills), sum(courierkills),
name from (SELECT hero, name, kills, deaths, assists, creepkills, creepdenies, neutralkills, towerkills, raxkills, courierkills 
FROM gameplayers LEFT JOIN games ON games.id=gameplayers.gameid LEFT JOIN dotaplayers ON dotaplayers.gameid=games.id AND dotaplayers.colour=gameplayers.colour 
LEFT JOIN dotagames ON games.id=dotagames.gameid where winner<>0  order by kills desc)
as tb1 where hero='$heroid' $aliasheroes group by name) as tb3 order by killdeathratio desc";
if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$totgames=$row["totgames"];
		$killdeathratio=$row["killdeathratio"];
		$kills=$row["sumkills"];
		$deaths=$row["sumdeaths"];
		$assists=$row["sum(assists)"];
		$creepkills=$row["sum(creepkills)"];
		$creepdenies=$row["sum(creepdenies)"];
		$neutralkills=$row["sum(neutralkills)"];
		$towerkills=$row["sum(towerkills)"];
		$raxkills=$row["sum(raxkills)"];
		$courierkills=$row["sum(courierkills)"];
		$name=$row["name"]; 
		
		if(!$killdeathratio){$killdeathratio=$kills;}
		$killdeathratio= substr($killdeathratio,0,4);
	?>

		 <tr class="row"> 
			<td><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc"><?php print $name; ?></a></td>
			<td width=7%> <?php print $totgames;?></td>
			<td width=7%> <?php print $killdeathratio;?></td>
			<td width=7%> <?php print ROUND($kills/$totgames, 1);?></td>
			<td width=7%><?php print ROUND($deaths/$totgames, 1);?></td>
			<td width=7%><?php print ROUND($assists/$totgames, 1);?></td>
			<td width=7%><?php print ROUND($creepkills/$totgames, 1);?></td>
			<td width=7%><?php print ROUND($creepdenies/$totgames, 1);?></td>
			<td width=7%><?php print ROUND($neutralkills/$totgames, 1);?></td>
			<td width=7%><?php print ROUND($towerkills/$totgames, 1);?></td>
			<td width=7%><?php print ROUND($raxkills/$totgames, 1);?></td>
			<td width=7%><?php print ROUND($courierkills/$totgames, 1);?></td>
		  </tr>
<?php 
	}
}
else
{	
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$totgames=$row["totgames"];
    $killdeathratio=$row["killdeathratio"];
    $kills=$row["sumkills"];
	$deaths=$row["sumdeaths"];
    $assists=$row["sum(assists)"];
	$creepkills=$row["sum(creepkills)"];
	$creepdenies=$row["sum(creepdenies)"];
	$neutralkills=$row["sum(neutralkills)"];
	$towerkills=$row["sum(towerkills)"];
	$raxkills=$row["sum(raxkills)"];
	$courierkills=$row["sum(courierkills)"];
	$name=$row["name"]; 
	
	if(!$killdeathratio){$killdeathratio=$kills;}
	$killdeathratio= substr($killdeathratio,0,4);
?>

 <tr class="row"> 
    <td><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc"><?php print $name; ?></a></td>
	<td width=7%> <?php print $totgames;?></td>
    <td width=7%> <?php print $killdeathratio;?></td>
    <td width=7%> <?php print ROUND($kills/$totgames, 1);?></td>
    <td width=7%><?php print ROUND($deaths/$totgames, 1);?></td>
    <td width=7%><?php print ROUND($assists/$totgames, 1);?></td>
	<td width=7%><?php print ROUND($creepkills/$totgames, 1);?></td>
	<td width=7%><?php print ROUND($creepdenies/$totgames, 1);?></td>
	<td width=7%><?php print ROUND($neutralkills/$totgames, 1);?></td>
	<td width=7%><?php print ROUND($towerkills/$totgames, 1);?></td>
	<td width=7%><?php print ROUND($raxkills/$totgames, 1);?></td>
	<td width=7%><?php print ROUND($courierkills/$totgames, 1);?></td>
  </tr>
<?php 
	}
	mysql_free_result($result);
}
 ?>
</table>
</div>
<table class="table" id="introtable">
  <tr>
    <td width=48px align="left" class="tableheader"> <img src=./img/heroes/<?php print $heroid ?>.gif alt="w<?php print heroname($heroid);?>" width=48px height=48px></td>
	<td class="tableheader"><h3><?php print heroName($heroid);?> Information</td>
	<td width=48px align="left" class="tableheader"> <img src=./img/heroes/<?php print $heroid ?>.gif alt="w<?php print heroname($heroid);?>" width=48px height=48px></td>
  </tr>
  <tr class="rowuh"> 
    <td align="center" colspan=3><?php print heroDescription($heroid);?></td>
  </tr>
  <tr class="rowuh" height=10px>
  </tr>
</table>
