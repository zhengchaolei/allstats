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
}
else
{
	$offset=mysql_real_escape_string($_GET["n"]);
}

$sql = "SELECT COUNT( DISTINCT id ) as count from admins";

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
$pages = ceil($count/$adminResultSize);
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
							print "Showing All Admins";
						}
						else
						{
							print "<a href=\"?p=admins&n=all\"><strong>Show All Admins</strong></a>";
						}
						?>
						</td>
					</tr>
				</table>
			</td>
			<td width=50%>
				<h2>Administrators:</h2>
			</td>
			<td width=25% class="rowuh">
				<table class="rowuh" width = 235px style="float:right">
				<h4>
				<tr>
					<td colspan=7>
					<?php
					if($offset == 'all')
					{
						print "Show Admins Page:";
					}
					else
					{
						$min = $offset*$adminResultSize+1;
						$max = $offset*$adminResultSize+$adminResultSize;
						if($max > $count)
						{
							$max = $count;
						}
						print "Showing Admins: ".$min." - ".$max;
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
						print "<td width=35px><a href=\"?p=admins&n=".($counter-1)."\">".$counter."</a></td>";
						}
					}
					print "<td width=35px><span style=\"color:#ddd;\">></span></td>";
				}
				else
				{
					if($offset > 0)
					{
						print "<td width=35px><a href=\"?p=admins&n=".($offset-1)."\"><strong><</strong></a>";
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
									print "<td width=35px><a href=\"?p=admins&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
						}
						if($offset == 1)
						{
							print "<td width=35px><a href=\"?p=admins&n=0\">1</a></td>";
							print "<td width=35px><span style=\"color:#ddd;\">2</span></td>";
							for($counter = 3; $counter < 6; $counter++)
							{
								if($counter-1 < $pages)
								{
								print "<td width=35px><a href=\"?p=admins&n=".($counter-1)."\">".$counter."</a></td>";
								
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
								print "<td width=35px><a href=\"?p=admins&n=".($counter-1)."\">".$counter."</a></td>";
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
									print "<td width=35px><a href=\"?p=admins&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
							print "<td width=35px><span style=\"color:#ddd;\">".($offset+1)."</span>";
							print "<td width=35px><a href=\"?p=admins&n=".($offset+1)."\">".($offset+2)."</a></td>";
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
								print "<td width=35px><a href=\"?p=admins&n=".($counter-1)."\">".$counter."</a></td>";
							}
						}
					}
					if(($offset+1)*$adminResultSize < $count)
					{
						print "<td width=35px><a href=\"?p=admins&n=".($offset+1)."\"><strong>></strong></a></td>";
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
				<td class="headercell" width=508px align="center"><h5>Head Administrator: <a href="?p=user&u=<?php print $rootAdmin; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $rootAdmin; ?></a></h5></td>   
				<td class="headercell" width=508px align="center"><h5>Number of Administrators: <?php print $count; ?> </h5></td>   
			</tr>
		</table>
	</div>
	<div id="datawrapper">
		<table class="table" id="data">
<?php 
$sql = "SELECT Distinct(name), server FROM `admins` ORDER BY name asc ";

if($offset!='all')
{
$sql = $sql." LIMIT ".$adminResultSize*$offset.", $adminResultSize";
}
if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
	$name=$row["name"];
	?> 
	<tr class="row" >
		<td align=center colspan=3><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $name; ?></a></td>   	
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
		<td align=center colspan=3><a href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $name; ?></a></td>   	
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
</div>
<div id="footer" class="footer">
</div>