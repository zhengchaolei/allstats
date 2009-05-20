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
?>

<?php
$sql = "SELECT COUNT( DISTINCT id ) as totadmins from admins";

if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$totadmins=$row["totadmins"];
	?>
	<table class="table" id="theader">
		<tr class="rowuh">
		<td colspan=2><h2>Administrators:</h2></td>
		</tr>

	  <tr class="tableheader">
		<td width=50% align="center"><h5>Head Administrator: <a href="?p=user&u=<?php print $rootAdmin; ?>&s=datetime&o=desc"><?php print $rootAdmin; ?></a></h5></td>   
		<td widht=50%align="center"><h5>Number of Administrators: <?php print $totadmins; ?> </h5></td>   
	 </tr>
	  <tr height=10px>
	  </tr>
	</table>
	<?php
	}
}
else
{
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$totadmins=$row["totadmins"];
	?>
	<table class="table" id="theader">
		<tr class="rowuh">
		<td colspan=2><h2>Administrators:</h2></td>
		</tr>

	  <tr class="tableheader">
		<td width=50% align="center"><h5>Head Administrator: <a href="?p=user&u=<?php print $rootAdmin; ?>&s=datetime&o=desc"><?php print $rootAdmin; ?></a></h5></td>   
		<td widht=50%align="center"><h5>Number of Administrators: <?php print $totadmins; ?> </h5></td>   
	 </tr>
	  <tr height=10px>
	  </tr>
	</table>
	<?php
	}
	mysql_free_result($result);
}
?>

<div id="datawrapper">
	<table class="table" id="data">
<?php 
$sql = "SELECT name, server FROM `admins` ORDER BY name asc ";

if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
	$name=$row["name"];
	?> 
	<tr class="row" >
		<td align=center colspan=3><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc"><?php print $name; ?></a></td>   	
	</tr>
<?php
	}
}
else
{	
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$name=$row["name"];
		
	?> 
	<tr class="row" >
		<td align=center colspan=3><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc"><?php print $name; ?></a></td>   	
	</tr>
	<?php
	}
	mysql_free_result($result);
}
?>
 <tr height=10px>
  </tr>


<tr class="rowuh">
<td colspan=3><h3>General Commands:</h3><br></td>
</tr>

<tr class="rowuh">
<td align=right width=30%>!stats [name]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Display basic player statistics, optionally add [name] to display statistics for another player</td>
</tr>
<tr class="rowuh">
<td align=right width=30%>!statsdota [name]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Display DotA player statistics, optionally add [name] to display statistics for another player</td>
</tr>

<tr class="rowuh">
<td colspan=3><br><h3>Admin Commands:</h3><br></td>
</tr>

<tr class="rowuh">
<td align=right width=30%>!priv [name]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Host a private game</td>
</tr>
<tr class="rowuh">
<td align=right width=30%>!pub [name]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Host a public game</td>
</tr>
<tr class="rowuh">
<td align=right width=30%>!unhost</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Unhost the current game</td>
</tr>
<tr class="rowuh">
<td align=right width=30%>!swap [n1] [n2]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Swap slots</td>
</tr>
<tr class="rowuh">
<td align=right width=30%>!start [force]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Start game, optionally add [force] to skip checks</td>
</tr>
<tr class="rowuh">
<td align=right width=30%>!ping [number]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Ping players, optionally add [number] to kick players with ping above [number]</td>
</tr>
<tr class="rowuh">
<td align=right width=30%>!close [number]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Close slot</td>
</tr>
<tr class="rowuh">
<td align=right width=30%>!open [number]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Open slot</td>
</tr>
<tr class="rowuh">
<td align=right width=30%>!say [text]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Send [text] to battle.net as a chat command.</td>
</tr>
<tr class="rowuh">
<td colspan=3><br></td>
</tr>
<tr class="rowuh">
<td align=right width=30%>!ban [name] [reason]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Permabans [name] for [reason].</td>
</tr>
<tr class="rowuh">
<td align=right width=30%>!kick  [partial name]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Kick [partial name] from game.</td>
</tr>
<tr class="rowuh">
<td align=right width=30%>!latency [number]</td>
<td align=center width=10%>|</td>
<td align=left width=60%>Set in game latency to [number]. Set higher if people are spiking, lower if people have delay.</td>
</tr>

</table>
</div>
