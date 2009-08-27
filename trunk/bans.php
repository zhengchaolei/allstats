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

$sql = "SELECT COUNT( DISTINCT id ) as count from bans";
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
	$pages = ceil($count/$banResultSize);
?>


<div class="header" id="header">
	<table class="rowuh" width=1016px>
		<tr>
			<td width=25%>
				<table class="rowuh" width = 235px style="float:left">
					<h4>
					<tr>
						<td>
						<?php
						if($offset == 'all')
						{
							print "Showing All Bans";
						}
						else
						{
							print "<a href=\"?p=bans&s=".$sortcat."&o=".$order."&n=all\">Show All Bans</a>";
						}
						?>
						</td>
					</tr>
					</h4>
				</table>
			</td>
			<td width=50%>
				<h2>Banned Players:</h2>
			</td>
			<td width=25% class="rowuh">
				<table class="rowuh" width = 235px style="float:right">
				<h4>
				<tr>
					<td colspan=7>
					<?php
					if($offset == 'all')
					{
						print "Show Bans Page:";
					}
					else
					{
						$min = $offset*$banResultSize+1;
						$max = $offset*$banResultSize+$banResultSize;
						if($max > $count)
						{
							$max = $count;
						}
						print "Showing Bans: ".$min." - ".$max;
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
						print "<td width=35px><a href=\"?p=bans&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
						}
					}
					print "<td width=35px><span style=\"color:#ddd;\">></span></td>";
				}
				else
				{
					if($offset > 0)
					{
						print "<td width=35px><a href=\"?p=bans&s=".$sortcat."&o=".$order."&n=".($offset-1)."\"><</a>";
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
									print "<td width=35px><a href=\"?p=bans&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
						}
						if($offset == 1)
						{
							print "<td width=35px><a href=\"?p=bans&s=".$sortcat."&o=".$order."&n=0\">1</a></td>";
							print "<td width=35px><span style=\"color:#ddd;\">2</span></td>";
							for($counter = 3; $counter < 6; $counter++)
							{
								if($counter-1 < $pages)
								{
								print "<td width=35px><a href=\"?p=bans&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
								
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
								print "<td width=35px><a href=\"?p=bans&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
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
									print "<td width=35px><a href=\"?p=bans&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
							print "<td width=35px><span style=\"color:#ddd;\">".($offset+1)."</span>";
							print "<td width=35px><a href=\"?p=bans&s=".$sortcat."&o=".$order."&n=".($offset+1)."\">".($offset+2)."</a></td>";
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
								print "<td width=35px><a href=\"?p=bans&s=".$sortcat."&o=".$order."&n=".($counter-1)."\">".$counter."</a></td>";
							}
						}
					}
					if(($offset+1)*$banResultSize < $count)
					{
						print "<td width=35px><a href=\"?p=bans&s=".$sortcat."&o=".$order."&n=".($offset+1)."\">></a></td>";
					}
					else
					{
						print "<td width=35px><span style=\"color:#ddd;\">></span></td>";
					}
				}
				?>
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
<?php
 if($offset == 'all')
 {
        //Id
        if($sortcat == "id")
        {
                if($order == "asc")
                {
                        print("<td class=\"headercell\" width=70px><a href=\"?p=bans&s=id&o=desc&n=all\">Id</a></td>");
                }
                else
                {
                        print("<td class=\"headercell\" width=70px><a href=\"?p=bans&s=id&o=asc&n=all\">Id</a></td>");
                }
        }
        else
        {
                print("<td class=\"headercell\" width=70px><a href=\"?p=bans&s=id&o=desc&n=all\">Id</a></td>");
        }
	//Name
	if($sortcat == "name")
	{
		if($order == "asc")
		{
			print("<td class=\"headercell\" width=150px><a href=\"?p=bans&s=name&o=desc&n=all\">Name</a></td>");
		}
		else
		{
			print("<td class=\"headercell\" width=150px><a href=\"?p=bans&s=name&o=asc&n=all\">Name</a></td>");
		}
	}
	else
	{
		print("<td class=\"headercell\" width=150px><a href=\"?p=bans&s=name&o=desc&n=all\">Name</a></td>");
	}
	//Reason
	if($sortcat == "reason")
	{
		if($order == "asc")
		{
			print("<td class=\"headercell\" width=360px><a href=\"?p=bans&s=reason&o=desc&n=all\">Reason</a></td>");
		}
		else
		{
			print("<td class=\"headercell\" width=360px><a href=\"?p=bans&s=reason&o=asc&n=all\">Reason</a></td>");
		}
	}
	else
	{
		print("<td class=\"headercell\" width=360px><a href=\"?p=bans&s=reason&o=desc&n=all\">Reason</a></td>");
	}
	//Game Name
	if($sortcat == "gamename")
	{
		if($order == "asc")
		{
			print("<td class=\"headercell\" width=250px><a href=\"?p=bans&s=gamename&o=desc&n=all\">Game Name</a></td>");
		}
		else
		{
			print("<td class=\"headercell\" width=250px><a href=\"?p=bans&s=gamename&o=asc&n=all\">Game Name</a></td>");
		}
	}
	else
	{
		print("<td class=\"headercell\" width=250px><a href=\"?p=bans&s=gamename&o=desc&n=all\">Game Name</a></td>");
	}
	//Date
	if($sortcat == "date")
	{
		if($order == "asc")
		{
			print("<td class=\"headercell\" width=90px><a href=\"?p=bans&s=date&o=desc&n=all\">Date</a></td>");
		}
		else
		{
			print("<td class=\"headercell\" width=90px><a href=\"?p=bans&s=date&o=asc&n=all\">Date</a></td>");
		}
	}
	else
	{
		print("<td class=\"headercell\" width=90px><a href=\"?p=bans&s=date&o=desc&n=all\">Date</a></td>");
	}
	//Banned by
	if($sortcat == "admin")
	{
		if($order == "asc")
		{
			print("<td class=\"headercell\" width=150px><a href=\"?p=bans&s=admin&o=desc&n=all\">Banned by</a></td>");
		}
		else
		{
			print("<td class=\"headercell\" width=150px><a href=\"?p=bans&s=admin&o=asc&n=all\">Banned by</a></td>");
		}
	}
	else
	{
		print("<td class=\"headercell\" width=150px><a href=\"?p=bans&s=admin&o=desc&n=all\">Banned by</a></td>");
	}
}
else
{
        //Id
        if($sortcat == "id")
        {
                if($order == "asc")
                {
                        print("<td class=\"headercell\" width=70px><a href=\"?p=bans&s=id&o=desc&n=0\">Id</a></td>");
                }
                else
                {
                        print("<td class=\"headercell\" width=70px><a href=\"?p=bans&s=id&o=asc&n=0\">Id</a></td>");
                }
        }
        else
        {
                print("<td class=\"headercell\" width=70px><a href=\"?p=bans&s=id&o=desc&n=0\">Id</a></td>");
        }
	//Name
	if($sortcat == "name")
	{
		if($order == "asc")
		{
			print("<td class=\"headercell\" width=150px><a href=\"?p=bans&s=name&o=desc&n=0\">Name</a></td>");
		}
		else
		{
			print("<td class=\"headercell\" width=150px><a href=\"?p=bans&s=name&o=asc&n=0\">Name</a></td>");
		}
	}
	else
	{
		print("<td class=\"headercell\" width=150px><a href=\"?p=bans&s=name&o=desc&n=0\">Name</a></td>");
	}
	//Reason
	if($sortcat == "reason")
	{
		if($order == "asc")
		{
			print("<td class=\"headercell\" width=360px><a href=\"?p=bans&s=reason&o=desc&n=0\">Reason</a></td>");
		}
		else
		{
			print("<td class=\"headercell\" width=360px><a href=\"?p=bans&s=reason&o=asc&n=0\">Reason</a></td>");
		}
	}
	else
	{
		print("<td class=\"headercell\" width=360px><a href=\"?p=bans&s=reason&o=desc&n=0\">Reason</a></td>");
	}
	//Game Name
	if($sortcat == "gamename")
	{
		if($order == "asc")
		{
			print("<td class=\"headercell\" width=250px><a href=\"?p=bans&s=gamename&o=desc&n=0\">Game Name</a></td>");
		}
		else
		{
			print("<td class=\"headercell\" width=250px><a href=\"?p=bans&s=gamename&o=asc&n=0\">Game Name</a></td>");
		}
	}
	else
	{
		print("<td class=\"headercell\" width=250px><a href=\"?p=bans&s=gamename&o=desc&n=0\">Game Name</a></td>");
	}
	//Date
	if($sortcat == "date")
	{
		if($order == "asc")
		{
			print("<td class=\"headercell\" width=90px><a href=\"?p=bans&s=date&o=desc&n=0\">Date</a></td>");
		}
		else
		{
			print("<td class=\"headercell\" width=90px><a href=\"?p=bans&s=date&o=asc&n=0\">Date</a></td>");
		}
	}
	else
	{
		print("<td class=\"headercell\" width=90px><a href=\"?p=bans&s=date&o=desc&n=0\">Date</a></td>");
	}
	//Banned by
	if($sortcat == "admin")
	{
		if($order == "asc")
		{
			print("<td class=\"headercell\" width=150px><a href=\"?p=bans&s=admin&o=desc&n=0\">Banned by</a></td>");
		}
		else
		{
			print("<td class=\"headercell\" width=150px><a href=\"?p=bans&s=admin&o=asc&n=0\">Banned by</a></td>");
		}
	}
	else
	{
		print("<td class=\"headercell\" width=150px><a href=\"?p=bans&s=admin&o=desc&n=0\">Banned by</a></td>");
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
$sql = "SELECT id, name,  date, gamename, admin, reason FROM `bans` ORDER BY $sortcat $order, id desc, name asc";
if($offset!='all')
{
$sql = $sql." LIMIT ".$banResultSize*$offset.", $banResultSize";
}

if($dbType == 'sqlite')
{
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
                $id=$row["id"];
		$banname=$row["name"];
		$banby=$row["admin"];
		$bandate=substr($row["date"],0,10);
		$banreason=$row["reason"];
		$gamename=$row["gamename"];
?> 
	<tr class="row">
		
                <td width=70px><?php print $id;?></td>
                <td width=150px><a style="color:#e56879" href="?p=user&u=<?php print $banname; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $banname; ?></a></td>
		<td width=360px><?php print $banreason;?></td>	
		<td width=250px><?php print $gamename;?></td>
		<td width=90px><?php print $bandate;?></td>
		<td width=150px><a href="?p=user&u=<?php print $banby; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>" target="_self"><?php print $banby;?></a></td>
		
	</tr>
<?php
	}
}
else
{ 
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$id=$row["id"];
		$banname=$row["name"];
		$banby=$row["admin"];
		$bandate=substr($row["date"],0,10);
		$banreason=$row["reason"];
		$gamename=$row["gamename"];
?> 
	<tr class="row">
		
		<td width=70px><?php print $id;?></td>
		<td width=150px><a style="color:#e56879" href="?p=user&u=<?php print $banname; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $banname; ?></a></td>   
		<td width=360px><?php print $banreason;?></td>	
		<td width=250px><?php print $gamename;?></td>
		<td width=90px><?php print $bandate;?></td>
		<td width=150px><a href="?p=user&u=<?php print $banby; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>" target="_self"><?php print $banby;?></a></td>
		
	</tr>
<?php
	}
}
?>
</table>
</div>
</div>
<div id="footer" class="footer">
	<h5>Number of Banned Players: <?php print $count; ?></h5>
</div>
