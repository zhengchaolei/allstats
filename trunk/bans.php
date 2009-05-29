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
}
else
{
	$sortcat=mysql_real_escape_string($_GET["s"]);
	$order=mysql_real_escape_string($_GET["o"]);
}

$sql = "SELECT COUNT( DISTINCT id ) as totbanned from bans";
if($dbType == 'sqlite')
{
	
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$totbanned=$row["totbanned"];
	?>
	<table class="table" id="theader">

		<tr>
		<td colspan=5>
		<h2>Banned Players:</h2>
		</td>
		</tr>

	<tr>
		<td colspan=5>
		<h5>Number of Banned Players: <?php print $totbanned; ?> </h5>
		</td>
	</tr>
	<?php
	}
}
else
{
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$totbanned=$row["totbanned"];
	?>
	<table class="table" id="theader">

		<tr>
		<td colspan=5>
		<h2>Banned Players:</h2>
		</td>
		</tr>

	<tr>
		<td colspan=5>
		<h5>Number of Banned Players: <?php print $totbanned; ?> </h5>
		</td>
	</tr>
	<?php
	}
	mysql_free_result($result);
}
?>

<tr class="tableheader">
<?php
	//Name
	if($sortcat == "name")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=bans&s=name&o=desc\">Name</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=bans&s=name&o=asc\">Name</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=bans&s=name&o=desc\">Name</a></td>");
	}
	//Reason
	if($sortcat == "reason")
	{
		if($order == "asc")
		{
			print("<td width=35%><a href=\"?p=bans&s=reason&o=desc\">Reason</a></td>");
		}
		else
		{
			print("<td width=35%><a href=\"?p=bans&s=reason&o=asc\">Reason</a></td>");
		}
	}
	else
	{
		print("<td width=35%><a href=\"?p=bans&s=reason&o=desc\">Reason</a></td>");
	}
	//Game Name
	if($sortcat == "gamename")
	{
		if($order == "asc")
		{
			print("<td width=20%><a href=\"?p=bans&s=gamename&o=desc\">Game Name</a></td>");
		}
		else
		{
			print("<td width=20%><a href=\"?p=bans&s=gamename&o=asc\">Game Name</a></td>");
		}
	}
	else
	{
		print("<td width=20%><a href=\"?p=bans&s=gamename&o=desc\">Game Name</a></td>");
	}
	//Date
	if($sortcat == "date")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=bans&s=date&o=desc\">Date</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=bans&s=date&o=asc\">Date</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=bans&s=date&o=desc\">Date</a></td>");
	}
	//Banned by
	if($sortcat == "admin")
	{
		if($order == "asc")
		{
			print("<td width=15%><a href=\"?p=bans&s=admin&o=desc\">Banned by</a></td>");
		}
		else
		{
			print("<td width=15%><a href=\"?p=bans&s=admin&o=asc\">Banned by</a></td>");
		}
	}
	else
	{
		print("<td width=15%><a href=\"?p=bans&s=admin&o=desc\">Banned by</a></td>");
	}
?>
  </tr>
</table>
<div id="datawrapper">
	<table class="table" id="data">
 <?php 
$sql = "SELECT name,  date, gamename, admin, reason FROM `bans` ORDER BY $sortcat $order, name asc";

if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$banname=$row["name"];
		$banby=$row["admin"];
		$bandate=$row["date"];
		$banreason=$row["reason"];
		$gamename=$row["gamename"];
?> 
	<tr class="row">
		
		<td width=15%align=center><?php print $banname;?></td>   
		<td width=35%align=center><?php print $banreason;?></td>	
		<td width=20%align=center><?php print $gamename;?></td>
		<td width=15%align=center><?php print $bandate;?></td>
		<td width=15%align=center><a href="?p=user&u=<?php print $banby; ?>&s=datetime&o=desc"" target="_self"><Strong><?php print $banby;?></strong></a></td>
		
	</tr>
<?php
	}
}
else
{ 
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$banname=$row["name"];
		$banby=$row["admin"];
		$bandate=$row["date"];
		$banreason=$row["reason"];
		$gamename=$row["gamename"];
?> 
	<tr class="row">
		
		<td width=15%align=center><?php print $banname;?></td>   
		<td width=35%align=center><?php print $banreason;?></td>	
		<td width=20%align=center><?php print $gamename;?></td>
		<td width=15%align=center><?php print substr($bandate,0,stripos($bandate," "));?></td>
		<td width=15%align=center><a href="?p=user&u=<?php print $banby; ?>&s=datetime&o=desc"" target="_self"><Strong><?php print $banby;?></strong></a></td>
		
	</tr>
<?php
	}
}
?>

</table>
</div>
