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
?>
<div class="header" id="header">
</div>
<div class="pageholder" id="pageholder">
<center>
	<div id="theader">
	<table class="tableheader" id="tableheader">
  <tr>
	<td class="rowuh">
			<br>
			<br>
			<br>
			<h4>
			Welcome to the <?php print $botName; ?> Stats Page
			</h4>
			<br>
			<br>
			<br>
	</td>
  </tr>
  </table>
	</div>
	<div id="datawrapper">
		<table class="table" id="data">
	<tr class="rowuh">
		<td>
			<br>
			<br>
			<br>
			Here you can find information about all players who have played games hosted by <?php print $botName; ?> 
			<br>
			<br>
			<br>
			Please choose from menu above.
<?php

if($dbType == 'sqlite')
{
	$sql = "select count(*) as count from sqlite_master WHERE tbl_name = 'originals' and type = 'table'";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$count=$row["count"];
	}
}
else
{
	$sql = "SELECT count(*) as count FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='originals' and TABLE_SCHEMA='$databasename'";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$count=$row["count"];
	}
	mysql_free_result($result);
}
if ($count == 0) 
{
?>
			<br>
			<br>
			<br>
			<div style="color:red">WARNING: "originals" table not found. Please run allstats sql setup script first!</div>
<?php
}

if($dbType == 'sqlite')
{
	$sql = "select count(*) as count from sqlite_master WHERE (tbl_name like 'dota%' or tbl_name like 'game%') and type = 'table'";
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$count=$row["count"];
	}
}
else
{
	$sql = "SELECT count(*) as count FROM INFORMATION_SCHEMA.TABLES WHERE (TABLE_NAME like 'dota%' or TABLE_NAME like 'game%') and TABLE_SCHEMA='$databasename'";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$count=$row["count"];
	}
	mysql_free_result($result);
}
if ($count < 4) 
{
?>
			<br>
			<br>
			<br>
			<div style="color:red">ERROR: ghost tables not found. Please check your configuration</div>
<?php
}
?>


		</td>
	</tr>
</table>
</div>
</center>
</div>
<div id="footer" class="footer">
</div>
